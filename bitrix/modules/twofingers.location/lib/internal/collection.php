<?php
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage sale
 * @copyright 2001-2012 Bitrix
 */
namespace Twofingers\Location\Internal;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * Class CollectionBase
 *
 * @package Bitrix\Sale\Internals
 */
class Collection
	implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array  */
	protected $collection = [];

    /**
     * Collection constructor.
     *
     * @param array   $collection
     */
	public function __construct(array $collection = [])
    {
	    $this->collection = $collection;
    }

    /**
     * @return string
     * @author Pavel Shulaev (https://rover-it.me)
     */
    public function getHash(): string
    {
        return crc32(serialize($this->collection));
    }

    /**
	 * @return ArrayIterator
	 */
	public function getIterator(): ArrayIterator
    {
		return new ArrayIterator($this->collection);
	}

    /**
     * Whether a offset exists
     *
     * @param $offset
     * @return bool
     */
	public function offsetExists($offset): bool
    {
		return isset($this->collection[$offset]) || array_key_exists($offset, $this->collection);
	}

    /**
     * Offset to retrieve
     */
    public function offsetGet($offset)
    {
        if (isset($this->collection[$offset]) || array_key_exists($offset, $this->collection))
        {
            return $this->collection[$offset];
        }

        return null;
    }

    /**
     * Offset to set
     */
    public function offsetSet($offset, $value)
    {
        if($offset === null)
        {
            $this->collection[] = $value;
        }
        else
        {
            $this->collection[$offset] = $value;
        }
    }

	/**
	 * Offset to unset
	 */
	public function offsetUnset($offset)
	{
		unset($this->collection[$offset]);
	}

	/**
	 * Count elements of an object
	 */
	public function count(): int
    {
		return count($this->collection);
	}

	/**
	 * Return the current element
	 */
	public function current()
	{
		return current($this->collection);
	}

	/**
	 * Move forward to next element
	 */
	public function next()
	{
		return next($this->collection);
	}

	/**
	 * Return the key of the current element
	 */
	public function key()
	{
		return key($this->collection);
	}

	/**
	 * Checks if current position is valid
	 */
	public function valid(): bool
    {
		$key = $this->key();
		return $key !== null;
	}

	/**
	 * Rewind the Iterator to the first element
	 */
	public function rewind()
	{
		return reset($this->collection);
	}

	/**
	 * Checks if collection is empty.
	 *
	 * @return bool
	 */
	public function isEmpty(): bool
    {
		return empty($this->collection);
	}

    /**
     * @return array
     * @author Pavel Shulaev (https://rover-it.me)
     */
	public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * @param array $collection
     */
    public function setCollection(array $collection = [])
    {
        $this->collection = $collection;
    }


}