<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\Migration\Phinx\Config;
use Symfony\Component\Console\Output\OutputInterface;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class ManagerSpec extends PHPSpecContext
{
    public function let(Config $c, OutputInterface $o)
    {
        $c->hasEnvironment('development')->willReturn(true);
        $c->getEnvironment('development')->willReturn([
            'adapter' => 'mysql',
            'host' => 'localhost',
            'user' => 'foo',
            'pass' => '',
            'charset' => 'utf8',
            'name' => 'sandbox',
        ]);

        $this->beConstructedWith($c, $o);
    }


    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Manager');
    }


    public function it_gets_environment()
    {
        $e = $this->getEnvironment('development');
        $e->getAdapter()->shouldHaveType('Samurai\Samurai\Component\Migration\Phinx\Adapter\MysqlAdapter');
    }
}

