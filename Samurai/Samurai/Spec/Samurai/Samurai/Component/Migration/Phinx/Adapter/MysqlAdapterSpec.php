<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx\Adapter;

use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSpec\Exception\Example\SkippingException;

class MysqlAdapterSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $request;


    public function let(OutputInterface $o)
    {
        $op = [
            'name' => 'sandbox',
            'host' => 'localhost',
            'user' => 'foo',
            'pass' => '',
        ];
        $this->beConstructedWith($op, $o);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Adapter\MysqlAdapter');
    }


    public function it_gets_column_sql_definition(Column $c)
    {
        $c->getName()->willReturn('foo');
        $c->getType()->willReturn('integer');
        $c->getLimit()->willReturn(11);
        $c->getPrecision()->willReturn(null);
        $c->getScale()->willReturn(null);
        $c->isSigned()->willReturn(false);
        $c->isNull()->willReturn(false);
        $c->isIdentity()->willReturn(true);
        $c->getDefault()->willReturn(null);
        $c->getComment()->willReturn(null);
        $c->getUpdate()->willReturn(null);
        $c->getCollation()->willReturn(null);
        $c->getCharset()->willReturn(null);

        $this->getColumnSqlDefinition($c)->shouldBe("INT(11) unsigned NOT NULL AUTO_INCREMENT");
    }

    public function it_gets_column_sql_definition_with_charset(Column $c)
    {
        $c->getName()->willReturn('foo');
        $c->getType()->willReturn('string');
        $c->getLimit()->willReturn(256);
        $c->getPrecision()->willReturn(null);
        $c->getScale()->willReturn(null);
        $c->isSigned()->willReturn(false);
        $c->isNull()->willReturn(false);
        $c->isIdentity()->willReturn(false);
        $c->getDefault()->willReturn(null);
        $c->getComment()->willReturn(null);
        $c->getUpdate()->willReturn(null);
        $c->getCollation()->willReturn(null);
        $c->getCharset()->willReturn('ascii');

        $this->getColumnSqlDefinition($c)->shouldBe("VARCHAR(256) CHARACTER SET ascii NOT NULL");
    }
    
    public function it_gets_column_sql_definition_with_collation(Column $c)
    {
        $c->getName()->willReturn('foo');
        $c->getType()->willReturn('string');
        $c->getLimit()->willReturn(256);
        $c->getPrecision()->willReturn(null);
        $c->getScale()->willReturn(null);
        $c->isSigned()->willReturn(false);
        $c->isNull()->willReturn(false);
        $c->isIdentity()->willReturn(false);
        $c->getDefault()->willReturn(null);
        $c->getComment()->willReturn(null);
        $c->getUpdate()->willReturn(null);
        $c->getCollation()->willReturn('utf8_general_ci');
        $c->getCharset()->willReturn(null);

        $this->getColumnSqlDefinition($c)->shouldBe("VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
    }
}

