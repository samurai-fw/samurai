<?php

namespace spec\Samurai\Samurai\Task;

use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;

class SampleTaskListSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Samurai\Samurai\Task\SampleTaskList');
    }


    public function it_is_usage_text_from_doc_comment()
    {
        $usage = <<<'EOL'
something do.

[usage]
    $ ./app sample:some

[options]
    --usage          show help.

EOL;
        $this->get('some')->getOption()->usage()->shouldBe($usage);
    }
}


/**
 * dummy sample task.
 */
namespace Samurai\Samurai\Task;

use Samurai\Samurai\Component\Task\TaskList;

class SampleTaskList extends TaskList
{
    /**
     * something do.
     *
     * [usage]
     *     $ ./app sample:some
     *
     * [options]
     *     --usage          show help.
     *
     * @access  public
     */
    public function someTask()
    {
    }
}


