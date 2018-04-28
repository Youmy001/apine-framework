<?php
/**
 * ServiceAwareTrait
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


namespace Apine\Application;

use Psr\Container\ContainerInterface;


/**
 * Trait ServiceAwareTrait
 *
 * @package Apine\Core\Container
 */
trait ServiceAwareTrait
{
    /**
     * @var ContainerInterface
     */
    protected $services;
    
    /**
     * @param string $className
     * @param callable|mixed $service
     */
    /*public function registerService(string $className, $service) : void
    {
        $this->services->register($className, $service);
    }*/
    
    /**
     * @param ContainerInterface $container
     */
    public function setServices(ContainerInterface $container) : void
    {
        $this->services = $container;
    }
}