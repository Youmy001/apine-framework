APIne Framework
================

APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and database abstraction without including useless tools. APIne is lets you work without imposing too much. Per example, one may use APIne for nothing but the simple routing and build his own handlers for everything else.

The most notable features include a complete session manager with basic users and permissions and a database abstraction layer that prevents you to manually write every queries.

## Requirements

* PHP 5.4.0 or greater
* MySQL 5
* Apache 2.4
* mod_rewrite
* filter_module

The project must be set in a virtual host that allows rewrites and .htaccess files for routes to work. PHP's user must have writing permissions on the project directory.

> If you are using another web server (nginx, lighttpd, ...) you must modify your server's config to replace the role of the .htaccess file.

## Installation

APIne Framework is available as a Composer Package on Packagist as well as a standalone project.

### With Composer

Add the following line to your composer.json file : `"youmy001/apine-framework": "~1.*"`.
Or enter this command : `$ composer require youmy001/apine-framework`.

### Standalone

Clone this repository in your working directory : `$ git clone https://github.com/Youmy001/apine_framework.git`.

## Quick Start a Project

To quickly start a new project execute the assistant in a web browser located at `http://[domain_name_of_your_project]/apine-framework/install.php` if APIne is used standalone or `http://[domain_name_of_your_project]/vendor/youmy001/apine-framework/install.php` if it is used as a composer package, and follow the steps. The assistant will automatically generate a basic config, an empty locale, a .htaccess file, and a basic index.php and will automatically download the lastest version of Composer's binary if you are using the standalone version.

The assistant will also try to import some tables essential for APIne's operation. Make sure you already created a database before launching the assistant and that the database is accessible from your project's perspective.

If you run a standalone version of APIne's, install depedancies using this command after going through the assistant : `$ php composer.phar install`.

## Manually Start a Project

First of all, in order to use APIne Framwork, you must copy this file to the root of your project :

- apine-framework/Installation/htacess_template.php

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
    [runtime]
    token_lifespan = "600"
    default_layout = "default"

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

The APIne Framework is licensed under the MIT license. See the [LICENSE file](https://github.com/Youmy001/apine-framework/blob/master/LICENSE) for more information.
