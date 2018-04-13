APIne Framework
================

APIne is a simple to use modular MVC Framework ready for use as a RESTful API. It intends to be a general purpose framework and a RESTful service providing basic session management, authentication, routing, and database abstraction without including useless tools. APIne's focus is to let you work without imposing to relearn PHP.

You may per se, use APIne solely for its routing system and its MVC approach then use your favorite PHP libraries for everything else.

APIne already implements a comprehensive session manager, a basic yet effective Entity manager, and, TWIG as its template manager — enough to boot any kind of project.

## Requirements

* PHP 5.6 or greater with PDO extension
* MySQL 5.6 or MariaDB 10.1 or greater
* Apache 2.4 with mod_rewrite active

The project using APIne must be installed at the root of a host that must include the instruction AllowOverride FileInfo Options Indexes for the default settings. PHP's user must also have writing permissions on the project directory.

> APIne does not officially support any other HTTP server (nginx, lighttpd, ...). If you are using one of those you might need modify your server's configuration.

## Installation

APIne Framework is available as a Composer Package on Packagist as well as a standalone project.

### With Composer

Add the following line to your composer.json file : `"youmy001/apine-framework": ^1.2"`.
Or enter this command : `$ composer require youmy001/apine-framework`.

### Standalone

Clone this repository in your working directory : `$ git clone https://github.com/Youmy001/apine_framework.git`. Then checkout to the branch of the latest stable release: `git checkout 1.2.x`.

## Quick Start a Project

To quickly start a new project execute the assistant in a web browser located at `http://[domain_name_of_your_project]/apine-framework/install.php` if APIne is used standalone or `http://[domain_name_of_your_project]/vendor/youmy001/apine-framework/install.php` if it is used as a composer package, and follow the steps. The assistant will automatically generate a basic config, an empty locale, a .htaccess file, and a basic index.php and will automatically download the lastest version of Composer's binary if you are using the standalone version.

The assistant will also try to import some tables essential for APIne's operation. Make sure you already created a database before launching the assistant and that the database is accessible from your project's perspective.

If you run a standalone version of APIne's, install dependencies using this command after going through the assistant : `$ php composer.phar install`.

## Manually Start a Project

First of all, in order to use APIne Framework, you must copy this file to the root of your project :

- apine-framework/Installation/htaccess_template.php

Then replace the PHP tag by the path to APIne. If you are using APIne as a Composer package, this will be `/vendor/youmy001/apine-framework`. If you are using a standalone APIne, this will likely be `/apine-framework`. And rename the file `.htaccess`.

Next, create a `index.php` file and add the following content in it:

    require_once 'vendor/autoload.php'; // If APIne is a Composer package
    //require_once 'apine-framework/Autoloader.php'; // If APIne is standalone
    
    $loader = new Apine\Autoloader();
    $loader->register();
    
    $apine = new Apine\Application\Application();
    $apine->set_mode(APINE_MODE_DEVELOPMENT);
    $apine->run(APINE_RUNTIME_HYBRID);

Finally, you will need to create a file name `config.ini` that will contain various configuration information. Complete the following in your config file :

    [application]
    	title = "Project Name"
    	author = "Author Name"
    	description = "Description"
    [database]
    	host = "localhost"
    	type = "mysql"
    	dbname = "projectdb"
    	charset = "utf8"
    	username = "root"
    	password = ""
    [localization]
    	timezone_default = "America/New_York"
    	locale_default = "en_US"
    	locale_detection = "yes"
    	locale_cookie = "no"
    	locale_directory = "resources/languages"
    [runtime]
    	token_lifespan = "600"
    	default_layout = "default"
    [entity]
    	adjust_timestamp = "yes"

## Learn More

Learn More at the following links :

- [Documentation](https://github.com/Youmy001/apine-framework/wiki)
- [Example Project](https://github.com/Youmy001/apine-framework-example)

## Support

Get support at the following links :

- [Email Assistance](mailto:tteasdaleroads@gmail.com?subject=Support%20Request%20APIne%20Framework)
- [Issue Tracker](https://github.com/Youmy001/apine-framework/issues)
- [Public Wiki](https://github.com/Youmy001/apine-framework/wiki)

## License

The APIne Framework is distributed under the MIT license. See the [LICENSE file](https://github.com/Youmy001/apine-framework/blob/master/LICENSE) for more information.
