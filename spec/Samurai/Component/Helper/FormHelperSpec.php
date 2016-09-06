<?php

namespace spec\Samurai\Samurai\Component\Helper;

use Samurai\Samurai\Component\Helper\FormHelper;
use Samurai\Samurai\Component\Spec\Context\PHPSpecContext;
use Samurai\Onikiri\Entity;

class FormHelperSpec extends PHPSpecContext
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(FormHelper::class);
    }


    public function it_opens_form()
    {
        $this->open('http://example.jp/foo/bar/zoo')
            ->make()
            ->shouldBe('<form action="http://example.jp/foo/bar/zoo" method="POST">');
    }

    public function it_close_form()
    {
        $this->close()
            ->make()
            ->shouldBe('</form>');
    }

    public function it_makes_input_tag()
    {
        $this->input('text', 'name', 'value')
            ->make()
            ->shouldBe('<input type="text" name="name" value="value" />');
    }

    public function it_makes_input_hidden_tag()
    {
        $this->hidden('name', 'value')
            ->make()
            ->shouldBe('<input type="hidden" name="name" value="value" />');
    }
    
    public function it_makes_input_text_tag()
    {
        $this->text('name', 'value')
            ->make()
            ->shouldBe('<input type="text" name="name" value="value" />');
    }
    
    public function it_makes_input_date_tag()
    {
        $this->date('name', 'value')
            ->make()
            ->shouldBe('<input type="date" name="name" value="value" />');
    }
    
    public function it_makes_input_number_tag()
    {
        $this->number('name', 'value')
            ->make()
            ->shouldBe('<input type="number" name="name" value="value" />');
    }

    public function it_makes_input_checkbox_tag()
    {
        $this->checkbox('name', 'value')
            ->make()
            ->shouldBe('<input type="checkbox" name="name" value="value" />');
        
        // checked
        $this->checkbox('name', 'value', true)
            ->make()
            ->shouldBe('<input type="checkbox" name="name" value="value" checked="checked" />');
    }
    
    public function it_makes_input_radio_tag()
    {
        $this->radio('name', 'value')
            ->make()
            ->shouldBe('<input type="radio" name="name" value="value" />');
        
        // checked
        $this->radio('name', 'value', true)
            ->make()
            ->shouldBe('<input type="radio" name="name" value="value" checked="checked" />');
    }
    
    public function it_makes_textarea_tag()
    {
        $this->textarea('name', 'value')
            ->make()
            ->shouldBe('<textarea name="name">value</textarea>');

        // html escaped
        $this->textarea('name', 'value<b>bold</b>')
            ->make()
            ->shouldBe('<textarea name="name">value&lt;b&gt;bold&lt;/b&gt;</textarea>');
    }
    
    public function it_makes_select_tag()
    {
        $this->select('name', ['item1', 'item2', 'item3'])
            ->make()
            ->shouldBe('<select name="name">'
                . '<option value="0">item1</option>'
                . '<option value="1">item2</option>'
                . '<option value="2">item3</option>'
                . '</select>');
        
        // selected
        $this->select('name', ['item1', 'item2', 'item3'], 2)
            ->make()
            ->shouldBe('<select name="name">'
                . '<option value="0">item1</option>'
                . '<option value="1">item2</option>'
                . '<option value="2" selected>item3</option>'
                . '</select>');
    }

    public function it_makes_submit_tag()
    {
        $this->submit('submit!!')
            ->make()
            ->shouldBe('<input type="submit" value="submit!!" />');
    }

    public function it_makes_label_tag()
    {
        $this->label('some', 'label')
            ->make()
            ->shouldBe('<label for="some">label</label>');
    }


    public function it_opens_form_by_model(Entity $model)
    {
        $model->get('id')->willReturn(10);
        $model->has('name')->willReturn(true);
        $model->get('name')->willReturn('John Doe');
        $model->has('description')->willReturn(true);
        $model->get('description')->willReturn('a long long text.');

        $this->model($model, 'http://example.jp/foo/bar/zoo')
            ->make()
            ->shouldBe('<form action="http://example.jp/foo/bar/zoo" method="POST">');

        $this->text('name')
            ->make()
            ->shouldBe('<input type="text" name="name" value="John Doe" />');
        $this->textarea('description')
            ->make()
            ->shouldBe('<textarea name="description">a long long text.</textarea>');
    }
}

