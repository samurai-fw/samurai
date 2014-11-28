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

namespace Samurai\Console\Task;

use Samurai\Samurai\Component\Task\Task;
use Samurai\Samurai\Component\Task\Option;
use Samurai\Samurai\Component\Migration\Phinx\Manager;
use Samurai\Samurai\Component\Migration\Phinx\Config;
use Samurai\Onikiri\Database;
use Symfony\Component\Console\Output\ConsoleOutput;

class DbTaskList extends Task
{
    /**
     * database migration task. using phinx.
     *
     * usage:
     *     $ ./app db:migrate [version]
     *
     * @option      database,d=all      target database (default is all databases).
     */
    public function migrateTask(Option $option)
    {
        if ($option->get('database') === 'all') {
            $databases = $this->onikiri->getDatabases();
        } else {
            $alias = $option->get('database');
            $databases = [$alias => $this->onikiri->getDatabase($alias)];
        }

        $start = microtime(true);
        foreach ($databases as $alias => $database) {
            $manager = $this->getManager($alias, $database);
            $manager->migrate($this->application->getEnv(), $option->getArg(0));
        }
        $end = microtime(true);

        $this->sendMessage('');
        $this->sendMessage('All Done. Took %.4fs', $end - $start);
    }

    
    /**
     * database rollback task. using phinx.
     *
     * usage:
     *     $ ./app db:rollback [version]
     *
     * @option      database,d=all      target database (default is all databases).
     */
    public function rollbackTask(Option $option)
    {
        if ($option->get('database') === 'all') {
            $databases = $this->onikiri->getDatabases();
        } else {
            $alias = $option->get('database');
            $databases = [$alias => $this->onikiri->getDatabase($alias)];
        }

        $start = microtime(true);
        foreach ($databases as $alias => $database) {
            $manager = $this->getManager($alias, $database);
            $manager->rollback($this->application->getEnv(), $option->getArg(0));
        }
        $end = microtime(true);

        $this->sendMessage('');
        $this->sendMessage('All Done. Took %.4fs', $end - $start);
    }

    /**
     * get migration manager.
     *
     * @param   string                      $alias
     * @param   Samurai\Onikiri\Database    $database
     * @return  Samurai\Samurai\Component\Migration\Phinx\Manager
     */
    public function getManager($alias, Database $database)
    {
        $reporter = $this->getReporter();

        $config = new Config();
        $config->setContainer($this->raikiri());
        $config->initialize($alias, $database);

        $env = $config->getEnvironment($this->application->getEnv());
        $reporter->writeln('<info>using migration path</info> ' . $config->getMigrationPath());
        $reporter->writeln('<info>using environment</info> ' . $this->application->getEnv());
        $reporter->writeln('<info>using adapter</info> ' . $env['adapter']);
        $reporter->writeln('<info>using database</info> ' . $env['name']);

        $manager = new Manager($config, $reporter);
        return $manager;
    }


    /**
     * get symfony output interface
     *
     * @return  Symfony\Component\Console\Output\ConsoleOutput
     */
    public function getReporter()
    {
        $output = new ConsoleOutput();
        $output->setDecorated(false);
        return $output;
    }

}
