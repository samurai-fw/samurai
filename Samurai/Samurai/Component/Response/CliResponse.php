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

namespace Samurai\Samurai\Component\Response;

/**
 * Response for Cli.
 *
 * @package     Samurai
 * @subpackage  Component.Response
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class CliResponse extends HttpResponse
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * output contents
     */
    public function execute()
    {
        $this->sendBody();
    }


    /**
     * send body content
     */
    private function sendBody()
    {
        $content = $this->body->getContent();
        $this->send($content);
    }


    /**
     * send simple stdout.
     *
     * @param   string  $line
     */
    public function send($line, $eol = PHP_EOL)
    {
        echo $line . $eol;
    }

    
    /**
     * ask
     *
     * @param   string|array    $message
     * @param   boolean         $secret
     * @param   string          $default
     */
    public function ask($message, $secret = false, $default = '')
    {
        $message = is_array($message) ? call_user_func_array('sprintf', $message) : $message;

        $this->send($message);
        $answer = $this->readline($secret);
        
        return $answer === '' ? $default : $answer;
    }

    /**
     * confirmation
     *
     * @param   string|array    $message
     * @param   array           $choices
     * @param   string          $default
     */
    public function confirmation($message, $choices = ['y' => true, 'n' => false], $default = false)
    {
        $message = is_array($message) ? call_user_func_array('sprintf', $message) : $message;

        $answers = [];
        foreach ($choices as $answer => $value) {
            $answers[$answer] = $answer;
            if ($value === $default) $answers[$answer] = ucfirst($answer);
        }
        
        $this->send(sprintf('%s [%s]: ', $message, join('/', $answers)), '');
        $answer = $this->readline();
        if ($answer === '') return $default;

        if (array_key_exists($answer, $choices)) {
            return $choices[$answer];
        } else {
            foreach ($choices as $key => $value) {
                if ($answer === substr($key, 0, strlen($answer))) return $value;
            }
            return $default;
        }
    }


    /**
     * read stdin
     *
     * @param   boolean $secret
     * @return  string
     */
    public function readline($secret = false)
    {
        $line = null;

        if ($secret) {
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                system('stty -echo');
                $line = trim(fgets(STDIN));
                system('stty echo');
            }
        }
        
        $line = $line !== null ? $line : trim(fgets(STDIN));

        return $line;
    }



    /**
     * {@inheritdoc}
     */
    public function isHttp()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isHttps()
    {
        return false;
    }
}

