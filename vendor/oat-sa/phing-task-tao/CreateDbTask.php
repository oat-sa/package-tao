<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */

require_once "phing/Task.php";
require_once "phing/tasks/system/ExecTask.php";

class CreateDbTask extends Task
{

    /**
     * @var TaoDbConfig
     */
    private $taoDbConfig;

    public function addTaoDbConfig(TaoDbConfig $db)
    {
        $this->taoDbConfig = $db;
    }

    /**
     * The main entry point method.
     */
    public function main()
    {
        $command  = null;
        $execTask = new ExecTask();
        $execTask->setProject($this->getProject());

        switch ($this->taoDbConfig->getDbDriver()) {
            case 'pdo_pgsql':
                $command = vsprintf("createdb %s --host=%s --username=%s --no-password",
                    [
                        $this->taoDbConfig->getDbName(),
                        $this->taoDbConfig->getDbHost(),
                        $this->taoDbConfig->getDbUser(),
                    ]);

                break;
            case 'pdo_mysql':

                $command = vsprintf("echo 'create database %s  | mysql -u%s -p%s",
                    [
                        $this->taoDbConfig->getDbName(),
                        $this->taoDbConfig->getDbUser(),
                        $this->taoDbConfig->getDbPass(),
                    ]);
                break;
            default:
                throw new BuildException('DB Driver is not supported');
                break;
        }
        if ($command) {
            $execTask->setCommand($command);
            $execTask->main();
            $this->log(sprintf('Database %s has been created', $this->taoDbConfig->getDbName()));
        }

    }

}