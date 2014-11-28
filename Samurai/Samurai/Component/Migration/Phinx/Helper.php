<?php

namespace Samurai\Samurai\Component\Migration\Phinx;

use Samurai\Samurai\Component\FileSystem\Directory;

/**
 * migration system phinx helper.
 *
 * @package     Samurai
 * @subpackage  Component.Migration.Phinx
 * @copyright   2007-2013, Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://opensource.org/licenses/MIT
 */
class Helper
{

    /**
     * migration name adjusting
     *
     * "-" (underbar), "-" (hyphen) replace to CamelCase
     *
     * @param   string  $name
     * @return  string
     */
    public function nameStrategy($name)
    {
        $name = join('', array_map('ucfirst', explode('_', str_replace('-', '_', $name))));
        return $name;
    }


    /**
     * migration file name adjusting
     *
     * add version prefix (YmdHis + index)
     *
     * @param   string  $database
     * @param   string  $name
     * @return  string
     */
    public function fileNameStrategy($database, $name)
    {
        $name = join('_', array_map('lcfirst', preg_split('/(?=[A-Z])/', $name)));
        return $database . DS . $this->generateVersion() . $name . '.php';
    }


    /**
     * migration class name adjusting
     *
     * @param   string  $database
     * @param   string  $name
     * @return  string
     */
    public function classNameStrategy($database, $name)
    {
        return $name;
    }


    /**
     * migration namespace adjusting
     *
     * @param   Samurai\Samurai\Component\FileSystem\Directory  $dir
     * @param   string                                          $database
     * @param   string                                          $name
     * @return  string
     */
    public function namespaceStrategy(Directory $dir, $database, $name)
    {
        return '';
    }


    /**
     * generate version
     *
     * @return  string
     */
    public function generateVersion()
    {
        return sprintf('%s', date('YmdHis'));
    }
}
