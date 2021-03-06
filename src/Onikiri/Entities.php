<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri;

use ArrayIterator;
use IteratorAggregate;
use Samurai\Onikiri\Statement;
use Samurai\Onikiri\Connection;
use Samurai\Onikiri\EntityTable;
use Samurai\Onikiri\Criteria\Criteria;
use Samurai\Samurai\Component\Pager\SimplePager;

/**
 * Entities class.
 *
 * @package     Onikiri
 * @subpackage  Entity
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Entities implements IteratorAggregate
{
    /**
     * entities cache.
     *
     * @var     array
     */
    private $_entities = [];

    /**
     * position.
     *
     * @var     int
     */
    private $_position = 0;

    /**
     * entity table
     *
     * @var     Samurai\Onikiri\EntityTable
     */
    public $table;

    /**
     * criteria
     *
     * @var     Samurai\Onikiri\Criteria\Criteria
     */
    public $criteria;


    /**
     * constructor.
     *
     * @param   Samurai\Onikiri\EntityTable $table
     */
    public function __construct(EntityTable $table = null)
    {
        $this->table = $table;
    }


    /**
     * add entity.
     *
     * @param   Samurai\Onikiri\Entity  $entity
     */
    public function add(Entity $entity)
    {
        $this->_entities[] = $entity;
    }


    /**
     * get size.
     *
     * @return  int
     */
    public function size()
    {
        return count($this->_entities);
    }


    /**
     * get cols
     *
     * @return array
     */
    public function col($column)
    {
        $values = [];
        foreach ($this->_entities as $entity) {
            if ($entity->hasAttribute($column)) $values[] = $entity->$column;
        }
        return $values;
    }


    /**
     * reverse sorted.
     *
     * @return  Samurai\Onikiri\Entities
     */
    public function reverse()
    {
        $entities = new Entities();

        foreach (array_reverse($this->_entities) as $entity) {
            $entities->add($entity);
        }

        return $entities;
    }


    /**
     * get by position.
     *
     * @param   int     $position
     * @return  Samurai\Onikiri\Entity
     */
    public function getByPosition($position)
    {
        return isset($this->_entities[$position]) ? $this->_entities[$position] : null;
    }


    /**
     * fetch.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function fetch()
    {
        $entity = $this->current();
        if ($this->valid()) {
            $this->next();
        }
        return $entity;
    }

    /**
     * get first entity.
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function first()
    {
        return $this->getByPosition(0);
    }

    /**
     * get last entity
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function last()
    {
        return $this->getByPosition($this->size() - 1);
    }

    /**
     * get by random pick
     *
     * @return  Samurai\Onikiri\Entity
     */
    public function random()
    {
        return $this->getByPosition(rand(0, $this->size() - 1));
    }


    /**
     * filtering results
     *
     * @param   string|closure  $key
     * @param   mixed           $value
     * @return  Samurai\Onikiri\Entities
     */
    public function filter($key, $value = null)
    {
        if (! $key instanceof \Closure) {
            $closure = function($entity) use ($key, $value) {
                return $entity->$key == $value;
            };
        } else {
            $closure = $key;
        }
        
        $filtered = new Entities();
        foreach ($this->_entities as $entity) {
            if ($closure($entity)) {
                $filtered->add($entity);
            }
        }
        return $filtered;
    }

    /**
     * sorting results
     *
     * @param   callable    $closure
     * @return  Samurai\Onikiri\Entities
     */
    public function sort(callable $closure)
    {
        $entities = $this->_entities;
        usort($entities, $closure);

        $sorted = new Entities();
        foreach ($entities as $entity) {
            $sorted->add($entity);
        }
        return $sorted;
    }


    /**
     * each attach closure
     *
     * @param   Closure     $closure
     */
    public function each(\Closure $closure)
    {
        foreach ($this as $entity) {
            $closure($entity);
        }
    }
    
    
    /**
     * get pager helper
     *
     * @return  Samurai\Samurai\Component\Pager\SimplePager
     */
    public function getPager($class = 'Samurai\\Samurai\\Component\\Pager\\SimplePager')
    {
        $pager = new $class();
        return $this->table->initializePager($pager, $this->criteria);
    }

    /**
     * set criteria
     *
     * @param   Samurai\Onikiri\Criteria\Criteria   $criteria
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }


    /**
     * @implements
     */
    public function current()
    {
        return $this->getByPosition($this->_position);
    }

    /**
     * @implements
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * @implements
     */
    public function next()
    {
        $this->_position++;
    }

    /**
     * @implements
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * @implements
     */
    public function valid()
    {
        return isset($this->_entities[$this->_position]);
    }


    /**
     * get iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->_entities);
    }


    /**
     * bredge to each entity
     */
    public function __call($method, $args)
    {
        foreach ($this->_entities as $entity) {
            call_user_func_array([$entity, $method], $args);
        }
    }
}

