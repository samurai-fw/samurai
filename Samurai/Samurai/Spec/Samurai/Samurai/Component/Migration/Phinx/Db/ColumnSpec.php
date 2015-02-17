<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx\Db;

use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Prophecy\Argument;

class ColumnSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Column');
    }


    public function it_sets_table(Table $t)
    {
        $this->setTable($t)->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Column');
        $this->getTable()->shouldBe($t);
    }

    public function it_sets_collation()
    {
        $this->setCollation('utf8_general_ci')->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Column');
        $this->getCollation()->shouldBe('utf8_general_ci');
    }

    public function it_sets_charset()
    {
        $this->setCharset('ascii')->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Db\Column');
        $this->getCharset()->shouldBe('ascii');
    }


    public function it_bridges_undefined_method_to_table(Table $t, Column $c)
    {
        $t->column('foo', 'integer')->willReturn($c);
        $this->setTable($t);
        $this->column('foo', 'integer')->shouldBe($c);
    }
}

