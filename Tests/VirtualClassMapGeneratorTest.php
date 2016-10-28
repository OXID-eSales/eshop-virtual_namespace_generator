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
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

use OxidEsales\Eshop\VirtualClassMapGenerator;

require_once __DIR__ . '/../src/VirtualClassMapGenerator.php';

/**
 * Tests for the VirtualClassMapGenerator.
 */
class VirtualClassMapGeneratorTest extends \OxidTestCase
{

    /**
     * @var VirtualClassMapGenerator The object under test.
     */
    protected $generator = null;

    public function setUp()
    {
        parent::setUp();

        $this->generator = new VirtualClassMapGenerator();
    }

    /**
     * Test, that the method getFilesMarkedInternal works correct with an empty folder.
     */
    public function testGetFilesMarkedInternalWithNonExistentDirectory()
    {
        $fileNamesMarkedInternal = $this->generator->getFilesMarkedInternal(__DIR__ . '/testData/NotExisting');

        $expected = array();
        $this->assertSame($expected, $fileNamesMarkedInternal);
    }

    /**
     * Test, that the method getFilesMarkedInternal works correct with a file.
     */
    public function testGetFilesMarkedInternalWithFile()
    {
        $fileNamesMarkedInternal = $this->generator->getFilesMarkedInternal(__DIR__ . '/testData/internal/internalPresent.php');

        $expected = array(__DIR__ . '/testData/internal/internalPresent.php');
        $this->assertSame($expected, $fileNamesMarkedInternal);
    }

    /**
     * Test, that the method getFilesMarkedInternal works correct with a possible security exploit.
     */
    public function testGetFilesMarkedInternalWithSecurity()
    {
        $fileNamesMarkedInternal = $this->generator->getFilesMarkedInternal(__DIR__ . '/testData/internal/internalPresent.php;ls -larst');

        $expected = array();
        $this->assertSame($expected, $fileNamesMarkedInternal);
    }

    /**
     * Test, that the method getFilesMarkedInternal works correct with an empty folder.
     */
    public function testGetFilesMarkedInternalWithEmptyDirectory()
    {
        $fileNamesMarkedInternal = $this->generator->getFilesMarkedInternal(__DIR__ . '/testData/internal/emptyDir');

        $expected = array();
        $this->assertSame($expected, $fileNamesMarkedInternal);
    }

    /**
     * Test, that the method getFilesMarkedInternal works correct with multiple subfolders.
     */
    public function testGetFilesMarkedInternalWithMultipleSubDirectories()
    {
        $fileNamesMarkedInternal = $this->generator->getFilesMarkedInternal(__DIR__ . '/testData/internal/');

        $expected = array(
            __DIR__ . '/testData/internal/exampleDir/deepDir/internalPresent.php',
            __DIR__ . '/testData/internal/exampleDir/internalPresent.php',
            __DIR__ . '/testData/internal/internalPresent.php',
        );
        $this->assertSame($expected, $fileNamesMarkedInternal);
    }

    /**
     * Test that the method getClasses works correct with a non existing directory.
     */
    public function testGetClassesWithNotExistingDirectory()
    {
        $classes = $this->generator->getClasses(__DIR__ . '/testData/NotExisting');

        $expected = array();
        $this->assertSame($expected, $classes);
    }

    /**
     * Test that the method getClasses works correct with a file without a class.
     */
    public function testGetClassesWithFileWithoutClass()
    {
        $classes = $this->generator->getClasses(__DIR__ . '/testData/normal/withoutClass.php');

        $expected = array();
        $this->assertSame($expected, $classes);
    }

    /**
     * Test that the method getClasses works correct with a possible security exploit.
     */
    public function testGetClassesWithSecurity()
    {
        $classes = $this->generator->getClasses(__DIR__ . '/testData/;ls -larst');

        $expected = array();
        $this->assertSame($expected, $classes);
    }

    /**
     * Test that the method getClasses works correct with an empty directory.
     */
    public function testGetClassesWithEmptyDirectory()
    {
        $classes = $this->generator->getClasses(__DIR__ . '/testData/emptyDir');

        $expected = array();
        $this->assertSame($expected, $classes);
    }

    /**
     * Test that the method getClasses works correct with an empty directory.
     */
    public function testGetClassesWithMultipleSubDirectories()
    {
        $classes = $this->generator->getClasses(__DIR__ . '/testData/normal');

        $expectedClasses = array(
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/classWithExtend.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class0',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => '\ABC\Just_A_Pure_Class'
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/classWithImplement.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class1',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/classWithImplements.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class2',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/classWithExtend.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class3',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => '\ABC\Just_A_Pure_Class'
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/classWithExtendImplements.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\CDE\Just_A_Class4',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => '\ABC\Just_A_Pure_Class'
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/classWithImplement.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class5',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/classWithImplementExtends.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class6',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => '\ABC\Just_A_Pure_Class'
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/classWithImplements.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\Just_A_Class7',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/exampleDir/pureClass.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\ABC\Eshop\Just_A_Pure_Class',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
            array(
                VirtualClassMapGenerator::ATTRIBUTE_NAME_FILE    => __DIR__ . '/testData/normal/pureClass.php',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_CLASS   => '\ABC\Just_A_Pure_Class',
                VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS => ''
            ),
        );
        $this->assertArraysContainSameItems($expectedClasses, $classes);
    }

    /**
     * Test, that the method createNodes gives back an empty graph for an empty directory.
     */
    public function testCreateNodesWithEmptyDir()
    {
        $graph = $this->generator->createNodes(__DIR__ . '/testData/emptyDir');

        $this->assertSame('Fhaculty\Graph\Graph', get_class($graph));
        $this->assertEmpty($graph->getVertices());

        return $graph;
    }

    /**
     * Test, that the method createNodes gives back a non empty graph for a non empty directory.
     */
    public function testCreateNodesWithFilledDir()
    {
        $graph = $this->generator->createNodes(__DIR__ . '/testData/normal');

        $this->assertSame('Fhaculty\Graph\Graph', get_class($graph));
        $vertices = $graph->getVertices();
        $vertexIds = $vertices->getIds();

        $this->assertNotEmpty($vertexIds);
        $this->assertSame(10, count($vertexIds));
        $this->assertTrue(in_array('\Just_A_Class0', $vertexIds));

        $this->assertTrue($vertices->hasVertexId('\Just_A_Class0'));
        $vertex = $vertices->getVertexId('\CDE\Just_A_Class4');
        $this->assertSame('\ABC\Just_A_Pure_Class', $vertex->getAttribute(VirtualClassMapGenerator::ATTRIBUTE_NAME_EXTENDS));

        return $graph;
    }

    /**
     * Test,that the method createEdges doesn't create edges for an empty directory.
     */
    public function testCreateEdgesWithEmptyDir()
    {
        $graph = $this->testCreateNodesWithEmptyDir();
        $graph = $this->generator->createEdges($graph);

        $edges = $graph->getEdges();
        $this->assertTrue($edges->isEmpty());

        return $graph;
    }

    /**
     * Test,that the method createEdges doesn't create edges for an empty directory.
     */
    public function testCreateEdgesWithFilledDir()
    {
        $graph = $this->testCreateNodesWithFilledDir();
        $graph = $this->generator->createEdges($graph);

        $edges = $graph->getEdges();

        $this->assertFalse($edges->isEmpty());
        $this->assertSame(4, $edges->count());

        $expectedEdges = array(
            "\\ABC\\Just_A_Pure_Class->\\Just_A_Class0",
            "\\ABC\\Just_A_Pure_Class->\\Just_A_Class3",
            "\\ABC\\Just_A_Pure_Class->\\CDE\\Just_A_Class4",
            "\\ABC\\Just_A_Pure_Class->\\Just_A_Class6"
        );

        foreach ($edges->getVector() as $edge) {
            $asString = $edge->getVertexStart()->getId() . "->" . $edge->getVertexEnd()->getId();

            if (in_array($asString, $expectedEdges)) {
                $index = array_search($asString, $expectedEdges);
                unset($expectedEdges[$index]);
            } else {
                $this->fail("Found a non expected edge: " . var_export($asString, true));
            }
        }

        if (!empty($expectedEdges)) {
            $this->fail("Not all edges were found! " . var_export($expectedEdges, true));
        }

        return $graph;
    }

    /**
     * Test, that the method markLeaves doesn't change an empty graph.
     */
    public function testMarkLeavesWithEmptyDir()
    {
        $graph = $this->testCreateEdgesWithEmptyDir();
        $graph = $this->generator->markLeaves($graph, __DIR__ . '/testData/normal');

        $this->assertEmpty($graph->getVertices()->getIds());
    }

    /**
     * Test, that the method markLeaves works correct for a non empty graph
     */
    public function testMarkLeavesWithFilledDir()
    {
        $graph = $this->testCreateEdgesWithFilledDir();
        $graph = $this->generator->markLeaves($graph, __DIR__ . '/testData/normal');

        $this->assertNotEmpty($graph->getVertices());
        $this->assertNotEmpty($graph->getEdges());

        $vertices = $graph->getVertices();
        $expectedLeaveIds = array('\Just_A_Class0',
                                  '\Just_A_Class1',
                                  '\Just_A_Class2',
                                  '\Just_A_Class3',
                                  '\CDE\Just_A_Class4',
                                  '\Just_A_Class5',
                                  '\Just_A_Class6',
                                  //'\Just_A_Class7', marked as internal, leaving it commented for documentation
                                  '\ABC\Eshop\Just_A_Pure_Class'
        );

        foreach ($vertices as $vertex) {
            $id = $vertex->getId();
            $isLeave = $vertex->getAttribute(VirtualClassMapGenerator::ATTRIBUTE_NAME_LEAVE);

            $this->assertNotNull($isLeave, "Expected vertex attribute (" . VirtualClassMapGenerator::ATTRIBUTE_NAME_LEAVE . ") is not present in vertex with id '$id'!");

            if (in_array($id, $expectedLeaveIds)) {
                $this->assertTrue($isLeave, "Expected vertex attribute (" . VirtualClassMapGenerator::ATTRIBUTE_NAME_LEAVE . ") is not true in vertex with id '$id'!");
            } else {
                $this->assertFalse($isLeave, "Expected vertex attribute (" . VirtualClassMapGenerator::ATTRIBUTE_NAME_LEAVE . ") is not false in vertex with id '$id'!");
            }
        }
    }

    /**
     * Test, that the method generate works with an empty dir
     */
    public function testGenerateWithEmptyDir()
    {
        $generatedFileName = __DIR__ . '/testData/generated/actual/empty.php';
        $expectedFileName = __DIR__ . '/testData/generated/expected/empty.php';

        $this->assureFileNotExists($generatedFileName);

        $this->generator->generate(__DIR__ . '/testData/emptyDir', $generatedFileName);

        $this->assertSame(file_get_contents($generatedFileName), file_get_contents($expectedFileName));

        $this->assureFileNotExists($generatedFileName);
    }

    /**
     * Test, that the method generate works with an empty dir
     */
    public function testGenerateWithEnterpriseLicense()
    {
        $generatedFileName = __DIR__ . '/testData/generated/actual/emptyEnterpriseLicense.php';
        $expectedFileName = __DIR__ . '/testData/generated/expected/emptyEnterpriseLicense.php';

        $this->assureFileNotExists($generatedFileName);

        $this->generator->generate(__DIR__ . '/testData/emptyDir', $generatedFileName, 'Enterprise');

        $this->assertSame(file_get_contents($expectedFileName), file_get_contents($generatedFileName));

        $this->assureFileNotExists($generatedFileName);
    }

    /**
     * Test, that the method generate works with an empty dir
     */
    public function testGenerateWithFilledDir()
    {
        $generatedFileName = __DIR__ . '/testData/generated/actual/normal.php';
        $expectedFileName = __DIR__ . '/testData/generated/expected/normal.php';

        $this->assureFileNotExists($generatedFileName);

        $this->generator->generate(__DIR__ . '/testData/normal', $generatedFileName);

        $this->assertFileExists($generatedFileName);
        $content = file_get_contents($generatedFileName);
        $this->assertNotEmpty($content);

        $this->assertSame(file_get_contents($generatedFileName), file_get_contents($expectedFileName));

        $this->assureFileNotExists($generatedFileName);
    }

    /**
     * @group generateAll
     */
    public function testGenerateAll()
    {
        $this->markTestSkipped('Takes to long and not sure, how to test this thing.');

        $this->generator->generateAll();
    }

    /**
     * Assert, that the two given arrays have the same sets of items.
     *
     * @param array $expectedArray The array we expect here.
     * @param array $resultArray   The array the functionality gave back.
     */
    protected function assertArraysContainSameItems($expectedArray, $resultArray)
    {
        foreach ($expectedArray as $expectedItem) {
            $this->assertTrue(
                in_array($expectedItem, $resultArray),
                "Expected item " . var_export($expectedItem, true) . " was not found in the result " . var_export($resultArray, true)
            );
        }
        foreach ($resultArray as $resultItem) {
            $this->assertTrue(
                in_array($resultItem, $expectedArray),
                "Result item " . var_export($resultItem, true) . " was not found in the expected " . var_export($expectedArray, true)
            );
        }
    }

    /**
     * Assure, that the given file doesn't exists.
     *
     * @param string $fileName The name of the file we want to be sure, which doesn't exists.
     */
    protected function assureFileNotExists($fileName)
    {
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $this->assertFileNotExists($fileName);
    }

}