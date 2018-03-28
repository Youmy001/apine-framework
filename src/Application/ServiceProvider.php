<?php
/**
 * Service Provider
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */

declare(strict_types=1);

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
    
        $container->register('request', function () : Request {
            return Request::createFromGlobals();
        });
    
        $container->register('response', function () : Response {
            return new Response();
        }, true);
    
        $container->register(Connection::class, function () use ($container) : Connection {
            $config = new Config('config/database.json');
            
            return new Connection(
                $config->type,
                $config->host,
                $config->dbname,
                $config->username,
                $config->password,
                $config->charset
            );
        });
    
        $container->register(Database::class, function () use ($container) : Database {
            $connection = $container->get(Connection::class);
        
            return new Database($connection);
        });
    
        $container->register(BasicDatabase::class, function () use ($container) : BasicDatabase {
            $config = new Config('config/database.json');
            
            return new BasicDatabase(
                $config->type,
                $config->host,
                $config->dbname,
                $config->username,
                $config->password,
                $config->charset
            );
        });
        
        return $container;
    }
}