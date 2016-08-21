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

namespace Samurai\Samurai\Component\Validate;

use Samurai\Raikiri\DependencyInjectable;
use Samurai\Samurai\Component\Validate\Validator\Validator as ValidatorUnit;

/**
 * validator
 *
 * @package     Samurai
 * @subpackage  Component.Validate
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Validator
{
    /**
     * @traits
     */
    use DependencyInjectable;


    /**
     * validate
     *
     * @param   mixed   $value
     * @param   mixed   $validator
     * @param   mixed   $attributes
     * @param   boolean $negative
     */
    public function validate($value, $validator, $attributes)
    {
        $validator = $this->getValidator($validator);

        $result = $validator->validate($value, $attributes);

        return $negative ? ! $result : $result;
    }


    /**
     * get validator
     *
     * @param   mixed   $validator
     * @return  Validator
     */
    public function getValidator($name)
    {
        if ($name instanceof ValidatorUnit) return $name;

        $fileName = 'Component/Validate/Validator/' . ucfirst($name) . 'Validator.php';
        $filePath = $this->loader->findFirst($fileName);
        if (! $filePath) throw new \InvalidArgumentException('validator not found. -> ' . $name);

        $class = $filePath->getClassName();
        $validator = new $class();

        return $validator;
    }
}

