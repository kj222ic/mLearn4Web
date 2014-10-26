<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 16:09
 */

class mLearn4WebAPI {
    private static $api = "http://celtest1.lnu.se:3030/mlearn4web/";


    /**
     * @return string
     */
    private static function getApi(){return self::$api;}

    public function doCall($method,$param = null){
        return json_decode(file_get_contents($this->getApi().$method."/".$param));
    }

   /* public function getMediaFromScenario($id){
        $this->doCall("getalldata",$id);
    }*/

    public function getScenarios(){
        return $this->doCall("getall");
    }

    public function getScenario($id){
        return $this->doCall("get",$id);
    }

    public function getAllDataFromScenario(){
        return $this->doCall("getalldata");
    }

    public function getDataFromScenario($id){
        return $this->doCall("getscenariodata",$id);
    }
    public function __construct(){
    }
} 