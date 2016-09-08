<?php

namespace spec\Samurai\Samurai\Component\Routing;

use Closure;
use Samurai\Samurai\Component\Routing\Router;
use Samurai\Samurai\Component\Routing\ActionCaller;
use Samurai\Samurai\Component\Routing\Exception\NotFoundException;
use Samurai\Samurai\Controller\Controller;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Raikiri\Container;
use Samurai\Samurai\Component\Request\HttpRequest;
use Samurai\Samurai\Component\Core\ActionChain;

class RouterSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(Router::class);
    }

    public function it_is_root_controller()
    {
        $this->get('/', function(){
            return 'foo';
        });

        $action = $this->dispatch('/');
        $action->shouldHaveType(ActionCaller::class);
        $action->execute()->shouldBe('foo');
    }

    public function it_throws_not_found_exception_when_matched_routing()
    {
        $this->shouldThrow(NotFoundException::class)
            ->duringDispatch('/user/index');
    }

    public function it_is_match_routing()
    {
        $this->match('/foo/bar', 'get|post|delete', function()
        {
            return 'zoo';
        });

        $this->dispatch('/foo/bar', 'GET')
            ->shouldHaveType(ActionCaller::class);
        $this->shouldThrow(NotFoundException::class)
            ->duringDispatch('/foo/bar', 'PUT');
    }
    
    public function it_is_any_routing()
    {
        $this->any('/foo/bar', function()
        {
            return 'zoo';
        });

        $this->dispatch('/foo/bar', 'GET')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/bar', 'POST')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/bar', 'PUT')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/bar', 'PATCH')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/bar', 'DELETE')
            ->shouldHaveType(ActionCaller::class);
    }

    public function it_is_controller_routing()
    {
        $this->controller('/foo', 'spec\Samurai\Samurai\Component\Routing\ExampleController');

        $this->dispatch('/foo/index')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/index', 'POST')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/show')
            ->shouldHaveType(ActionCaller::class);
        $this->shouldThrow(NotFoundException::class)
            ->duringDispatch('/foo/output');
    }


    public function it_is_restful_controller()
    {
        $this->controller('/foo', 'spec\Samurai\Samurai\Component\Routing\ExampleController');

        $this->dispatch('/foo/index')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/index', 'POST')
            ->shouldHaveType(ActionCaller::class);
        $this->dispatch('/foo/show')
            ->shouldHaveType(ActionCaller::class);
        $this->shouldThrow(NotFoundException::class)
            ->duringDispatch('/foo/output');
    }


    /*
    public function let(HttpRequest $r, ActionChain $a)
    {
        $this->setRoot('default.index');

        $c = $this->getContainer();
        $c->register('request', $r);
        $c->register('actionChain', $a);

        $r->getAll()->willReturn([]);    
    }


    public function it_is_requested_root(HttpRequest $r, ActionChain $a)
    {
        $r->getPath()->willReturn('/');
        $a->existsController('default', 'index')->willReturn(true);

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\RootRule');

        $route->getController()->shouldBe('default');
        $route->getAction()->shouldBe('index');
    }

    public function it_is_requested_no_match(HttpRequest $r)
    {
        $r->getPath()->willReturn('/favicon.ico');

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\NotFoundRule');

        $route->getController()->shouldBe('error');
        $route->getAction()->shouldBe('notFound');
    }
    
    public function it_is_requested_controller_not_exists(HttpRequest $r, ActionChain $a)
    {
        $r->getPath()->willReturn('/user/show');
        $a->existsController('user', 'show')->willReturn(false);

        $route = $this->routing();
        $route->shouldHaveType('Samurai\Samurai\Component\Routing\Rule\Rule');

        $route->getController()->shouldBe('error');
        $route->getAction()->shouldBe('notFound');
    }
     */
}


class ExampleController extends Controller
{
    public function indexAction()
    {
        return 'index';
    }
    public function showAction()
    {
        return 'show';
    }
}

