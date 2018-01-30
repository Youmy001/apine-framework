<?php
/**
 * Created by PhpStorm.
 * User: youmy
 * Date: 07/01/18
 * Time: 11:31 PM
 */

namespace Apine\Core\Container;


use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends \Exception implements NotFoundExceptionInterface
{
    
}