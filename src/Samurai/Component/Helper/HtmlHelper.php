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
 * HTML helper component.
 *
 * @package     Samurai
 * @subpackage  Component.Helper
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class HtmlHelper
{
    /**
     * stylesheet
     *
     * @param  string $src
     * @param  array  $attributes
     * @return Tag
     */
    public function stylesheet($src, $attributes = [])
    {
        $attributes['rel'] = 'stylesheet';
        $attributes['href'] = $src;
        $tag = new Tag('link', $attributes);
        return $tag;
    }

    /**
     * script
     *
     * @param  string $src
     * @param  array  $attributes
     * @return Tag
     */
    public function script($src, $attributes = [])
    {
        $attributes['type'] = 'text/javascript';
        $attributes['src'] = $src;
        $tag = new Tag('script', $attributes);
        return $tag;
    }

    /**
     * img
     *
     * @param  string $src
     * @param  array  $attributes
     * @return Tag
     */
    public function img($src, $attributes = [])
    {
        $attributes['src'] = $src;
        $tag = new Tag('img', $attributes);
        return $tag;
    }
    
    
    /**
     * link
     *
     * @param   string  $url
     * @param   string  $title
     * @param   array   $attributes
     * @return  Tag
     */
    public function link($url, $title, $attributes = [])
    {
        $attributes['href'] = $url;
        $tag = new Tag('a', $attributes);
        $tag->setText($title);
        return $tag;
    }


    /**
     * span tag render
     *
     * @param  string $value
     * @param  array  $attributes
     * @return Tag
     */
    public function span($value, $attributes = [])
    {
        $tag = new Tag('span', $attributes);
        $tag->setText($value);
        return $tag;
    }


    /**
     * h[n]
     *
     * @param   int     $level
     * @param   string  $value
     * @param   array   $attributes
     * @return  Tag
     */
    public function h($level, $value, $attributes = [])
    {
        $tag = new Tag('h' . $level, $attributes);
        $tag->setText($value);
        return $tag;
    }

    /**
     * h1
     *
     * @param  string $value
     * @param  array  $attributes
     * @return Tag
     */
    public function h1($value, $attributes = [])
    {
        return $this->h(1, $value, $attributes);
    }

    /**
     * h2
     *
     * @param  string $value
     * @param  array  $attributes
     * @return Tag
     */
    public function h2($value, $attributes = [])
    {
        return $this->h(2, $value, $attributes);
    }

    /**
     * h3
     *
     * @param  string $value
     * @param  array  $attributes
     * @return Tag
     */
    public function h3($value, $attributes = [])
    {
        return $this->h(3, $value, $attributes);
    }

    /**
     * h4
     *
     * @param  string $value
     * @param  array  $attributes
     * @return Tag
     */
    public function h4($value, $attributes = [])
    {
        return $this->h(4, $value, $attributes);
    }
}

