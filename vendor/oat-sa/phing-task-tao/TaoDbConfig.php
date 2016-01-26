<?php

require_once "phing/types/DataType.php";

class TaoDbConfig extends DataType
{

    private $dbDriver;
    private $dbHost = 'localhost';
    private $dbName;
    private $dbUser;
    private $dbPass;
    /**
     * @var bool
     */
    private $taoInstalled = true;
    /**
     * @var array
     */
    private $params = [];
    private $taoPath;

    /**
     * Retrieve params from exist tao instance
     * @return array
     * @throws Exception
     */
    public function getParams()
    {
        $path = $this->taoPath . DIRECTORY_SEPARATOR . 'tao' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'raw_start.php';

        if ( ! is_file($path)) {
            throw new Exception('DB config auto integration require a tao package install should be found ' . $path);
        }

        require_once $path;

        if ( ! $this->params) {
            $persistence   = \common_persistence_Manager::getPersistence('default');
            $schemaManager = $persistence->getDriver()->getSchemaManager();

            $reflectionMethod = (new ReflectionMethod(get_class($schemaManager), 'getDriver'));
            $reflectionMethod->setAccessible(true);
            $this->params = $reflectionMethod->invoke($schemaManager)->getParams();

        }


        return $this->params;
    }

    /**
     * @return boolean
     */
    public function getTaoInstalled()
    {
        return $this->taoInstalled;
    }

    /**
     * @param boolean $taoInstalled
     */
    public function setTaoInstalled($taoInstalled)
    {
        $this->taoInstalled = $taoInstalled;
    }

    public function setDbDriver($driver)
    {
        $this->dbDriver = $driver;
    }

    public function setDbHost($host)
    {
        $this->dbHost = $host;
    }

    public function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    public function setDbUser($user)
    {
        $this->dbUser = $user;
    }

    public function setDbPass($pass)
    {
        $this->dbPass = $pass;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'db_driver' => $this->getDbDriver($this->getProject()),
            'db_host'   => $this->getDbHost($this->getProject()),
            'db_name'   => $this->getDbName($this->getProject()),
            'db_user'   => $this->getDbUser($this->getProject()),
            'db_pass'   => $this->getDbPass($this->getProject()),
        ];
    }

    /**
     * @param Project $p = null
     *
     * @return string
     * @throws BuildException
     * @throws Exception
     */
    public function getDbDriver(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getDbDriver($p);
        }

        if ($this->taoInstalled) {
            $this->setDbDriver($this->getParams()['driver']);
        }

        return $this->dbDriver;
    }

    /**
     * @param Project $p = null
     *
     * @return string
     * @throws BuildException
     * @throws Exception
     */
    public function getDbHost(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getDbHost($p);
        }

        if ($this->taoInstalled) {
            $this->setDbHost($this->getParams()['host']);
        }

        return $this->dbHost;
    }

    /**
     * @param Project $p = null
     *
     * @return string
     * @throws BuildException
     * @throws Exception
     */
    public function getDbName(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getDbName($p);
        }
        if ($this->taoInstalled) {
            $this->setDbName($this->getParams()['dbname']);
        }

        return $this->dbName;
    }

    /**
     * @param Project $p = null
     *
     * @return string
     * @throws BuildException
     * @throws Exception
     */
    public function getDbUser(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getDbUser($p);
        }
        if ($this->taoInstalled) {
            $this->setDbUser($this->getParams()['user']);
        }

        return $this->dbUser;
    }

    /**
     * @param Project $p = null
     *
     * @return string
     * @throws BuildException
     * @throws Exception
     */
    public function getDbPass(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getDbPass($p);
        }
        if ($this->taoInstalled) {
            $this->setDbPass($this->getParams()['password']);
        }

        return $this->dbPass;
    }

    /**
     * @param Project $p = null
     *
     * @throws BuildException
     * @return string
     */
    public function getTaoPath(Project $p = null)
    {
        if ($this->isReference()) {
            return $this->getRef($this->getProject())->getTaoPath($p);
        }

        return $this->taoPath;
    }

    /**
     * @param mixed $taoPath
     */
    public function setTaoPath($taoPath)
    {
        $this->taoPath = $taoPath;
    }

    /**
     * @param Project $p
     *
     * @return TaoDbConfig
     * @throws BuildException
     */
    public function getRef(Project $p = null)
    {
        if ( ! $this->checked) {
            $stk = array();
            array_push($stk, $this);
            $this->dieOnCircularReference($stk, $p);
        }
        $o = $this->ref->getReferencedObject($p);
        if ( ! ( $o instanceof TaoDbConfig )) {
            throw new BuildException($this->ref->getRefId() . " doesn't denote a TaoDbConfig");
        } else {
            return $o;
        }
    }

}