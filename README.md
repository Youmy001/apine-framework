APIne Framework
================

APIne is a simple to use modular MVC Framework ready for the IotT (Internet of the Things). It intends to be a general purpose framework and API providing session management, authentication and DAL abstraction without including useless tools.

## Requirements
* PHP 5.4.0 or greater
* MySQL 5
* Apache 2

The project must be set in a virtual host that allows rewrites for the routes to work.

## Installation

1. Clone this project in your working directory 
```sh
$git clone https://github.com/Youmy001/apine_framework.git
```
2. Setup a virtual host for the project directory that allow rewrite rules
3. Import `resources/apine_sql_tables.sql` into your database. This file includes the instructions to create the tables needed by the framework.
4. Edit the `Database` section in `config.conf` to include connection to your database. Check the [wiki](https://github.com/Youmy001/apine_framework/wiki) for more informations on configuration 
5. Open your browser and go to your virtual host address. APIne Framework is now ready to work.