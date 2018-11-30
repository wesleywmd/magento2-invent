# Invent2
File templating engine for magento modules

# How to install this Module
First, add a repository reference to you composer.json file

    composer config repositories.invent2 vcs https://github.com/wesleywmd/Invent2

Then allow composer to install the library

    composer require wesleywmd/invent2 dev-master --dev
    
# Current Version
Currently this module is a proof of concept. I appreciate feedback, feature requests, and bug reports. Please open issues on this repository.
 
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

    bin/magento invent:controller <module_name> <controller_url>

## Create a new Cron 

    bin/mangento