<?php
/**
 * The MIT License
 *
 * Copyright (c) 2007-2013, Samurai Framework Project, All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 *
 * @package     Samurai
 * @copyright   2007-2013, Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://opensource.org/licenses/MIT
 */

namespace Samurai\Samurai\Component\Helper;

use Samurai\Onikiri\Entity;

/**
 * Form helper component.
 *
 * @package     Samurai
 * @subpackage  Component.Helper
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class FormHelper
{
    /**
     * model
     *
     * @var     Entity
     */
    protected $model;


    /**
     * open form tag.
     *
     * @param   string  $url
     * @param   array   $attributes
     * @return  Tag
     */
    public function open($url, array $attributes = [])
    {
        $tag = new Tag('form', $attributes);
        $tag->action = $url;
        $tag->method = 'POST';
        return $tag;
    }

    /**
     * open form tag using model
     *
     * @param   Entity  $model
     * @param   string  $url
     * @param   array   $attributes
     * @return  Tag
     */
    public function model(Entity $model, $url, array $attributes = [])
    {
        $this->model = $model;

        $tag = $this->open($url, $attributes);
        return $tag;
    }

    /**
     * close form tag.
     *
     * @return  Tag
     */
    public function close()
    {
        $tag = new Tag('form');
        $tag->closeMode = Tag::CLOSE_ONLY;

        $this->model = null;

        return $tag;
    }


    /**
     * input
     *
     * @param   string  $type
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function input($type, $name, $value = null, array $attributes = [])
    {
        $tag = new Tag('input', $attributes);
        $tag->type = $type;
        $tag->name = $name;
        $tag->value = $value;
        return $tag;
    }

    /**
     * hidden tag
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function hidden($name, $value = null, array $attributes = [])
    {
        $value = $this->getDefaultValue($name, $value);
        return $this->input('hidden', $name, $value, $attributes);
    }
    
    /**
     * text input tag
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function text($name, $value = null, array $attributes = [])
    {
        $value = $this->getDefaultValue($name, $value);
        return $this->input('text', $name, $value, $attributes);
    }
    
    /**
     * date input tag
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function date($name, $value = null, array $attributes = [])
    {
        $value = $this->getDefaultValue($name, $value);
        return $this->input('date', $name, $value, $attributes);
    }

    /**
     * number input
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function number($name, $value = null, array $attributes = [])
    {
        $value = $this->getDefaultValue($name, $value);
        return $this->input('number', $name, $value, $attributes);
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @param   boolean $checked
     * @param   array   $attributes
     * @return  Tag
     */
    public function checkbox($name, $value, $checked = false, array $attributes = [])
    {
        $tag = $this->input('checkbox', $name, $value, $attributes);
        
        if ($checked)
            $tag->checked = 'checked';

        return $tag;
    }

    /**
     * @param   string  $name
     * @param   string  $value
     * @param   boolean $checked
     * @param   array   $attributes
     * @return  Tag
     */
    public function radio($name, $value, $checked = false, array $attributes = [])
    {
        $tag = $this->input('radio', $name, $value, $attributes);
        
        $default = $this->getDefaultValue($name);
        if ($default !== null)
            $checked = $default == $value;

        if ($checked)
            $tag->checked = 'checked';

        return $tag;
    }

    /**
     * textarea
     *
     * @param   string  $name
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function textarea($name, $value = null, array $attributes = [])
    {
        $tag = new Tag('textarea', $attributes);
        $tag->name = $name;
        $tag->setText($this->getDefaultValue($name, $value));
        return $tag;
    }

    /**
     * select
     *
     * @param   string  $name
     * @param   array   $options
     * @param   mixed   $selected
     * @return  Tag
     */
    public function select($name, array $options, $selected = null, array $attributes = [])
    {
        $tag = new Tag('select', $attributes);
        $tag->name = $name;
        
        if ($selected === null)
            $selected = $this->getDefaultValue($name);

        foreach ($options as $value => $label)
        {
            $option = new Tag('option');
            $option->value = $value;
            $option->setText($label);

            if ($selected !== null && $value == $selected)
                $option->selected = true;

            $tag->addChild($option);
        }

        return $tag;
    }

    /**
     * submit
     *
     * @param   string  $label
     * @param   array   $attributes
     * @return  Tag
     */
    public function submit($label, array $attributes = [])
    {
        return $this->input('submit', null, $label, $attributes);
    }


    /**
     * Label
     *
     * @param   string  $for
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function label($for, $label, array $attributes = [])
    {
        $tag = new Tag('label', $attributes);
        $tag->for = $for;
        $tag->setText($label);
        return $tag;
    }


    /**
     * get default value
     *
     * 1. from argument
     * 2. from request
     * 3. from model
     *
     * @param   string  $name
     * @param   string  $value
     * @return  mixed
     */
    protected function getDefaultValue($name, $value = null)
    {
        if ($value !== null)
            return $value;

        if ($this->model && $this->model->has($name))
            return $this->model->get($name);

        return $value;
    }
}

