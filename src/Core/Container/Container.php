<?php
/**
 * Container
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);

namespace Apine\Core\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var Component[]
     */
    protected $entries = [];
    
    /**
     * Adds an entry to the container with an identifier
     *
     * Doing so will override any entry with the same identifier
     *
     * @param string $id
     * @param mixed $value
     * @param bool $factory
     */
    public function register (string $id, $value, bool $factory = false) : void
    {
        $this->remove($id);
        
        $this->entries[] = new Component($id, $value, $factory);
    }
    
    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws ContainerNotFoundException  No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id)
    {
        try {
            if (!$this->has($id)) {
                throw new ContainerNotFoundException(sprintf('No entry was found for the identifier "$e"', $id));
            }
    
            return $this->find($id)->invoke();
        } catch (ContainerNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new ContainerException(
                sprintf('Error while trying to retrieve the entry "%s"', $id)
            );
        }
    }
    
    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     * @throws \Throwable
     */
    public function has($id) : bool
    {
        return (null !== $this->find($id));
    }
    
    /**
     * @param string $id
     *
     * @return Component|null
     *
     * @throws \Throwable
     */
    private function find(string $id) : ?Component
    {
        $result = null;
    
        foreach ($this->entries as $component) {
            if (($id === $component->getName()) || $component->hasType($id)) {
                $result = $component;
                break;
            }
        }
        
        return $result;
    }
    
    /**
     * @param string $id
     */
    public function remove(string $id) : void
    {
        foreach ($this->entries as $key => $value) {
            if ($value->getName() === $id) {
                unset($this->entries[$key]);
            }
        }
    }
}