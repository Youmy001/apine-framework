APIne Framework
================

APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and database abstraction without including useless tools. APIne is lets you work without imposing too much. Per example, one may use APIne for nothing but the simple routing and build his own handlers for everything else.

The most notable features include a complete session manager (login, logout, registration and password restoration) with basic users and permissions and a database abstraction layer that prevents you to write every queries.

## Requirements
* PHP 5.4.0 or greater
* MySQL 5
* Apache 2.4
* mod_rewrite
* filter_module

The project must be set in a virtual host that allows rewrites for routes to work.

## Get Strarted from the example

1. Clone this project in your working directory : `$ git clone https://github.com/Youmy001/apine_framework.git`
2. Setup a virtual host for the project directory that allow rewrite rules and optionaly has mod\_deflate and filter\_module enabled in apache for version 2.4 or greater.
3. Import `apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.
4. Create a copy of `sample_config.ini` named `config.ini`.
5. Edit the `Database` section in `config.ini` to include connection to your database. Check the [wiki](https://github.com/Youmy001/apine_framework/wiki) for more informations on configuration.
6. Install composer depandancies with the following command : `$ php composer.phar install`
7. Open your browser and go to your virtual host address. APIne Framework is now ready to work.

## Get Started from scratch

1. Clone this project in a temporary directory : `$ git clone https://github.com/Youmy001/apine_framework.git`
2. Create a directory for your new project
3. Setup a virtual host for the project directory that allow rewrite rules and optionaly has mod\_deflate and filter\_module enabled in apache for version 2.4 or greater.
4. Copy these files/directories to your project from the previous clone : `/lib`, `.htaccess`, `index.php`, `apine_sql_tables.sql` and `sample_config.ini`.
5. Import `apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.
6. Edit the `Database` section in the config to include connection to your database. Check the [wiki](https://github.com/Youmy001/apine_framework/wiki) for more informations on configuration.
7. Install composer depandancies with the following command : `$ php composer.phar install`
8. You are now ready to work on a blank APIne Project.


## Migration from RC3 (1.0.0-dev.14.00 or 1.0.0-rc3) to DEV branch

If you recently decided to switch to the dev branch from the lastest RC release, your application might still work by only replacing the `/lib` directory with the new one. However, your application might behave strangely - not behaving according to your config - because of many configuration entries being deprecated in favour of an Application facade.

## Migration from RC2 (1.0.0-dev.11.8 or 1.0.0-rc2) to RC3 (1.0.0-dev.14.00 or 1.0.0-rc3)

There was a lot of changes in the internal management of the session we strongly recommend you to simply reinstall APIne Framework and import the missing tables from `apine_sql_tables.sql`. However, your application might work just by replacing the `/lib` directory and the `index.php`.

### New Features
* Translation Directory;
* Session handling based in the Database;
* Support for webroot;
* JSON Route config;
* Improved HTTPS support;

### Deprecated Components
* ApineTranslator. Use ApineTranslationDirectory instead.

## Migration from RC1 (1.0.0-dev.8.6) to RC2 (1.0.0-dev.11.8 or 1.0.0-rc2)

Since RC1, there was a lot of modifications uncompatible with older versions. We recommend to users of older than 1.0.0-dev.11.0 (RC2 release was 1.0.0-dev.11.8) to simply reinstall APIne Framework. The support for the older encryption method was dropped with version 1.0.0-dev.11.0.

### New Features
* Extensible users;
* Improved encryption and hashing methods;
* Exception Handling;
* Improved Session Handling;
* Improved Routing;
* Basic RESTful API;
* Composer Integration;

### Renamed Components

Former names for these components are now deprecated.

* Autoload became ApineAutoload;
* Liste became ApineCollection;
* Controller became ApineController;
* Config became ApineConfig;
* Cookie became ApineCookie;
* Database became ApineDatabase;
* Encryption became ApineEncryption;
* Request became ApineRequest;
* ApineTranslator became ApineAppTranslator;
* Translation became ApineTranslation;
* TranslationLanguage became ApineTranslationLanguage;
* TranslationLocale became ApineTranslationLocale;
* Translator became ApineTranslator;
* URL_Helper became ApineURLHelper;
* Version became ApineVersion;
* View became ApineView;
* HTMLView became ApineHTMLView;
* FileView became ApineFileView;
* JSONView became ApineJSONView;

