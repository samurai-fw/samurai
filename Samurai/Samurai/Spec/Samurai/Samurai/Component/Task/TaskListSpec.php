<?php

namespace Samurai\Samurai\Spec\Samurai\Samurai\Component\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class TaskListSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Component\Task\TaskList');
    }
}

