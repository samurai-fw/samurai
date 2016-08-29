<?php

namespace spec\Samurai\Onikiri;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Connection;

class TransactionSpec extends PHPSpecContext
{
    public function let(Connection $c)
    {
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Onikiri\Transaction');
    }


    public function it_sets_connection(Connection $c)
    {
        $this->setConnection($c);
        $this->getConnections()->shouldHaveValue($c);
    }


    public function it_begins()
    {
        $this->begin()->shouldBe($this);
        $this->inTx()->shouldBe(true);
    }
    
    public function it_begins_transaction(Connection $c)
    {
        $this->setConnection($c);
        
        $c->inTx()->willReturn(false);
        $c->beginTransaction()->shouldBeCalled();

        $this->begin();
        $this->beginTransaction();
    }
    
    public function it_commits(Connection $c)
    {
        $this->begin();
        $this->inTx()->shouldBe(true);

        $this->setConnection($c);
        $c->beginTransaction()->shouldBeCalled();
        $c->commit()->shouldBeCalled();
        $c->inTx()->willReturn(false);

        $this->beginTransaction();
        $c->inTx()->willReturn(true);

        $this->commit();
        $c->inTx()->willReturn(false);

        $this->inTx()->shouldBe(false);
    }
    
    
    public function it_rollbacks(Connection $c)
    {
        $this->begin();
        $this->inTx()->shouldBe(true);

        $this->setConnection($c);
        $c->beginTransaction()->shouldBeCalled();
        $c->rollback()->shouldBeCalled();
        $c->inTx()->willReturn(false);

        $this->beginTransaction();
        $c->inTx()->willReturn(true);

        $this->shouldThrow('Samurai\Onikiri\Exception\TransactionFailedException')->duringRollback();
        $c->inTx()->willReturn(false);

        $this->inTx()->shouldBe(false);
    }
    
    
    public function getMatchers()
    {
        return [
            'haveValue' => function($subject, $key) {
                return in_array($key, $subject, true);
            }
        ];
    }
}

