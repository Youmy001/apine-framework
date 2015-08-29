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

1. Clone this project in your working directory 
```sh
$ git clone https://github.com/Youmy001/apine_framework.git
```
2. Setup a virtual host for the project directory that allow rewrite rules and has filter module enabled in apache for version 2.4 or greater.
3. Import `resources/apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.
4. Edit the `Database` section in `config.conf` to include connection to your database. Check the [wiki](https://github.com/Youmy001/apine_framework/wiki) for more informations on configuration 
5. Open your browser and go to your virtual host address. APIne Framework is now ready to work.

