# Invent2
File templating engine for magento modules

# How to install this Module
It's packagist now! https://packagist.org/packages/wesleywmd/magento2-invent

To install this library is as simple as 

    composer require wesleywmd/magento2-invent dev-master --dev
    
# Current Version
### 1.1.0

- added `invent:model` command
- added `--model` option to `invent:module` command
- added `invent:preference` command
- restructured xml file generation to use a standard class instead of separate ones.
 
# How to use this Module
This module can be used with new and existing modules and even appends to existing xml files. This module makes skeleton classes and does not populate anything specific to your module. It is meant to create a standard of what different components should look like. Feedback or this standard is appreciated.

## Create a new Module 
To create a new module use this command:

    bin/magento invent:module <module_name>
    
Where module_name name defines you, well, your module name. Here is an example:

    bin/magento invent:module Wesleywmd_TestModule

This will create a new module, including you directory structure in `app/code`, your `etc/module.xml`, and your `registration.php` files.

Note: You can also pass options for most components that invent can create when creating your module or you can choose to use the commands for each component. Here is an example:

    bin/magento invent:module Wesleywmd_TestModule --cron test_cron --cron other_cron --controller "test/index/index"
    
This command will create your module with 2 cron jobs and a controller prebuilt in for you. If you want more options for creating a component see the command for the specific component.

## Create a new Block

    bin/magento invent:block <module_name> <block_name>

## Create a new Command

    bin/magento invent:command <module_name> <command_name>

## Create a new Controller 

    bin/magento invent:controller <module_name> <controller_url> <--router standard>

## Create a new Cron 

    bin/mangento invent:cron <module_name> <cron_name> <--method execute> <--schedule "* * * * *"> <--group default>

## Create a new Model

    bin/magento invent:model <module_name> <model_name>
    
## Create a new Preference

    bin/magento invent:preference <module_name> <for> <type>
    
# Changelog

### 1.0.0

- Initial Launch
