<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx\Adapter;

use Samurai\Onikiri\Connection;
use Samurai\Onikiri\Statement;
use Samurai\Samurai\Component\Migration\Phinx\Db\Table;
use Samurai\Samurai\Component\Migration\Phinx\Db\Column;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Symfony\Component\Console\Output\OutputInterface;
use PhpSpec\Exception\Example\SkippingException;
use Prophecy\Argument;

class MysqlAdapterSpec extends PHPSpecContext
{
    /**
     * @dependencies
     */
    public $request;


    public function let(OutputInterface $o, Connection $con, Statement $st)
    {
        $op = [
            'name' => 'sandbox',
            'host' => 'localhost',
            'user' => 'foo',
            'pass' => '',
        ];
        $this->beConstructedWith($op, $o);

        $con->exec(Argument::any())->willReturn(0);
        $con->query(Argument::any())->willReturn($st);

        $this->setConnection($con);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Adapter\MysqlAdapter');
    }


    public function it_gets_column_sql_definition(Column $c)
    {
        $this->_prepareColumn($c);
        $c->getType()->willReturn('integer');
        $c->getLimit()->willReturn(11);
        $c->isSigned()->willReturn(false);
        $c->isIdentity()->willReturn(true);

        $this->getColumnSqlDefinition($c)->shouldBe("INT(11) unsigned NOT NULL AUTO_INCREMENT");
    }

    public function it_gets_column_sql_definition_with_charset(Column $c)
    {
        $this->_prepareColumn($c);
        $c->getCharset()->willReturn('ascii');

        $this->getColumnSqlDefinition($c)->shouldBe("VARCHAR(256) CHARACTER SET ascii NOT NULL");
    }
    
    public function it_gets_column_sql_definition_with_collation(Column $c)
    {
        $this->_prepareColumn($c);
        $c->getCollation()->willReturn('utf8_general_ci');

        $this->getColumnSqlDefinition($c)->shouldBe("VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
    }

    public function it_gets_column_sql_definition_notnull_and_default_empty_string(Column $c, Connection $con)
    {
        $this->_prepareColumn($c);
        $c->getDefault()->willReturn('');
        
        $con->quote(Argument::any())->willReturn("''");

        $this->getColumnSqlDefinition($c)->shouldBe("VARCHAR(256) NOT NULL DEFAULT ''");
    }


    /**
     * column setup
     *
     * @param   Samurai\Samurai\Component\Migration\Phinx\Db\Column     $c
     */
    private function _prepareColumn($c)
    {
        $c->getName()->willReturn('foo');
        $c->getType()->willReturn('string');
        $c->getLimit()->willReturn(256);
        $c->getPrecision()->willReturn(null);
        $c->getScale()->willReturn(null);
        $c->getValues()->willReturn([]);
        $c->isSigned()->willReturn(false);
        $c->isNull()->willReturn(false);
        $c->isIdentity()->willReturn(false);
        $c->getDefault()->willReturn(null);
        $c->getComment()->willReturn(null);
        $c->getUpdate()->willReturn(null);
        $c->getCollation()->willReturn(null);
        $c->getCharset()->willReturn(null);
    }
}

