<?php

namespace Samurai\Samurai\Component\Spec\PHPSpec;

use PhpSpec\Runner\Maintainer\Maintainer;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\Specification;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;

class ApplicationMaintainer implements Maintainer
{
    /**
     * samurai di container.
     *
     * @var     Samurai\Raikiri/Container
     */
    public $Container;

    /**
     * {@inheritdoc}
     */
    public function supports(ExampleNode $example)
    {
        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, Specification $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        // di container injection.
        if ($context instanceof \Samurai\Samurai\Component\Spec\Context\PHPSpecContext) {
            $context->__setContainer($this->Container);
            $this->Container->injectDependency($context);
        }
    }


    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, Specification $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }


    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 15;
    }
}

