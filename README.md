APIne Framework
================

APIne is a simple to use modular MVC Framework ready for the IoT (Internet of Things). It intends to be a general purpose framework and API providing session management, authentication, routing, and database abstraction without including useless tools. APIne is lets you work without imposing too much. Per example, one may use APIne for nothing but the simple routing and build his own handlers for everything else.

The most notable features include a complete session manager (login, logout, registration and password restoration) with basic users and permissions and a database abstraction layer that prevents you to write every queries.

> For an example project see [https://github.com/Youmy001/apine-framework-example.git](https://github.com/Youmy001/apine-framework-example.git)

## Requirements

* PHP 5.4.0 or greater
* MySQL 5
* Apache 2.4
* mod_rewrite
* filter_module

The project must be set in a virtual host that allows rewrites for routes to work. PHP's user must have writing permissions on the project directory.

## Get Started

1. Clone this project in your project directory : `$ git clone https://github.com/Youmy001/apine-framework.git`
3. Setup a virtual host for the project directory that allow rewrite rules and optionaly has mod\_deflate and filter\_module enabled in apache for version 2.4 or greater.
4. In a web browser, go to `http://[domain_name_of_your_project]/apine-framework/install.php` and follow the steps. The installer will automatically generate basic assets.
5. Install composer depandancies with the following command : `$ php composer.phar install`
6. You are now ready to work on a blank APIne Project.
7. Check the [wiki](https://github.com/Youmy001/apine-framework/wiki) for more informations on configuration or documentation about the framework.