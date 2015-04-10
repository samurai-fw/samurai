<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class CodeGeneratorSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\CodeGenerator');
    }


    public function it_generates_table_option(Table $t)
    {
        $t->getOptions()->willReturn([]);
        $this->generateTableOptions($t)->shouldBe('');
        
        $t->getOptions()->willReturn(['id' => 'foo_id']);
        $this->generateTableOptions($t)->shouldBe(", ['id' => 'foo_id']");
        
        $t->getOptions()->willReturn(['id' => false, 'primary_key' => ['id', 'foo_id']]);
        $this->generateTableOptions($t)->shouldBe(", ['id' => false, 'primary_key' => ['id', 'foo_id']]");
    }
    
    
    public function it_generates_column_option(Column $c)
    {
        $keys = ['length', 'default', 'null', 'precision', 'scale', 'after', 'update', 'comment'];
        $c->getLimit()->willReturn(256);
        $c->getDefault()->willReturn('aaaa');
        $c->getNull()->willReturn(true);
        $c->getPrecision()->willReturn(null);
        $c->getScale()->willReturn(null);
        $c->getAfter()->willReturn(null);
        $c->getUpdate()->willReturn(null);
        $c->getComment()->willReturn('bbbb');

        $this->generateColumnOptions($c)->shouldBe(", ['length' => 256, 'default' => 'aaaa', 'null' => true, 'comment' => 'bbbb']");
    }
}

