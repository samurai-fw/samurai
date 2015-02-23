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

namespace Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;

/**
 * Driver for sqlite.
 *
 * @package     Onikiri
 * @subpackage  Driver
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SqliteDriver extends Driver
{
    /**
     * @implements
     */
    public function connect(Database $database)
    {
        $dsn = $this->makeDsn($database);
        $con = new Connection($dsn);
        return $con;
    }
    
    
    /**
     * @implements
     */
    public function makeDsn(Database $database)
    {
        $dsn = 'sqlite:';
        $info = array();

        // database name
        $name = $database->getDatabaseName();

        // modify for phinx name strategy
        if (! $database->isUseMemory()) $name = $name . '.sqlite3';
        $info[] = $name;

        $dsn = $dsn . join(';', $info);
        return $dsn;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function getTableDescribe(Connection $connection, $table)
    {
        $info = [];
        
        $sql = "PRAGMA table_info('{$table}')";
        $stmt = $connection->query($sql);
        $describes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($describes as $describe){
            // column name
            $column = $describe['name'];
            
            // column type
            $attribute = null;
            if(preg_match('/^(.+?)\((.+?)\)(.*)?/', $describe['type'], $matches)){
                $type = $matches[1];
                $length = $matches[2];
                $attribute = isset($matches[3]) ? trim($matches[3]) : null;
            } else {
                $matches = preg_split('/\s+/', $describe['type']);
                $type = $matches[0];
                $length = null;
            }
            
            // null accepted ?
            $nullable = $describe['notnull'] === '0' ? true : false;
            
            // keys
            $is_primary_key = $describe['pk'] === '1' ? true : false;
            
            // default value
            $default = $describe['dflt_value'];
            
            // others
            $extras = [];
            
            //値の生成
            $info[$column] = [
                'table'       => $table,
                'name'        => $column,
                'type'        => $type,
                'length'      => $length,
                'attribute'   => $attribute ? $attribute : null,
                'null'        => $nullable,
                'primary_key' => $is_primary_key,
                'default'     => $default,
                'extras'      => $extras,
            ];
        }
        
        return $info;
    }
}

