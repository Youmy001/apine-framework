<?php
/**
 * DependencyResolver
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Routing;


use Apine\Core\Container\Container;
use Apine\Core\Container\ContainerException;
use Apine\Core\Container\ContainerNotFoundException;
use Psr\Http\Message\ServerRequestInterface as ServerRequest;

class DependencyResolver
{
    /**
     * @param ServerRequest $request
     * @param Route $route
     *
     * @return array
     */
    public static function mapParametersForRequest (ServerRequest $request, Route $route)
    {
        return array_merge(
            $request->getQueryParams(),
            self::resolveParameters($request, $route)
        );
    }
    
    /**
     * @param ServerRequest  $request
     * @param Route $route
     *
     * @return array
     */
    public static function resolveParameters (ServerRequest $request, Route $route)
    {
        $parameters = [];
        $requestString = $request->getUri()->getPath();
    
        // Compose the regular expression from the uri and the parameter definitions
        $regex = '/^' . str_ireplace('/', '\\/', $route->uri) . '$/';
    
        array_walk($route->parameters, function (ParameterDefinition $parameter) use (&$regex) {
            //$regex = str_ireplace('{' . $parameter->name . '}', $parameter->pattern, $regex);
            if($parameter->optional) {
                //$regex = str_ireplace('\/{?' . $parameter->name . '}', '(\/?' . $parameter->pattern . ')?', $regex);
                $regex = preg_replace('/\{(\??)' . $parameter->name . '(:(\(.+?\)))?\}/', '(\/?' . $parameter->pattern . ')?', $regex);
            } else {
                //$regex = str_ireplace('{' . $parameter->name . '}', $parameter->pattern, $regex);
                $regex = preg_replace('/\{' . $parameter->name . '(:(\(.+?\)))?\}/', $parameter->pattern, $regex);
            }
        });
    
        $results = preg_match($regex, $requestString, $matches);
    
        if ($results === 1) {
            foreach ($route->parameters as $key => $parameter) {
                if ($parameter->optional) {
                    $index = $key+2;
                } else {
                    $index = $key+1;
                }
                
                if (isset($matches[$index])) {
                    $parameters[$parameter->name] = $matches[$index];
                }
            }
        }
    
        return $parameters;
    }
    
    /**
     * @param Container $container
     * @param array     $arguments
     *
     * @return array
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public static function mapConstructorArguments(Container $container, array $arguments)
    {
        $parameters = array();
        
        foreach ($arguments as $arg) {
            $default = $arg->isDefaultValueAvailable() ? $arg->getDefaultValue() : null;
            $parameter = new Parameter((string) $arg->getType(), (string) $arg->getName(), $default);
            $parameters[] = self::getContainerServiceForParam($container, $parameter);
        }
        
        return $parameters;
    }
    
    /**
     * @param Container $container
     * @param array     $queryParams Query Arguments from the request
     * @param array     $arguments   Arguments from the controller action
     *
     * @return array
     */
    public static function mapActionArguments(Container $container, array $queryParams, array $arguments)
    {
        $parameters = array();
    
        array_walk($arguments, function (Parameter $param) use (&$parameters, $queryParams, $container) {
            if (isset($queryParams[$param->getName()])) {
                if (!$param->isBuiltIn()) {
                    $class = $param->getType();
                    $parameters[] = new $class($queryParams[$param->getName()]);
                } else {
                    $parameters[] = $queryParams[$param->getName()];
                }
            } else {
                $service = self::getContainerServiceForParam($container, $param);
                
                if (null !== $service) {
                    $parameters[] = $service;
                } else if ($param->getDefaultValue() !== null) {
                    $parameters[] = $param->getDefaultValue();
                }
            }
        });
        
        return $parameters;
    }
    
    /**
     * @param Container $container
     * @param Parameter   $parameter
     *
     * @return mixed|null
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public static function getContainerServiceForParam(Container $container, Parameter $parameter)
    {
        if (!$parameter->isBuiltIn()) {
            $type = $parameter->getType();
            
            if ($container->has((string) $type)) {
                return $container->get((string) $type);
            }
        }
        
        return null;
    }
}