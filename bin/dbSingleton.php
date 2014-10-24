<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 28.05.12
 * Time: 15:31
 * To change this template use File | Settings | File Templates.
 */

class dbSingleton{

    static private $instance = null;
    public $connection = null;

    private function __construct(){}

    public function __destruct(){
        mysql_close(self::getInstance()->connection);
    }

    static public function getInstance(){
        if (null === self::$instance){

            self::$instance = new self;

            self::$instance->connection = mysql_connect(Config::$HOST,Config::$USERNAME,Config::$PASSWORD);
            if (!self::$instance->connection) {
                die('Database connection not possible@db(Host:'.Config::$HOST.'): ' . mysql_error());
            }
            //Database
            mysql_select_db(Config::$DB);
            //UTF-8
            mysql_query("SET NAMES 'utf8'");

        }
        return self::$instance;
    }
}
