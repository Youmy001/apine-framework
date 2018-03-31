<?php
/**
 * CollectionTest
 *
 * @license MIT
 * @copyright 2018 Tommy Teasdale
 */
declare(strict_types=1);


use Apine\Core\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    
    public function testConstructorEmpty()
    {
        $collection = new Collection();
        $this->assertAttributeEmpty('items', $collection);
        
        return $collection;
    }
    
    public function testConstructorWithItems()
    {
        $collection = new Collection([1,2,3]);
        $this->assertAttributeNotEmpty('items', $collection);
        $this->assertAttributeEquals([1,2,3], 'items', $collection);
    }
    
    public function testSet()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
        $items = $this->getObjectAttribute($collection, 'items');
        
        $this->assertArrayHasKey($index, $this->getObjectAttribute($collection, 'items'));
        $this->assertEquals('An Item', $items[$index]);
    }
    
    public function testSetWithKey()
    {
        $collection = new Collection();
        $collection->set('An Item', 'index');
        $items = $this->getObjectAttribute($collection, 'items');
        
        $this->assertArrayHasKey('index', $this->getObjectAttribute($collection, 'items'));
        $this->assertEquals('An Item', $items['index']);
    }
    
    public function testOffsetSet()
    {
        $collection = new Collection();
        $collection['index'] = 'An Item';
    
        $this->assertEquals('An Item', $collection['index']);
    }
    
    public function testAll()
    {
        $collection = new Collection([1,2,3]);
        
        $this->assertEquals([1,2,3], $collection->all());
    }
    
    public function testGet()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
        
        $item = $collection->get($index);
        $this->assertEquals('An Item', $item);
    }
    
    public function testGetNotFound()
    {
        $collection = new Collection();
        $item = $collection->get(8);
        
        $this->assertNull($item);
    }
    
    public function testFirst()
    {
        $collection = new Collection([1,2,3]);
        
        $this->assertEquals(1, $collection->first());
    }
    
    public function testLast()
    {
        $collection = new Collection([1,2,3]);
    
        $this->assertEquals(3, $collection->last());
    }
    
    public function testHas()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
        
        $this->assertTrue($collection->has($index));
    }
    
    public function testHasNotFound()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
        
        $this->assertFalse($collection->has('index'));
    }
    
    public function testOffsetExist()
    {
        $collection = new Collection();
        $index = $collection->set('An Item', 'index');
        $this->assertTrue(isset($collection['index']));
    }
    
    public function testRemove()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
        $this->assertArrayHasKey($index, $this->getObjectAttribute($collection, 'items'));
        
        $collection->remove($index);
        $this->assertArrayNotHasKey($index, $this->getObjectAttribute($collection, 'items'));
    }
    
    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessageRegExp /Index (.*) was not found in the collection/
     */
    public function testRemoveNotFound()
    {
        $collection = new Collection();
        $index = $collection->set('An Item');
    
        $collection->remove(2);
    }
    
    public function testOffsetUnset()
    {
        $collection = new Collection([
            25 => 2,
            345 => 3,
            4 => 1,
            500 => 5
        ]);
        unset($collection[345]);
        
        $this->assertEquals([25=>2,4=>1,500=>5], $this->getObjectAttribute($collection, 'items'));
    }
    
    public function testInject()
    {
        $collection = new Collection([1,2,3]);
        $collection->inject([
            23 => 'Name',
            'numbers' => [2,3,4,5]
        ]);
        
        $this->assertEquals(5, count($this->getObjectAttribute($collection, 'items')));
        $this->assertArrayHasKey('numbers', $this->getObjectAttribute($collection, 'items'));
    }
    
    public function testReverse()
    {
        $collection = new Collection([1,2,3]);
        $collection->reverse();
        
        $this->assertEquals([3,2,1], $collection->all());
    }
    
    public function testKeys()
    {
        $collection = new Collection([
            25 => 2,
            345 => 3,
            4 => 1,
            500 => 5
        ]);
        
        $this->assertEquals([25, 345, 4, 500], $collection->keys());
    }
    
    public function testKeysOnEmptyCollection()
    {
        $collection = new Collection();
        
        $this->assertEquals([], $collection->keys());
    }
    
    public function testCount()
    {
        $collection = new Collection();
        
        $this->assertEquals(0, $collection->count());
        
        $collection->inject([
            25 => 2,
            345 => 3,
            4 => 1,
            500 => 5
        ]);
        
        $this->assertEquals(4, $collection->count());
    }
    
    public function testClear()
    {
        $collection = new Collection([1,2,3]);
        $this->assertAttributeNotEmpty('items', $collection);
        
        $collection->clear();
        $this->assertAttributeEmpty('items', $collection);
    }
    
    public function testGetIterator()
    {
        $collection = new Collection([
            25 => 2,
            345 => 3,
            4 => 1,
            500 => 5
        ]);
        
        $iterator = $collection->getIterator();
        $this->assertInstanceOf(ArrayIterator::class, $iterator);
    }
}
