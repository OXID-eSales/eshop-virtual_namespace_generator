<?php
/* ADD_LICENSE_HERE */

namespace OxidEsales\Eshop/* ADD_EDITION_HERE */\Core;

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
/* ADD_OVERRIDABLE_MAP_HERE */
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
