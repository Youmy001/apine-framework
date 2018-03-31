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
use Apine\Core\Http\Factories\RequestFactory;
use Apine\Core\Http\Factories\ResponseFactory;
use Apine\Core\Http\Factories\UriFactory;
use Apine\Core\Container\Container;
use Apine\Core\Database\Connection;
use Apine\Core\Database\Database;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

final class ServiceProvider
{
    public static function registerDefaultServices() : Container
    {
        $container = new Container();
    
        $container->register('request', function () : ServerRequestInterface {
            return (new RequestFactory())->createServerRequestFromGlobals(
                $_SERVER,
                $_GET,
                $_POST,
                $_FILES,
                $_COOKIE,
                getallheaders(),
                file_get_contents('php://input')
            );
        }, true);
    
        $container->register('response', function () : ResponseInterface {
            return (new ResponseFactory())->createResponse();
        }, true);
        
        $container->register('uri', function () : UriInterface {
            return (new UriFactory())->createUriFromArray($_SERVER);
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