Generator for virtual namespace class maps 
==========================================

This component generates the virtual namespaces class maps for the three editions of the eShop.
The class map files are generated in the Core directory of each installed edition as VirtualNameSpaceClassMap.php.


Installation
------------

To install this component, run the following command in the root directory of your OXID eSales eShop: 

```
composer require --dev oxid-esales/eshop-virtual-namespace-generator
```

Usage
-----

To create or update the virtual namespace class maps, run the following command in the root directory of your OXID eSales eShop:  

```
vendor/bin/oe-eshop-generate_virtual_namespaces
```
