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

use Samurai\Samurai\Component\Task\TaskList;
use Samurai\Samurai\Component\Task\Option;
use Samurai\Samurai\Component\Migration\Phinx\Manager;
use Samurai\Samurai\Component\Migration\Phinx\Config;
use Samurai\Onikiri\Database;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class DbTaskList extends TaskList
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
        $databases = $this->getDatabases($option);

        $start = microtime(true);
        foreach ($databases as $alias => $database) {
            $manager = $this->getManager($alias, $database);
            $manager->migrate($this->application->getEnv(), $option->getArg(0));
        }
        $end = microtime(true);

        $this->sendMessage('');
        $this->sendMessage('All Done. Took %.4fs', $end - $start);

        $this->task('db:schema:dump', $option->copy());
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
        $databases = $this->getDatabases($option);

        $start = microtime(true);
        foreach ($databases as $alias => $database) {
            $manager = $this->getManager($alias, $database);
            $manager->rollback($this->application->getEnv(), $option->getArg(0));
        }
        $end = microtime(true);

        $this->sendMessage('');
        $this->sendMessage('All Done. Took %.4fs', $end - $start);
        
        $this->task('db:schema:dump', $option->copy());
    }


    /**
     * database reset task. using phinx.
     * rollback to "0"
     *
     * usage:
     *     $ ./app db:reset
     *
     * @option      database,d=all      target database (default is all databases).
     */
    public function resetTask(Option $option)
    {
        $option->setArg(0, 0);
        $this->task('db:rollback', $option);
    }


    /**
     * database refresh task.
     * call db:reset and db:migrate
     *
     * usage:
     *     $ ./app db:refresh
     *
     * @option      database,d=all      target database (default is all databases).
     * @option      seed,s              with call db:seed task.
     */
    public function refreshTask(Option $option)
    {
        $this->task('db:reset', $option->copy());
        $this->task('db:migrate', $option->copy());

        if ($option->get('seed')) {
            $this->task('db:seed', $option->copy());
        }
    }

    
    /**
     * database seeding task.
     *
     * usage:
     *     # all seeding files import.
     *     $ ./app db:seed
     *
     *     # target seeding file import.
     *     $ ./app db:seed [name] --database=base
     *
     * seeder file:
     *     App/Database/Seed/[database alias]/[name]Seeder.php
     *
     * @option      database,d=all      target database (default is all databases).
     */
    public function seedTask(Option $option)
    {
        $name = $option->getArg(0);
        $databases = $this->getDatabases($option);
        
        $start = microtime(true);
        foreach ($databases as $alias => $database) {
            foreach ($this->migrationHelper->getSeeders($alias, $name) as $seeder) {
                try {
                    $this->sendMessage('seeding... -> %s', $seeder->getName());
                    $seeder->seed();
                } catch (\Exception $e) {
                    $this->sendMessage($e->getMessage());
                    $this->sendMessage('has error. aborting.');
                    return;
                }
            }
        }
        $end = microtime(true);

        $this->sendMessage('');
        $this->sendMessage('All Done. Took %.4fs', $end - $start);
    }


    /**
     * show database mgration status task. using phinx.
     *
     * usage:
     *     $ ./app db:status [version]
     *
     * @option      format,f            output format. (default is text)
     * @option      database,d=all      target database (default is all databases).
     */
    public function statusTask(Option $option)
    {
        $databases = $this->getDatabases($option);

        foreach ($databases as $alias => $database) {
            $manager = $this->getManager($alias, $database);
            $manager->printStatus($this->application->getEnv(), $option->get('format'));
        }
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

        $config = new Config([]);
        $config->setContainer($this->raikiri());
        $config->initialize($alias, $database);

        $env = $config->getEnvironment($this->application->getEnv());
        $reporter->writeln('<info>using migration path</info> ' . $config->getMigrationPath());
        $reporter->writeln('<info>using environment</info> ' . $this->application->getEnv());
        $reporter->writeln('<info>using adapter</info> ' . $env['adapter']);
        $reporter->writeln('<info>using database</info> ' . $env['name']);

        $manager = new Manager($config, new ArrayInput([]), $reporter);
        $manager->setContainer($this->raikiri());
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


    /**
     * get target databases
     *
     * @param   Samurai\Samurai\Component\Task\Option   $option
     * @return  array
     */
    protected function getDatabases(Option $option)
    {
        if ($option->get('database') === 'all') {
            $databases = $this->onikiri->getDatabases();
        } else {
            $alias = $option->get('database');
            $databases = [$alias => $this->onikiri->getDatabase($alias)];
        }
        return $databases;
    }
}

