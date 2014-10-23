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
    public static function getApi(){return self::$api;}

    private function doCall($method,$param = null){
        return file_get_contents($this->getApi().$method."/".$param);
    }

    public function getMediaFromScenario($id){
        $this->doCall("get",$id);
    }
} 