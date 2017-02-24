<?php
/* ADD_LICENSE_HERE */

namespace OxidEsales\Eshop/* ADD_EDITION_HERE */\Core\Autoload;

/**
 * This file holds the mapping of classes from the virtual namespace to the concrete classes of each edition.
 * Each edition has its own map file. The map files will be merged like this: CE <- PE <- EE
 * So the mapping to a concrete class of the OXID eShop communitiy edition will be overwritten, if this class exists the
 * PE or EE edition.
 */
class VirtualNameSpaceClassMap/* ADD_EXTENDS_PARENT_EDITION_HERE */
{

    /**
     * Return mapping of classes from the virtual namespace to concrete classes.
     *
     * @return array Map of classes in the virtual namespace to concrete classes
     */
    public function getClassMap()
    {
        $classMap = [
/* ADD_MAP_HERE */
        ];

        /* ADD_MAP_MERGE HERE */

        return $classMap;
    }
}
