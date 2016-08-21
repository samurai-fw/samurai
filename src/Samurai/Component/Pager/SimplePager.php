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

namespace Samurai\Samurai\Component\Pager;

use Samurai\Samurai\Component\Core\Accessor;

/**
 * simple pager
 *
 * @package     Samurai
 * @subpackage  Component.Pager
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class SimplePager
{
    /**
     * total
     *
     * @var     int
     */
    public $total = 0;

    /**
     * now
     *
     * @var     int
     */
    public $now = 1;

    /**
     * per page
     *
     * @var     int
     */
    public $per_page = 10;

    /**
     * @traits
     */
    use Accessor;


    /**
     * pages
     *
     * @return  array
     */
    public function pages()
    {
        return range(1, $this->last());
    }


    /**
     * now page
     *
     * @return  int
     */
    public function now()
    {
        return $this->now;
    }

    /**
     * next page
     *
     * @return  int
     */
    public function next()
    {
        $next = $this->now() + 1;
        return $next > $this->last() ? null : $next;
    }

    /**
     * prev page
     *
     * @return  int
     */
    public function prev()
    {
        $prev = $this->now() - 1;
        return $prev < 1 ? null : $prev;
    }

    /**
     * last page
     *
     * @return  int
     */
    public function last()
    {
        return (int) ceil($this->total / $this->per_page);
    }


    /**
     * has next
     *
     * @return  boolean
     */
    public function hasNext()
    {
        return $this->next() !== null;
    }

    /**
     * has prev
     *
     * @return  boolean
     */
    public function hasPrev()
    {
        return $this->prev() !== null;
    }


    /**
     * sliding
     *
     * @param   int     $items
     * @return  array
     */
    public function sliding($items = 5)
    {
        $now = $this->now();
        $last = $this->last();
        if ($items < 1) $items = 1;
        if ($items > $last) $items = $last;
        $pages = [$now];

        $i = 1;
        while (count($pages) < $items) {
            $next = $now + $i;
            if ($next <= $last) {
                $pages[] = $next;
                if (count($pages) >= $items) break;
            }

            $prev = $now - $i;
            if ($prev > 0) {
                $pages[] = $prev;
                if (count($pages) >= $items) break;
            }

            $i++;
        }
        sort($pages);

        return $pages;
    }
}

