<?php

require_once "phing/types/DataType.php";

class TaoConfig extends DataType
{
    /**
     * @var generisConfig
     */
    private $taoDbConfig;
    /**
     * @var TaoDbConfig
     */
    private $generisConfig;

    private $login;
    private $pass;

    public function addGenerisConfig(generisConfig $generisConfig)
    {
        $this->generisConfig = $generisConfig;
    }

    public function addTaoDbConfig(TaoDbConfig $taoDbConfig)
    {
        $this->taoDbConfig = $taoDbConfig;
    }


    public function setLogin($login)
    {
        $this->login = $login;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
    }


    public function toArray()
    {

        $returnValue = array(

            "user_login"     => $this->login,
            "user_pass1"     => $this->pass,
            "user_email"     => "",
            "user_firstname" => "",
            "user_lastname"  => "",
            "import_local"   => true,


        );

        return array_merge($this->taoDbConfig->toArray(), $this->generisConfig->toArray(), $returnValue);
    }
}
