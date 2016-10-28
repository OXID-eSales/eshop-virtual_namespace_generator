Generator for virtual namespace class maps 
==========================================

This component generates the virtual namespaces for the three editions of the eShop.
The generated files lay in the source/Core or Core directories.


How this component works
------------------------

The generation is divided into four major steps:

1. Read all classes of the specific edition.
2. Build an inheritance tree of the classes.
3. Decide which classes should be in the output.
4. Output the needed classes into the generated file in the wished form.

These steps follow the proven compiler construction pipeline pattern. The tree is build with the help of the php [clue/graph library](https://github.com/clue/graph).


Vision of this component
------------------------

With the inheritance tree any kind of long list of classes (like class maps) can be generated. For example the auto completion of the IDE (e.g. [PHPStorm](https://www.jetbrains.com/phpstorm/)) is no big problem.
The third step "Decide which classes should be in the output" can be generalized in a way, like known from compiler construction, with an [DSL](https://en.wikipedia.org/wiki/Domain-specific_language), which describes the calculation of attributes. 

