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

class RestoreDbTask extends Task
{

    private $dir;
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
        $restoreGZ = $this->getDir() . DIRECTORY_SEPARATOR . $this->taoDbConfig->getDbName() . '.gz';

        switch ($this->taoDbConfig->getDbDriver()) {
            case 'pdo_pgsql':

                $command = vsprintf("gunzip -c %s | psql %s --host=%s --username=%s --no-password",
                    [
                        $restoreGZ,
                        $this->taoDbConfig->getDbName(),
                        $this->taoDbConfig->getDbHost(),
                        $this->taoDbConfig->getDbUser(),
                    ]);

                break;
            case 'pdo_mysql':

                $command = vsprintf("gunzip -c %s | mysql -u %s -p%s %s",
                    [
                        $restoreGZ,
                        $this->taoDbConfig->getDbUser(),
                        $this->taoDbConfig->getDbPass(),
                        $this->taoDbConfig->getDbName(),
                    ]);
                break;
            default:
                throw new BuildException('DB Driver is not supported');
                break;
        }
        if ($command) {
            $execTask->setCommand($command);
            $execTask->main();
            $this->log(sprintf('Backup for %s restored at %s', $this->taoDbConfig->getDbName(), $restoreGZ));
        }

    }

    /**
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param mixed $dir
     */
    public function setDir($dir)
    {
        $this->dir = $dir;
    }


}