<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx\Db;

use Phinx\Db\Adapter\MysqlAdapter;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Prophecy\Argument;

class TableSpec extends PHPSpecContext
{
    public function let(MysqlAdapter $a)
    {
        $a->getColumnTypes()->willReturn(['integer']);
        $this->beConstructedWith('foo', [], $a);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Table');
        $this->getName()->shouldBe('foo');
    }


    public function it_is_add_column_bridge(MysqlAdapter $a)
    {
        //$a->isValidColumnType(Argument::any())->willReturn(true);

        $c = $this->column('id', 'integer');
        $c->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Column');

        $columns = $this->getPendingColumns();
        $columns[0]->shouldBe($c);
    }


    public function it_sets_comment()
    {
        $this->setComment('hogehogehoge')->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Table');
        $this->getComment()->shouldBe('hogehogehoge');
    }


    public function it_sets_primary_key()
    {
        $this->setPrimaryKey('id');
        $o = $this->getOptions();
        $o['primary_key']->shouldBe(['id']);

        $this->setPrimaryKey('id', 'sub_id');
        $o = $this->getOptions();
        $o['primary_key']->shouldBe(['id', 'sub_id']);
    }
}

