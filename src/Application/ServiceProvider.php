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
use Apine\Core\Routing\RouteGroup;
use Apine\Core\Routing\Router;
use Apine\Core\Routing\RouterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use ReflectionClass;
use ReflectionMethod;

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
        });
    
        $container->register('response', function () : ResponseInterface {
            return (new ResponseFactory())->createResponse();
        }, true);
        
        $container->register('uri', function () : UriInterface {
            return (new UriFactory())->createUriFromArray($_SERVER);
        });
    
        $container->register('router', function () use ($container) : RouterInterface {
            $router = new Router($container);
            
            $config = new Config('config/router.json');
    
            try {
                if ($config->serve->api === true) {
                    $router->group($config->prefixes->api, function (RouteGroup $group) {
                        $routes = json_decode(file_get_contents('config/routes/api.json'), true);
    
                        foreach ($routes as $pattern => $definitions) {
                            $controller = $definitions['controller'];
                            $reflection = new ReflectionClass($definitions['controller']);
                            unset($definitions['controller']);
                            
                            array_map(function(ReflectionMethod $method) use ($pattern, $controller, $group) {
                                $requestMethod = strtoupper($method->getName());
    
                                $group->map(
                                    [$requestMethod],
                                    $pattern,
                                    $controller,
                                    $method->getName()
                                );
                            }, array_filter(
                                $reflection->getMethods(ReflectionMethod::IS_PUBLIC),
                                function (ReflectionMethod $method) {
                                    return in_array(strtoupper($method->getName()), Router::$verbs, true);
                                }
                            ));
                        }
                    });
                }
        
                if ($config->serve->web === true)  {
                    $router->group($config->prefixes->web, function (RouteGroup $group) {
                        $routes = json_decode(file_get_contents('config/routes/web.json'), true);
    
                        foreach ($routes as $pattern => $definitions) {
                            foreach ($definitions as $method => $definition) {
                                $group->map(
                                    [$method],
                                    $pattern,
                                    $definition['controller'],
                                    $definition['action']
                                );
                            }
                        }
                    });
                }
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage(), $e->getCode(), $e);
            }
            
            return $router;
        });
    
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