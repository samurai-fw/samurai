<?php

namespace spec\Samurai\Onikiri;

use spec\Samurai\Onikiri\PHPSpecContext;
use Samurai\Onikiri\Database;
use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Driver\MysqlDriver;

class OnikiriSpec extends PHPSpecContext
{
    public function let()
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Onikiri');
    }

    public function it_configurates()
    {
        $config = $this->configure();
        $config->shouldHaveType('Samurai\Onikiri\Configuration');
    }
    
    public function it_imports_database_configuration_file()
    {
        $this->configure();

        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('base');
        $database->getUser()->shouldBe('some');
        $database->getHostName()->shouldBe('localhost.localdomain');
    }

    public function it_gets_database_instance()
    {
        $this->configure();

        $config = __DIR__ . '/Fixtures/databases.yml';
        $this->import($config);

        $database = $this->getDatabase('admin');
        $database->shouldHaveType('Samurai\Onikiri\Database');
        $database->getHostName()->shouldBe('admin.localdomain');
    }

    public function it_gets_table_instance()
    {
        $config = $this->configure();
        $config->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');

        $userTable = $this->getTable('User');
        $userTable->shouldHaveType(__NAMESPACE__ . '\\Fixtures\\UserTable');
    }

    public function it_gets_table_instance_as_factory()
    {
        $config = $this->configure();
        $config->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');

        $t1 = $this->getTable('User');
        $t2 = $this->getTable('User');
        $t1->shouldBe($t2);
    }

    public function it_throws_entity_table_not_found_exception_when_not_exists_alias()
    {
        $config = $this->configure();
        $config->addModelDir(__DIR__ . '/Fixtures', __NAMESPACE__ . '\\Fixtures');

        $this->shouldThrow('Samurai\\Onikiri\\Exception\\EntityTableNotFoundException')->duringGetTable('Foo');
    }


    public function it_gets_transaction()
    {
        $this->getTx()->shouldHaveType('Samurai\Onikiri\Transaction');
    }

    public function it_is_transaction_nesting(Connection $c)
    {
        $tx = $this->getTx()->begin();
        $tx->setConnection($c);

        $tx2 = $this->getTx()->begin();
        $tx->shouldBe($tx2);
        
        $c->inTx()->willReturn(false);
        $c->beginTransaction()->shouldBeCalled();

        $tx2->beginTransaction();
        $c->inTx()->willReturn(true);

        $tx2->commit();

        $tx->inTx()->shouldBe(true);
        $tx2->inTx()->shouldBe(true);
        
        $tx->beginTransaction();
        
        $c->commit()->shouldBeCalled();

        $tx->commit();
        
        $tx->inTx()->shouldBe(false);
        $tx2->inTx()->shouldBe(false);
    }


    public function it_gets_table_schema(Database $d, Connection $c, MysqlDriver $md)
    {
        $this->_attachMySQLDefinitionFromEnv($d);
        $this->setDatabase('sandbox', $d);
        $d->connect()->willReturn($c);
        $d->getDriver()->willReturn($md);
        $d->pickSlave()->willReturn($d);
        $md->getTableDescribe($c, 'user')->willReturn([
            'id' => [
                'table' => 'user',
                'name' => 'id',
                'type' => 'int',
                'length' => '11',
                'attribute' => 'unsigned',
                'null' => false,
                'primary_key' => true,
                'default' => null,
                'extras' => ['auto_increment'],
            ],
            'name' => [
                'table' => 'user',
                'name' => 'name',
                'type' => 'varchar',
                'length' => '255',
                'attribute' => null,
                'null' => false,
                'primary_key' => false,
                'default' => 'who',
                'extras' => [],
            ],
        ]);

        $this->configure();
        $schema = $this->getTableSchema('user', 'sandbox');
        $schema->shouldHaveType('Samurai\Onikiri\Schema\TableSchema');
        $columns = $schema->getColumns();
        $columns['id']->getName()->shouldBe('id');
        $columns['id']->getDefaultValue()->shouldBe(null);
        $columns['name']->getName()->shouldBe('name');
        $columns['name']->getDefaultValue()->shouldBe('who');
    }
}

