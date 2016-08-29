<?php

namespace spec\Samurai\Onikiri\Driver;

use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Statement;
use PhpSpec\Exception\Example\SkippingException;
use Prophecy\Argument;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SqliteDriverSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Driver\SqliteDriver');
    }
    
    
    public function it_connects_to_sqlite(Database $d)
    {
        $d->isUseMemory()->willReturn(true);
        $d->getDatabaseName()->willReturn(':memory:');
        $d->getOptions()->willReturn([]);

        $connection = $this->connect($d);
        $connection->shouldHaveType('Samurai\Onikiri\Connection');
        $connection->shouldHaveType('PDO');
    }


    public function it_gets_table_describe(Connection $c, Statement $s)
    {
        $c->query(Argument::any())->willReturn($s);
        $s->fetchAll(Argument::any())->willReturn([
            ['cid' => 0, 'name' => 'id', 'type' => 'INTEGER', 'pk' => '1', 'notnull' => '1', 'dflt_value' => null],
            ['cid' => 1, 'name' => 'name', 'type' => 'VARCHAR(256)', 'pk' => '0', 'notnull' => '0', 'dflt_value' => "foo"],
        ]);

        $describe = $this->getTableDescribe($c, 'foo');

        $describe['id']->shouldBeLike([
            'table' => 'foo',
            'name' => 'id',
            'type' => 'INTEGER',
            'length' => null,
            'attribute' => null,
            'null' => false,
            'primary_key' => true,
            'default' => null,
            'extras' => [],
        ]);
        $describe['name']->shouldBeLike([
            'table' => 'foo',
            'name' => 'name',
            'type' => 'VARCHAR',
            'length' => 256,
            'attribute' => null,
            'null' => true,
            'primary_key' => false,
            'default' => 'foo',
            'extras' => [],
        ]);
    }
}

