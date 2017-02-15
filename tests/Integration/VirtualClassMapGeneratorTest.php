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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

require_once dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'VirtualClassMapGenerator.php';

/**
 * Tests class VirtualClassMapGenerator
 */
class VirtualClassMapGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \VirtualClassMapGenerator::getNameSpacedClasses
     */
    public function testgetNameSpacedClassesDoesNotExcludeExceptionsEE()
    {
        $expectedException = '\OxidEsales\EshopEnterprise\Core\Exception\AccessRightException';
        $sourcePath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'vendor/oxid-esales/oxideshop-ee';
        $edition = 'Enterprise';

        $virtualClassMapGenerator = new \VirtualClassMapGenerator();
        $iterator = $virtualClassMapGenerator->getDirectoryIterator($sourcePath, $edition);
        $classes = $virtualClassMapGenerator->getNameSpacedClasses($iterator);

        $this->assertNotFalse(array_search($expectedException, $classes));
    }

    /**
     * @covers \VirtualClassMapGenerator::getNameSpacedClasses
     */
    public function testgetNameSpacedClassesDoesNotExcludeExceptionsPE()
    {
        /** ATM there are no exceptions in the PE Edition, so we must take one from test_data  */
        $expectedException = '\OxidEsales\EshopProfessional\Tests\Integration\DummyException';
        $sourcePath = __DIR__ . DIRECTORY_SEPARATOR . 'test_data';
        $edition = 'Professional';

        $virtualClassMapGenerator = new \VirtualClassMapGenerator();
        $iterator = $virtualClassMapGenerator->getDirectoryIterator($sourcePath, $edition);
        $classes = $virtualClassMapGenerator->getNameSpacedClasses($iterator);

        $this->assertNotFalse(array_search($expectedException, $classes));
    }

    /**
     * @covers \VirtualClassMapGenerator::getNameSpacedClasses
     */
    public function testgetNameSpacedClassesDoesNotExcludeExceptionsCE()
    {

        $expectedException = '\OxidEsales\EshopCommunity\Core\Exception\StandardException';
        $sourcePath = dirname(dirname(dirname(dirname(dirname(__DIR__))))) . DIRECTORY_SEPARATOR . 'source/';
        $edition = 'Community';

        $virtualClassMapGenerator = new \VirtualClassMapGenerator();
        $iterator = $virtualClassMapGenerator->getDirectoryIterator($sourcePath, $edition);
        $classes = $virtualClassMapGenerator->getNameSpacedClasses($iterator);

        $this->assertNotFalse(array_search($expectedException, $classes));
    }
}
