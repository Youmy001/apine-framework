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
use Apine\Core\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DependencyResolver
{
    /**
     * @param Request $request
     * @param Route $route
     *
     * @return array
     */
    public static function mapParametersForRequest (Request $request, Route $route)
    {
        return ($request->isApiCall() && $route->isAPIRoute) ? self::resolveAPIParameters($request) : self::resolveWebParameters($request, $route);
    }
    
    /**
     * @param Request $request
     * @param Route $route
     *
     * @return array
     */
    public static function resolveWebParameters (Request $request, Route $route)
    {
        $parameters = [];
        $requestString = $request->getAction();
    
        // Compose the regular expression from the uri and the parameter definitions
        $regex = '/^' . str_ireplace('/', '\\/', $route->uri) . '$/';
    
        array_walk($route->parameters, function (ParameterDefinition $parameter) use (&$regex) {
            $regex = str_ireplace('{' . $parameter->name . '}', $parameter->pattern, $regex);
        });
    
        $results = preg_match($regex, $requestString, $matches);
    
        if ($results === 1) {
            foreach ($route->parameters as $key => $parameter) {
                $parameters[$parameter->name] = $matches[$key+1];
            }
        }
    
        return $parameters;
    }
    
    /**
     * @param Request $request
     *
     * @return array
     */
    public static function resolveAPIParameters (Request $request)
    {
        return $request->getQueryParams();
    }
    
    /**
     * @param Container $container
     * @param array     $arguments
     *
     * @return array
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function mapConstructorArguments(Container $container, array $arguments)
    {
        $parameters = array();
        
        foreach ($arguments as $arg) {
            $parameter = new Parameter((string) $arg->getType(), (string) $arg->getName());
            $parameters[] = self::getContainerServiceForParam($container, $parameter);
        }
        
        return $parameters;
    }
    
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
                $parameters[] = self::getContainerServiceForParam($container, $param);
            }
        });
        
        return $parameters;
    }
    
    /**
     * @param Container $container
     * @param Parameter   $parameter
     *
     * @return mixed|null
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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