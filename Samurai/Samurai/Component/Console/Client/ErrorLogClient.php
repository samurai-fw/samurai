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

namespace Samurai\Samurai\Component\Console\Client;

use Samurai\Samurai\Component\Core\Accessor;

/**
 * console error log client
 *
 * @package     Samurai
 * @subpackage  Component.Console
 * @copyright   2007-2013, Samurai Framework Project
 * @license     http://opensource.org/licenses/MIT
 */
class ErrorLogClient extends Client
{
    /**
     * traits
     */
    use Accessor;

    /**
     * @var int log level
     */
    public $log_level;

    /**
     * {@inheritdoc}
     */
    public function send($level, $message)
    {
        if (! $this->isEnoughLogLevel($level)) return;

        $message = sprintf('[%s]: %s', $this->levelToString($level), $this->wrapping($message));

        error_log($message);
    }

    private function isEnoughLogLevel($level)
    {
        if (! $this->log_level) $this->setLogLevel(self::LOG_LEVEL_WARN);

        return $level >= $this->log_level;
    }
}
