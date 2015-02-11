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
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Onikiri;

use Samurai\Onikiri\Exception\TransactionFailedException;

/**
 * Transaction
 *
 * @package     Samurai.Onikiri
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Transaction
{
    /**
     * depth
     *
     * @var     int
     */
    protected $_depth = 0;

    /**
     * connection
     *
     * @var     array(Samurai\Onikiri\Connection)
     */
    protected $_connections = [];


    /**
     * get connections
     *
     * @return  Samurai\Onikiri\Connection
     */
    public function getConnections()
    {
        return $this->_connections;
    }

    /**
     * set connection
     *
     * @param   Samurai\Onikiri\Connection  $connection
     */
    public function setConnection(Connection $connection)
    {
        if (! in_array($connection, $this->_connections, true)) {
            $this->_connections[] = $connection;
        }
    }


    /**
     * begin flagment
     */
    public function begin()
    {
        $this->_depth ++;

        return $this;
    }
    
    /**
     * begin transaction
     */
    public function beginTransaction()
    {
        if (! $this->isValid()) return;

        foreach ($this->getConnections() as $connection) {
            if (! $connection->inTx()) $connection->beginTransaction();
        }
    }

    /**
     * commit transaction
     */
    public function commit()
    {
        if (! $this->isValid()) return;

        if ($this->_depth > 1) {
            $this->_depth --;
            return;
        }

        foreach ($this->getConnections() as $connection) {
            $connection->commit();
        }

        $this->_depth = 0;
    }

    /**
     * rollback transaction
     *
     * @throw   Samurai\Onikiri\Exception\TransactionFailedException
     */
    public function rollback($message = 'failed to transaction.')
    {
        $this->rollbackWithoutThrow();
        throw new TransactionFailedException($message);
    }

    /**
     * rollback transaction without throw
     */
    public function rollbackWithoutThrow()
    {
        if (! $this->isValid()) return;

        foreach ($this->getConnections() as $connection) {
            $connection->rollback();
        }

        $this->_depth = 0;
    }


    /**
     * is valid ?
     *
     * @return  boolean
     */
    public function isValid()
    {
        return $this->_depth > 0;
    }


    /**
     * in transaction ?
     *
     * @return  boolean
     */
    public function inTx()
    {
        return $this->_depth > 0;
    }
}

