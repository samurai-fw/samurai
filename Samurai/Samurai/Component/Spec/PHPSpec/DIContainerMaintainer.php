<?php

namespace Samurai\Samurai\Component\Spec\PHPSpec;

use PhpSpec\Runner\Maintainer\MaintainerInterface;
use PhpSpec\Loader\Node\ExampleNode;
use PhpSpec\SpecificationInterface;
use PhpSpec\Runner\MatcherManager;
use PhpSpec\Runner\CollaboratorManager;
use Samurai\Raikiri\Container;
use Samurai\Raikiri\Container\NullableContainer;

class DIContainerMaintainer implements MaintainerInterface
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
        return $this->isUseableRaikiri($example->getSpecification()->getResource()->getSrcClassname());
    }


    /**
     * {@inheritdoc}
     */
    public function prepare(ExampleNode $example, SpecificationInterface $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
        $c = new NullableContainer('spec');
        $context->container = $c->inherit($this->Container);
            
        // console component is disable in spec context
        $c->remove('console')->register('console', $c->get('__undefined'));
    }


    /**
     * {@inheritdoc}
     */
    public function teardown(ExampleNode $example, SpecificationInterface $context, MatcherManager $matchers, CollaboratorManager $collaborators)
    {
    }


    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 14;
    }
    
    
    /**
     * useable raikiri ?
     *
     * @param   string  $class
     * @return  boolean
     */
    public function isUseableRaikiri($class)
    {
        $traits = [];
        do {
            $traits = array_merge($traits, class_uses($class));
        } while ($class = get_parent_class($class));
        foreach ($traits as $trait) {
            $traits = array_merge($traits, class_uses($trait));
        }

        return in_array('Samurai\\Raikiri\\DependencyInjectable', $traits);
    }
}

