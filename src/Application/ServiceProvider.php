<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 07/01/18
 * Time: 11:14 PM
 */

namespace Apine\Application;

use Apine\Core\Config;
use Apine\Core\Database as BasicDatabase;
use Apine\Core\Http\Response;
use Apine\Core\JsonStore;
use Apine\Core\Container\Container;
use Apine\Core\Database\Connection;
use Apine\Core\Database\Database;
use Apine\Core\Http\Request;

final class ServiceProvider
{
    public static function registerDefaultServices() : Container
    {
        $container = new Container();
    
        $container->register(Config::class, function () : Config {
            return new Config('settings.json');
        });
    
        $container->register(JsonStore::class, function () : JsonStore {
            return JsonStore::getInstance();
        });
    
        $container->register(Request::class, function () : Request {
            return Request::createFromGlobals();
        });
    
        $container->register(Response::class, function () : Response {
            return new Response();
        }, true);
    
        $container->register(Connection::class, function () use ($container) : Connection {
            $config = $container->get(Config::class);
            
            return new Connection(
                $config->database->type,
                $config->database->host,
                $config->database->dbname,
                $config->database->username,
                $config->database->password,
                $config->database->charset
            );
        });
    
        $container->register(Database::class, function () use ($container) : Database {
            $connection = $container->get(Connection::class);
        
            return new Database($connection);
        });
    
        $container->register(BasicDatabase::class, function () use ($container) : BasicDatabase {
            $config = $container->get(Config::class);
            
            return new BasicDatabase(
                $config->database->type,
                $config->database->host,
                $config->database->dbname,
                $config->database->username,
                $config->database->password,
                $config->database->charset
            );
        });
        
        return $container;
    }
}