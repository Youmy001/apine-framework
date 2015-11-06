APIne Framework
================

APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and DAL abstraction without including useless tools.

The most notable features include a conplete session manager (login, logout, registration and password restoration) with basic users and permissions and a database abstraction layer that prevents you to write every queries.

## Requirements
* PHP 5.4.0 or greater
* MySQL 5
* Apache 2.4
* mod_rewrite
* filter_module

The project must be set in a virtual host that allows rewrites for routes to work.

## Installation

1. Clone this project in your working directory : `$ git clone https://github.com/Youmy001/apine_framework.git`
2. Setup a virtual host for the project directory that allow rewrite rules and has filter module enabled in apache for version 2.4 or greater.
3. Import `resources/apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.
4. Edit the `Database` section in `config.conf` to include connection to your database. Check the [wiki](https://github.com/Youmy001/apine_framework/wiki) for more informations on configuration.
5. Install composer depandancies with the following command : `$ php composer.phar install`
6. Open your browser and go to your virtual host address. APIne Framework is now ready to work.

## Migration from RC1 (1.0.0-dev.8.6)

Since RC1, there was a lot of modifications uncompatible with older versions. We recommend to users of older than 1.0.0-dev.11.0 to simply reinstall APIne Framework. However, they can execute the migration script named `resources/migration-RC1.php` to automaticaly update the database and add missing entries in the configuration file. If they do so, user passwords are going to be reset to a value determined by the script. The support for the older encryption method was dropped with version 1.0.0-dev.11.0

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

