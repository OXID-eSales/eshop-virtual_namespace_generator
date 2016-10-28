<?php
/**
 * This Software is the property of OXID eSales and is protected
 * by copyright law - it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop EE
 */

namespace OxidEsales\EshopEnterprise\Core;

/**
 * @inheritdoc
 */
class VirtualNameSpaceClassMap extends \OxidEsales\EshopCommunity\Core\Edition\ClassMap
{

    /**
     * Returns leaf classes class map.
     *
     * @return array The classmap maps orignal calls to virtual class
     */
    public function getOverridableMap()
    {
        return [

        ];
    }

    /**
     * Returns class map, of classes which can't be extended by modules.
     * There are no usecases for virtual namspaces in not overidable classes at the moment.
     * This function will return always an empty array.
     *
     * @return array The classmap maps orignal calls to virtual class
     */
    public function getNotOverridableMap()
    {
        return [];
    }
}
