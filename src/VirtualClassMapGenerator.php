<?php
/**
 * This file is part of OXID eSales eShop Virtual Namespace ClassMap Generator
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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */
class VirtualClassMapGenerator
{

    /**
     * Generate virtual namespace class maps for OXID eSales eshop Community, Professional and Enterprise edition.
     */
    public function generateAll()
    {
        $communitySourcePath = OX_BASE_PATH;
        $professionalSourcePath = VENDOR_PATH . 'oxid-esales/oxideshop-pe';
        $enterpriseSourcePath = VENDOR_PATH . 'oxid-esales/oxideshop-ee';

        $communityGeneratedFile = OX_BASE_PATH . 'Core/Autoload/VirtualNameSpaceClassMap.php';
        $professionalGeneratedFile = VENDOR_PATH . 'oxid-esales/oxideshop-pe/Core/Autoload/VirtualNameSpaceClassMap.php';
        $enterpriseGeneratedFile = VENDOR_PATH . 'oxid-esales/oxideshop-ee/Core/Autoload/VirtualNameSpaceClassMap.php';

        $this->generate($communitySourcePath, $communityGeneratedFile, 'Community');
        $this->generate($professionalSourcePath, $professionalGeneratedFile, 'Professional');
        $this->generate($enterpriseSourcePath, $enterpriseGeneratedFile, 'Enterprise');
    }

    /**
     * Generate the virtual class map for the given source folder in the given file name.
     *
     * @param string $sourcePath        The source directory, which should be reflected to the virtual class map.
     * @param string $generatedFileName The name of the file, in which we want to write the virtual class map.
     * @param string $edition
     *
     * @throws Exception
     */
    public function generate($sourcePath, $generatedFileName, $edition)
    {

        $excludedClasses = [
            '\OxidEsales\EshopCommunity\Application\Controller\Admin\ShopCountries', // Excluded, as this file contains a namespace, but no class
            '\OxidEsales\EshopCommunity\Core\Autoload\AliasAutoload', // Excluded auto load
            '\OxidEsales\EshopCommunity\Core\Autoload\ModuleAutoload', // Excluded auto load
        ];
        $tabs = '    ';
        /** Collect classes, that define namespaces */
        if (is_dir($sourcePath)) {
            $iterator = $this->getDirectoryIterator($sourcePath, $edition);
            $classes = $this->getNameSpacedClasses($iterator);
            sort($classes);

            $map = '';
            foreach ($classes as $class) {
                if (!in_array($class, $excludedClasses)) {
                    $classInVirtualNamespace = ltrim(str_replace('Eshop' . $edition, 'Eshop', $class), '\\');
                    $map .= "$tabs$tabs$tabs'$classInVirtualNamespace' => $class::class," . PHP_EOL;
                }
            }
            if (in_array($edition,['Professional','Enterprise'])) {
                $mapMerge = '$classMap = array_merge(parent::getClassMap(), $classMap);';
            } else {
                $mapMerge = '';
            }

            $license = file_get_contents(__DIR__ . "/../templates/license/$edition.php");
            $template = file_get_contents(__DIR__ . '/../templates/VirtualNamespaceTemplate.php');

            $template = str_replace('/* ADD_LICENSE_HERE */', $license, $template);
            $template = str_replace('/* ADD_EDITION_HERE */', $edition, $template);
            $template = str_replace('/* ADD_EXTENDS_PARENT_EDITION_HERE */', $this->getClassExtends($edition), $template);
            $template = str_replace('/* ADD_MAP_HERE */', rtrim($map), $template);
            $template = str_replace('/* ADD_MAP_MERGE HERE */', $mapMerge, $template);

            if (!file_put_contents($generatedFileName, $template)) {
                throw new \Exception('Could not write content to file ' . $generatedFileName);
            };
        } else {
            echo 'Warning: source directory ' . $sourcePath . ' does not exists' . PHP_EOL;
        }
    }

    /**
     * Return the class extends part of the classmap
     * @param $edition Current edition
     */
    public function getClassExtends($edition) {
        $extendsString = '';
        if ('Enterprise' === $edition) {
            $extendsString = " extends \\OxidEsales\\EshopProfessional\\Core\\Autoload\\VirtualNameSpaceClassMap";
        }
        if ('Professional' === $edition) {
            $extendsString = " extends \\OxidEsales\\EshopCommunity\\Core\\Autoload\\VirtualNameSpaceClassMap";
        }

        return $extendsString;
    }

    /**
     * Get all classes within namespaces for a given recursive directory iterator.
     * This method assumes, that if a file defines a namespace, it also defines a class within this namespace according
     * the PSR-4 auto loading rules.
     *
     * @param SplFileInfo|RecursiveIteratorIterator|RecursiveDirectoryIterator $iterator
     *
     * @return array
     */
    public function getNameSpacedClasses(RecursiveIteratorIterator $iterator)
    {
        $namespaceClasses = [];
        while ($iterator->valid()) {
            if (!$iterator->isDot()) {
                $fileContent = file($iterator->key(), FILE_SKIP_EMPTY_LINES);
                $matches = preg_grep("/^([[:space:]])?namespace([[:space:]])+\\OxidEsales/i", $fileContent);
                if ($matches && $match = reset($matches)) {
                    $search = ['namespace', ';', '{'];
                    $replace = ['', '', ''];
                    $namespace = trim(str_replace($search, $replace, $match));
                    $namespaceClasses[] = '\\' . $namespace . '\\' . $iterator->getBasename('.php');
                }
            }

            $iterator->next();
        }

        return $namespaceClasses;
    }

    /**
     * Return a filtered recursive directory iterator.
     * Filters are defined as callback for CE edition (self::getFilterCommunity) and PE/EE edition (self::getFilterProfessional)
     *
     * @param string $sourcePath A given source path
     * @param string $edition    A given edition. Must be one of 'Community', 'Professional', 'Enterprise'
     *
     * @return RecursiveIteratorIterator|SplFileInfo
     */
    public function getDirectoryIterator($sourcePath, $edition)
    {
        $allowedEditions = ['Community', 'Professional', 'Enterprise'];
        if (!in_array($edition, $allowedEditions)) {
            throw new \InvalidArgumentException('Parameter edition must be one of ' . implode(',', $allowedEditions));
        }
        $filter = $edition == 'Community' ? [$this, 'getFilterCommunity'] : [$this, 'getFilterProfessional'];

        $directory = new \RecursiveDirectoryIterator($sourcePath);
        $filter = new \RecursiveCallbackFilterIterator(
            $directory, $filter
        );


        /** @var SplFileInfo $iterator */
        $iterator = new \RecursiveIteratorIterator($filter);
        $iterator->rewind();

        return $iterator;
    }

    /**
     * A callback for the recursive directory iterator filter applied to the community edition directory
     *
     * @param SplFileInfo $current
     * @param             $key
     * @param             $iterator
     *
     * @return bool
     */
    public function getFilterCommunity(SplFileInfo $current, $key, $iterator)
    {
        /**
         * In- and exclude directories to search in.
         * The order is include -> exclude. So you can include a directory, which automatically includes all
         * subdirectories and then exclude certain included subdirectories.
         */
        /** @var array $includedDirectories Directories and its subdirectories, which are included in the search */
        $includedDirectories = ['Application', 'Core'];
        /** @var array $excludedDirectories Directories and its subdirectories, which are excluded from the search */
        $excludedDirectories = ['views'];


        if ($current->isDir()) {
            $skip = true;
            $directoryPath = $current->getRealPath();
            foreach ($includedDirectories as $allowedDirectory) {
                if ($skip == false) {
                    break;
                }
                if (strpos($directoryPath, $allowedDirectory)) {
                    $skip = false;
                }
            }

            if ($skip == false) {
                foreach ($excludedDirectories as $forbiddenDirectory) {
                    if ($skip == true) {
                        break;
                    }
                    if (strpos($directoryPath, $forbiddenDirectory)) {
                        $skip = true;
                    }
                }
            }

            return $skip === false;
        } else {
            $filePath = $current->getRealPath();
            $skip = strpos($filePath, '/vendor/') !== false;

            if (!$skip) {
                $fileName = $current->getFilename();
                $skip = strpos($fileName, '.php') == false;
            }

            return $skip === false;
        }
    }

    /**
     * A callback for the recursive directory iterator filter applied to the professional and enterprise edition directory
     *
     * @param SplFileInfo $current
     * @param             $key
     * @param             $iterator
     *
     * @return bool
     */
    public function getFilterProfessional(SplFileInfo $current, $key, $iterator)
    {
        /**
         * In- and exclude directories to search in.
         * The order is include -> exclude. So you can include a directory, which automatically  includes all
         * subdirectories and then exclude certain included subdirectories.
         */
        $includedDirectories = ['Application', 'Core'];
        $excludedDirectories = ['views'];


        if ($current->isDir()) {
            $skip = true;
            $directoryPath = $current->getRealPath();
            foreach ($includedDirectories as $allowedDirectory) {
                if ($skip == false) {
                    break;
                }
                if (strpos($directoryPath, $allowedDirectory)) {
                    $skip = false;
                }
            }

            if ($skip == false) {
                foreach ($excludedDirectories as $forbiddenDirectory) {
                    if ($skip == true) {
                        break;
                    }
                    if (strpos($directoryPath, $forbiddenDirectory)) {
                        $skip = true;
                    }
                }
            }

            return $skip === false;
        } else {
            $fileName = $current->getFilename();
            $skip = strpos($fileName, '.php') == false;

            return $skip === false;
        }
    }
}
