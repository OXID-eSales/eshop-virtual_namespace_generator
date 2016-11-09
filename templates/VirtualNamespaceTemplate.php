<?php
/* ADD_LICENSE_HERE */

namespace OxidEsales\Eshop/* ADD_EDITION_HERE */\Core;

/**
 * This file holds the mapping of classes from the virtual namespace to the concrete classes of each edition.
 * Each edition has its own map file. The map files will be merged like this: CE <- PE <- EE
 * So the mapping to a concrete class will be overwritten, if a class exists in a different edition.
 *
 * @inheritdoc
 */
class VirtualNameSpaceClassMap extends \OxidEsales\EshopCommunity\Core\Edition\ClassMap
{

    /**
     * Return mapping of classes from the virtual namespace to concrete classes.
     *
     * @return array Map of classes in the virtual namespace to concrete classes
     */
    public function getOverridableMap()
    {
        return [
/* ADD_OVERRIDABLE_MAP_HERE */
        ];
    }

    /**
     * Returns class map, of classes which can't be extended by modules.
     * There are no use cases for virtual namespaces in not overridable classes at the moment.
     * This function will return always an empty array.
     *
     * @return array  Maps a class from the virtual namespace to a concrete class
     */
    public function getNotOverridableMap()
    {
        return [];
    }
}
