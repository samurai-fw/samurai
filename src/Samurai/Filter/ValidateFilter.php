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

namespace Samurai\Samurai\Filter;

/**
 * Validate filter.
 *
 * some.action:
 *   body.require: input body
 *   body.max_length:20: body is max 20 length. 
 *
 * @package     Samurai
 * @subpackage  Filter
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class ValidateFilter extends Filter
{
    /**
     * {@inheritdoc}
     */
    public function prefilter()
    {
        foreach ($this->getAttributes() as $key => $message) {
            list($key, $validator, $attributes, $negative) = $this->resolveKey($key);

            $result = $this->validator->validate($this->request->get($key), $validator, $attributes);
            if ($negative) $result = ! $result;

            if (! $result) {
                $this->errorList->setType('failedValidate');
                $this->errorList->add($key, $message);
            }
        }
    }


    /**
     * key resolve
     *
     * key.validator:attribute
     * key.!validator:attribute
     *
     * @param   string  $key
     */
    protected function resolveKey($key)
    {
        list($key, $validates) = explode('.', $key);

        $validates = explode(':', $validates);
        $validator = $validates[0];
        $attributes = count($validates) > 1 ? explode(';', $validates[1]) : [];

        $negative = false;
        if ($validator[0] === '!') {
            $negative = true;
            $validator = substr($validator, 1);
        }

        return [$key, $validator, $attributes, $negative];
    }
}

