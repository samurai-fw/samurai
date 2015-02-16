<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx\Db;

use Phinx\Db\Adapter\MysqlAdapter;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

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


    public function it_is_add_column_bridge()
    {
        $this->column('id', 'integer')->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Table');
        $columns = $this->getPendingColumns();
        $columns[0]->getName()->shouldBe('id');
    }


    public function it_sets_comment()
    {
        $this->setComment('hogehogehoge')->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Table');
        $this->getComment()->shouldBe('hogehogehoge');
    }
}

