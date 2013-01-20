<?php
/**
 * PHP version 5.
 *
 * Copyright (c) Samurai Framework Project, All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 *     * Neither the name of the Samurai Framework Project nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
 * LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Samurai
 * @copyright   Samurai Framework Project
 * @link        http://samurai-fw.org/
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */

/**
 * task: database: migrate: revert
 * 
 * @package     Samurai
 * @subpackage  Task.Database
 * @copyright   Samurai Framework Project
 * @author      KIUCHI Satoshinosuke <scholar@hayabusa-lab.jp>
 * @license     http://www.opensource.org/licenses/bsd-license.php The BSD License
 */
class Samurai_Task_Database_Migrate_Revert extends Samurai_Task_Database_Migrate
{
    /**
     * @dependencies
     */


    /**
     * constructor.
     *
     * @access     public
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * @implements
     */
    public function prepare()
    {
        // load already version.
        $aliases = ActiveGateway::getManager()->getAliases();
        foreach ( $aliases as $alias ) {
            $this->_loadAlreadyVersions($alias);
        }

        // load migrations.
        $files = $this->_getMigrationFiles();
        $this->_migrations = $files;

        // nothing to do.
        if ( ! $this->_migrations ) {
            $this->flushMessage('Nothing to migrate.');
            return false;
        }
    }
    
    
    /**
     * @implements
     */
    public function execute()
    {
        foreach ( $this->_migrations as $info ) {
            require_once $info['path'];
            $migration = new $info['class']();
            $migration->setReporter($this);
            $migration->revert();
        }
        $this->flushMessage('');
    }




    
    /**
     * get migration files.
     *
     * @access  protected
     * @return  array
     */
    protected function _getMigrationFiles()
    {
        $files = array();
        $helper = ActiveGateway::getManager()->getHelper();
        $dir = Samurai_Loader::getPath(Samurai_Config::get('generator.directory.migration'), true);

        // redo step
        $step = $this->getOption('step', 1);
        $revert_versions = array_slice($this->_alreadys, 0 - $step);

        $migration_files = $helper->getMigrationFiles($dir);
        foreach ( $migration_files as $file ) {
            if ( in_array($file['version'], $revert_versions) ) {
                $files[] = $file;
            }
        }

        // reverse
        $files = array_reverse($files);

        return $files;
    }
}

