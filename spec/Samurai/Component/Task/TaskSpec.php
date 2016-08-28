<?php

namespace spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Task\TaskList;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use ReflectionMethod;

class TaskSpec extends PHPSpecContext
{
    public function let(TaskList $list, ReflectionMethod $method)
    {
        $this->beConstructedWith($list, $method);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\Task');
    }
}

