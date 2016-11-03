<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use OxidEsales\Eshop\Exception;
use OxidEsales\Eshop\Core\Registry;

class VirtualClassMapGenerator
{

    /**
     * @var string The key for the name of the file with a class in it.
     */
    const ATTRIBUTE_NAME_FILE = 'file';

    /**
     * @var string The key for the complete class name.
     */
    const ATTRIBUTE_NAME_CLASS = 'fullclassname';

    /**
     * @var string The key for the complete extends class name.
     */
    const ATTRIBUTE_NAME_EXTENDS = 'fullextendsname';

    /**
     * @var string The key for the attribute, if the node is a leave (classes marked with internal are no leave).
     */
    const ATTRIBUTE_NAME_LEAVE = 'isleave';

    /**
     *
     */
    public function generateAll()
    {
        $baseDir = Registry::getConfig()->getConfigParam('sShopDir');

        $communitySourcePath = $baseDir . '';
        $professionalSourcePath = $baseDir . '/../vendor/oxid-esales/oxideshop-pe';
        $enterpriseSourcePath = $baseDir . '/../vendor/oxid-esales/oxideshop-ee';

        $communityGeneratedFile = $baseDir . '/Core/VirtualNameSpaceClassMap.php';
        $professionalGeneratedFile = $baseDir . '/../vendor/oxid-esales/oxideshop-pe/Core/VirtualNameSpaceClassMap.php';
        $enterpriseGeneratedFile = $baseDir . '/../vendor/oxid-esales/oxideshop-ee/Core/VirtualNameSpaceClassMap.php';

        $this->generate($communitySourcePath, $communityGeneratedFile);
        $this->generate($professionalSourcePath, $professionalGeneratedFile, 'Professional');
        $this->generate($enterpriseSourcePath, $enterpriseGeneratedFile, 'Enterprise');
    }

    /**
     * Generate the virtual class map for the given source folder in the given file name.
     *
     * @param string $sourcePath        The source directory, which should be reflected to the virtual class map.
     * @param string $generatedFileName The name of the file, in which we want to write the virtual class map.
     */
    public function generate($sourcePath, $generatedFileName, $edition = 'Community')
    {
        $graph = $this->createGraph($sourcePath, $edition);

        if (file_exists($generatedFileName)) {
            unlink($generatedFileName);
        }
        $file = fopen($generatedFileName, 'w+');

        $arrayContent = "";
        foreach ($graph->getVertices() as $vertex) {
            $classNameEdition = $this->createExtendsClassName($edition, $vertex);
            $className = $this->createClassName($vertex);

            if ($this->shouldIncludeInGeneration($className)) {
                $arrayContent .= "\t\t\t'$className' => '$classNameEdition',\n";
            }
        }

        $license = file_get_contents(__DIR__ . "/../templates/license/$edition.php");
        $template = file_get_contents(__DIR__ . '/../templates/VirtualNamespaceTemplate.php');

        $content = str_replace('/* ADD_OVERRIDABLE_MAP_HERE */', $arrayContent, $template);
        $content = str_replace('/* ADD_LICENSE_HERE */', $license, $content);
        $content = str_replace('/* ADD_EDITION_HERE */', $edition, $content);
        $content = str_replace("\t", '    ', $content);

        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Create the inheritance graph of the classes in the given path.
     *
     * @param string $path The path for which we want the inheritance graph of classes want.
     *
     * @return Graph The complete inheritance graph, in which the
     */
    public function createGraph($path, $edition = 'Community')
    {
        $graph = $this->createNodes($path, $edition);
        $graph = $this->createEdges($graph);
        $graph = $this->markLeaves($graph, $path);

        return $graph;
    }

    /**
     * Create a graph with nodes, which represent the classes in a given path.
     *
     * @param string $path The path for which we want to create the nodes.
     *
     * @return Graph The result graph (at this point only filled with node and no edges).
     */
    public function createNodes($path, $edition = 'Community')
    {
        $classes = $this->getClasses($path);
        $graph = new Graph();

        foreach ($classes as $class) {
            try {
                $virtualClassName = str_replace('OxidEsales\Eshop' . $edition, 'OxidEsales\Eshop', $class[self::ATTRIBUTE_NAME_CLASS]);

                $vertex = $graph->createVertex($virtualClassName);
                $vertex->setAttribute(self::ATTRIBUTE_NAME_EXTENDS, $class[self::ATTRIBUTE_NAME_EXTENDS]);
                $vertex->setAttribute(self::ATTRIBUTE_NAME_FILE, $class[self::ATTRIBUTE_NAME_FILE]);
            } catch (Exception $exception) {
                //@todo: handle exception - not sure, what to do
            }
        }

        return $graph;
    }

    /**
     * Add all edges to the inheritance graph.
     *
     * @param Graph $graph The graph, only consisting of nodes.
     *
     * @return Graph The graph with the needed edges.
     */
    public function createEdges($graph)
    {
        foreach ($graph->getVertices() as $vertex) {
            $extends = $vertex->getAttribute(self::ATTRIBUTE_NAME_EXTENDS);

            if (!empty($extends)) {
                try {
                    if ($graph->hasVertex($extends)) {
                        $from = $graph->getVertex($extends);
                        $from->createEdgeTo($vertex);
                    } else {
                        // @todo: Node with name '$extends' already exists! what should be done?
                    }
                } catch (Exception $exception) {
                    // @todo: handle exception - not sure, what to do
                }
            }
        }

        return $graph;
    }

    /**
     * Mark all leaves (normal leaves, which are not marked as @internal) of the given graph.
     *
     * @param Graph $graph The inheritance graph, with all edges.
     *
     * @return Graph The graph with the leaves marked as needed.
     */
    public function markLeaves($graph, $sourcePath)
    {
        $filesMarkedInternal = $this->getFilesMarkedInternal($sourcePath);

        foreach ($graph->getVertices() as $vertex) {
            $edgesOut = $vertex->getEdgesOut();

            if ($edgesOut->isEmpty()) {
                $vertex->setAttribute(self::ATTRIBUTE_NAME_LEAVE, true);
            } else {
                $vertex->setAttribute(self::ATTRIBUTE_NAME_LEAVE, false);
            }

            if (in_array($vertex->getAttribute(self::ATTRIBUTE_NAME_FILE), $filesMarkedInternal)) {
                $vertex->setAttribute(self::ATTRIBUTE_NAME_LEAVE, false);
            }
        }

        return $graph;
    }

    /**
     * Get all files marked as internal from the given directory and its sub directories.
     *
     * Note: if someone tries to insert anything else than a directory or a file, the result is an empty array.
     *
     * @param string $path The path to the directory we want to have all the internal marked files from.
     *
     * @return array All file names of files below the given directory.
     */
    public function getFilesMarkedInternal($path)
    {
        $result = array();

        $path = realpath($path);
        if (false === $path) {
            return $result;
        }

        $command = "grep -lir '* @internal' " . $path;
        $output = shell_exec($command);

        $result = $this->addFilesToResult($output, $result);

        return $result;
    }

    /**
     * Get all classes (names and file names) from the given path.
     *
     * @param string $path The path to the directory from which we want all classes.
     *
     * @return array The classes from the given directory.
     */
    public function getClasses($path)
    {
        $classes = array();

        $path = realpath($path);

        if (false === $path) {
            return $classes;
        }

        $command = "grep -Pr '^(abstract )?class .*(( extends)|( implements))?' " . $path;
        $output = shell_exec($command);

        $classes = $this->addFilesToResult($output, $classes, true);

        return $classes;
    }

    /**
     * Read the given shell output to a files array.
     *
     * @param string $output          The shell output we want to read.
     * @param array  $result          The result we want to append the files to.
     * @param bool   $scanOccurrences Should the file name and the class name be scanned into the file?
     *
     * @return array The found files. Either a flat array or a richer, associative array.
     */
    protected function addFilesToResult($output, $result, $scanOccurrences = false)
    {
        $files = explode("\n", $output);

        foreach ($files as $file) {
            if ('' === $file) {
                continue;
            }

            if ($scanOccurrences) {
                $parts = explode(':', $file);

                $fileName = $parts[0];

                $namespace = $this->extractNamespace($fileName);
                $className = $this->extractClassName($parts[1]);
                $classExtendsName = $this->extractExtendsClassName($parts[1]);
                $fullClassName = $this->combineFullClassName($namespace, $className);
                $fullExtendsName = $this->combineFullExtendsName($namespace, $classExtendsName);

                $result[] = array(
                    self::ATTRIBUTE_NAME_FILE => $fileName,
                    self::ATTRIBUTE_NAME_CLASS => $fullClassName,
                    self::ATTRIBUTE_NAME_EXTENDS => $fullExtendsName
                );
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * Find the namespace in the given file and give it back.
     *
     * @param string $fileName The file to search in for a namespace.
     *
     * @return string The namespace in the given file, if there is one.
     */
    protected function extractNamespace($fileName)
    {
        $namespace = '';

        $contents = file_get_contents($fileName);
        $lines = explode("\n", $contents);

        $found = false;

        foreach ($lines as $line) {
            if ($found) {
                continue;
            }
            if ('namespace' === substr($line, 0, 9)) {
                $line = str_replace('namespace', '', $line);
                $line = str_replace(' ', '', $line);
                $line = str_replace(';', '', $line);
                $namespace = str_replace("\t", '', $line);

                $found = true;
            }
        }

        if ('\\' !== $namespace[0]) {
            $namespace = '\\' . $namespace;
        }

        return $namespace;
    }

    /**
     * Extract the class name out of a given grep line.
     *
     * @param string $line The line we want the class name from.
     *
     * @return string The class name.
     */
    protected function extractClassName($line)
    {
        $line = str_replace('abstract ', '', $line);
        $parts = explode(' ', $line);

        return $parts[1];
    }

    /**
     * Extract the extends class name from the given line.
     *
     * @param string $line The line we want the extends from.
     *
     * @return string The extended class name.
     */
    protected function extractExtendsClassName($line)
    {
        $parts = explode(' extends ', $line);

        $extends = str_replace(';', '', $parts[1]);

        if (0 <= strpos($extends, ' implements ')) {
            $parts = explode(' implements ', $extends);
            $extends = $parts[0];
        }

        return $extends;
    }

    /**
     * Combine the namespace and the class name to the complete class name of the class, which is extended.
     *
     * @param string $namespace The actual namespace.
     * @param string $className The name of the class we want to combine.
     *
     * @return string The complete name of the class we extend from.
     */
    protected function combineFullExtendsName($namespace, $className)
    {
        if (0 <= strpos($className, "\\")) {
            $namespace = '';
        }

        $fullClassName = $this->combineFullClassName($namespace, $className);
        $fullClassName = str_replace('\\\\', '\\', $fullClassName);

        if ('\\' === $fullClassName) {
            $fullClassName = str_replace('\\', '', $fullClassName);
        }
        if ("\\$namespace\\" === $fullClassName) {
            $fullClassName = '';
        }
        if ("$namespace\\" === $fullClassName) {
            $fullClassName = '';
        }

        return $fullClassName;
    }

    /**
     * Combine the namespace and the class name to the complete class name.
     *
     * @param string $namespace The namespace of the class.
     * @param string $className The actual name of the class.
     *
     * @return string The complete class name.
     */
    protected function combineFullClassName($namespace, $className)
    {
        $fullClassName = $namespace . "\\" . $className;

        $fullClassName = str_replace('\\\\', '\\', $fullClassName);

        return $fullClassName;
    }

    /**
     * Create the extends class name.
     *
     * @param string $edition The edition we generate for.
     * @param Vertex $vertex  The node in the inheritance graph we want to create the extends for.
     *
     * @return string
     */
    protected function createExtendsClassName($edition, $vertex)
    {
        $extendsClassName = str_replace('Eshop', 'Eshop' . $edition, $vertex->getId());
        $extendsClassName = $this->deleteFirstChar($extendsClassName);

        return $extendsClassName;
    }

    /**
     * Create the name of the generated class.
     *
     * @param Vertex $vertex The node of the inheritance graph to
     *
     * @return string The class name to write to the generated file.
     */
    protected function createClassName($vertex)
    {
        $className = $vertex->getId();
        $className = $this->deleteFirstChar($className);

        return $className;
    }

    /**
     * Remove the first char of the given string.
     *
     * @param string $subject The string which should be without the first letter.
     *
     * @return string The given string without the first letter.
     */
    protected function deleteFirstChar($subject)
    {
        return substr($subject, 1, strlen($subject));
    }

    /**
     * Should the given class be included into the generated file?
     *
     * @param string $className The complete name of the class.
     *
     * @return bool Should the given class be included into the generated file?
     */
    protected function shouldIncludeInGeneration($className)
    {
        $posTestNamespace = strpos($className, 'OxidEsales\Eshop\Tests\\');
        $isNoTest = (false === $posTestNamespace);
        $hasNamespace = (false !== strpos($className, "\\"));

        return $isNoTest && $hasNamespace;
    }
}
