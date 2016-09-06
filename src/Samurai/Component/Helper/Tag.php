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

/**
 * TAG
 *
 * @package     Samurai
 * @subpackage  Component.Helper
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Tag
{
    /**
     * tag name
     *
     * @var     string
     */
    public $tag;

    /**
     * attributes
     *
     * @var     array
     */
    public $attributes = [];

    /**
     * contents
     *
     * @var     array
     */
    public $contents = [];

    /**
     * close mode
     *
     * @var     int
     */
    public $closeMode = self::CLOSE_NORMAL;

    /**
     * close mode mapping
     *
     * @var     array
     */
    protected $closeModeMapping = [
        'br' => self::SELF_CLOSE,
        'hr' => self::SELF_CLOSE,
        'img' => self::SELF_CLOSE,
        'link' => self::SELF_CLOSE,
        'meta' => self::SELF_CLOSE,
        'input' => self::SELF_CLOSE,
        'form' => self::DONT_CLOSE,
    ];

    /**
     * close mode const
     *
     * @const   int
     */
    const CLOSE_NORMAL = 1;     // <div></div>
    const DONT_CLOSE = 2;       // <form>
    const CLOSE_ONLY = 3;       // </form>
    const SELF_CLOSE = 4;       // <br />


    /**
     * constructor
     *
     * @param   string  $name
     * @param   array   $attributes
     */
    public function __construct($name, array $attributes = [])
    {
        $this->tag = $name;
        $this->closeMode = $this->detectCloseMode($name);

        foreach ($attributes as $name => $value)
            $this->setAttribute($name, $value);
    }


    /**
     * set text content
     *
     * @param   string  $text
     * @return  Tag
     */
    public function setText($text)
    {
        $this->clearContents();
        return $this->addText($text);
    }
    
    /**
     * add text content
     *
     * @param   string  $text
     * @return  Tag
     */
    public function addText($text)
    {
        $text = htmlspecialchars($text);
        $this->contents[] = $text;
        return $this;
    }
    
    /**
     * set html content
     *
     * @param   string  $html
     * @return  Tag
     */
    public function setHTML($html)
    {
        $this->clearContents();
        return $this->addHTML($html);
    }
    
    /**
     * add html content
     *
     * @param   string  $html
     * @return  Tag
     */
    public function addHTML($html)
    {
        $this->contents[] = $html;
        return $this;
    }

    /**
     * add child tag
     *
     * @param   Tag     $tag
     * @return  Tag
     */
    public function addChild(Tag $tag)
    {
        $this->contents[] = $tag;
    }

    /**
     * clear contents
     */
    public function clearContents()
    {
        $this->contents = [];
    }


    /**
     * set attribute
     *
     * @param   string  $name
     * @param   string  $value
     * @return  Tag
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = [$value];
        return $this;
    }

    /**
     * add attributes
     *
     * @param   string  $name
     * @param   string  $value
     * @return  Tag
     */
    public function addAttributes($name, $value)
    {
        if (empty($this->attributes[$name]))
            $this->attributes[$name] = [];

        $this->attributes[$name][] = $value;

        return $this;
    }

    /**
     * make attribute for tag
     *
     * @param   string  $name
     * @param   array   $values
     * @return  string
     */
    protected function makeAttribute($name, array $values)
    {
        $attr = $name . '="';

        switch (true)
        {
            case $values === [true]:
                $attr = $name;
                return $attr;
                break;
            case $name === 'style':
            case strpos($name, 'on') === 0:
                $attr .= htmlspecialchars(join(';', $values));
                break;
            default:
                $attr .= htmlspecialchars(join(' ', $values));
                break;
        }

        $attr .= '"';
        return $attr;
    }


    /**
     * make html
     *
     * @return  string
     */
    public function make()
    {
        if ($this->closeMode === self::CLOSE_ONLY)
            return '</' . $this->tag . '>';

        $join = function($tag, $last = null) {
            if ($last)
                $tag[] = $last;
            return join('', $tag);
        };

        $tag = [];
        $tag[] = '<' . $this->tag;

        foreach ($this->attributes as $name => $values)
        {
            if ($values === [null])
                continue;

            $tag[] = ' ' . $this->makeAttribute($name, $values);
        }

        if ($this->closeMode === self::SELF_CLOSE)
            return $join($tag, ' />');
        else
            $tag[] = '>';

        if ($this->closeMode === self::DONT_CLOSE)
            return $join($tag);

        foreach ($this->contents as $content)
        {
            if ($content instanceof Tag)
                $tag[] = $content->make();
            else
                $tag[] = $content;
        }

        return $join($tag, '</' . $this->tag . '>');
    }


    /**
     * auto detect closing mode by tag name
     *
     * @param   string  $name
     * @return  string
     */
    public function detectCloseMode($name)
    {
        $name = strtolower($name);
        return isset($this->closeModeMapping[$name]) ? $this->closeModeMapping[$name] : self::CLOSE_NORMAL;
    }


    /**
     * to string
     *
     * @return  string
     */
    public function __toString()
    {
        return $this->make();
    }

    /**
     * call missing method bridge to setAttribute
     *
     * @param   string  $method
     * @param   array   $args
     * @return  Tag
     */
    public function __call($method, $args)
    {
        $this->setAttribute($method, array_shift($args));
        return $this;
    }
    
    /**
     * call missing member bridge to setAttribute
     *
     * @param   string  $key
     * @param   mixed   $value
     * @return  Tag
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}

