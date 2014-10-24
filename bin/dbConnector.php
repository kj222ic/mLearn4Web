<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 28.05.12
 * Time: 14:27
 * To change this template use File | Settings | File Templates.
 */

include_once("dbSingleton.php");

class dbConnector
{
    protected $connection;

    public function __construct(){
        $this->connection = dbSingleton::getInstance();
    }

    public function __destruct(){
    }

    /**
     *
     * @param String $method Name of the Method e.c getRoleByName
     * @param array $param Array with parameters for the Method
     * @return array|mixed
     */
    public function doSql($method, $param = array()){
        if ($this->connection) {
            if (isset($method)) {
                try {
                    if ($param == 0 || !empty($param)) {
                        if (is_array($param)) {
                            foreach ($param as &$value) {
                                $this->escapeString($value);
                            }
                        } else {
                            $this->escapeString($param);
                        }
                        return call_user_func_array(array($this, $method), $param);
                    }else{
                        return call_user_func(array($this, $method));
                    }
                }catch (Exception $e) {
                    echo $e;
                }
            }
        } else {
            die("No Connection to Database");
        }
        return false;
    }

    public function escapeString()
    {
        $stack = debug_backtrace();
        if (isset($stack[0]["args"])) {
            for ($i = 0; $i < count($stack[0]["args"]); $i++) {
                if (is_bool($stack[0]["args"][$i]) === TRUE) {
                    continue;
                }
                if (get_magic_quotes_gpc()) {
                    $stack[0]["args"][$i] = stripslashes($stack[0]["args"][$i]);
                }
                if (phpversion() >= '4.3.0') {
                    $stack[0]["args"][$i] = mysql_real_escape_string($stack[0]["args"][$i]);
                }
                /*else{
                    $stack[0]["args"][$i] = mysql_escape_string($stack[0]["args"][$i]);
                }*/
            }
        }
    }

    private function getRoleByName($name){
        $sql = "SELECT * FROM Role WHERE Role.name = '$name'";
        return $this->fetchAssoc($sql);
    }

    /**
     * @description Runs a SQL and fetch the result to an array
     * @param $sql
     * @return array
     */
    private function fetchAssoc($sql){
        $retArray = array();
        $qry = mysql_query($sql);
        if ($qry) {
            while ($res = mysql_fetch_assoc($qry)) {
                $retArray[] = $res;
            }
            return $retArray;
        }
        return $qry;
    }

    private function getRoleById($id){
        $sql = "SELECT * FROM Role WHERE Role.Role_id = '$id'";
        return $this->fetchAssoc($sql);
    }

    private function getAccountByProfileId($profileId){
        $sql = "SELECT * FROM Account INNER JOIN Account_Role INNER JOIN Role WHERE
                  Account.Profile_id = '$profileId'";
        return $this->fetchAssoc($sql);
    }

    private function addAccount($profileID,$name, $email,$roleID){
        if(isset($profileID) && is_string($name) && isset($roleID)){
            $sql = "INSERT INTO Account (Profile_id,name,email)
                    VALUES ($profileID,'$name','$email')";
            if(mysql_query($sql)){
                $accID = mysql_insert_id();
                return $this->addAccountRole($accID,$roleID);;
            }
        }
        return false;
    }

    private function addAccountRole($accID,$roleID){
        $sql = "INSERT INTO Account_Role (Account_id,Role_id)
                        VALUES ('$accID','$roleID')";
        return mysql_query($sql);
    }


    /********************************
     * EXAMPLES
     ******************************/

    private function getCategoryAll(){
        $retArray = array();
        $sql = "SELECT * FROM category";
        $qry = mysql_query($sql);

        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function getCategory($column,$value){
        $retArray = array();
        $sql = "SELECT * FROM category WHERE category.category_".$column." = '$value'";
        $qry = mysql_query($sql);
        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function addCategory($name,$pos){
        if(is_string($name) && is_integer($pos)){
            $sql = "INSERT INTO category (category_name,category_pos)
                    VALUES ('$name','$pos');";
            return mysql_query($sql);
        }
        return false;
    }

    private function updateCategory($column,$value,$id){
        $sql = "UPDATE category SET category_".$column." =  '$value' WHERE category.category_id = $id";
        return mysql_query($sql);
    }

    private function delCategory($id){
        if(isset($id)){
            $sql = "DELETE category,category_picture,picture FROM category
                    LEFT JOIN category_picture ON category.category_id = category_picture.category_id
                    LEFT JOIN picture ON category_picture.picture_id = picture.picture_id
                    WHERE category.category_id = '$id'";
            return mysql_query($sql);

        }
        return false;
    }

    private function getPictureAll(){
        $retArray = array();
        $sql = "SELECT * FROM picture";
        $qry = mysql_query($sql);

        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function getPicture($column,$value){
        $retArray = array();
        $sql = "SELECT * FROM picture WHERE picture.picture_".$column." = '$value'";
        $qry = mysql_query($sql);

        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function addPicture($name,$filename,$pos,$cId){
        if(is_string($name) && isset($filename) && isset($pos)){
            $sql = "INSERT INTO picture (picture_name,picture_filename,picture_pos)
                    VALUES ('$name','$filename','$pos')";
            if(mysql_query($sql)){
                $pId = mysql_insert_id();
                $sql = "INSERT INTO category_picture (category_id,picture_id)
                        VALUES ('$cId','$pId')";
                return mysql_query($sql);
            }
        }
        return false;
    }

    private function delPicture($id){
        if(isset($id)){
            $sql = "DELETE picture FROM picture
                    WHERE picture.picture_id = '$id'";
            return mysql_query($sql);
        }
        return false;
    }

    private function updatePicture($column,$value,$id){
        $sql = "UPDATE picture SET picture_".$column." =  '$value' WHERE picture.picture_id = $id";
        return mysql_query($sql);
    }

    private function getCategoryPicture($cId=null,$pId=null){
        $retArray = array();
        if(isset($cId) && isset($pId)){
            $sql = "SELECT * FROM category_picture
                    LEFT JOIN (category,picture)
                    ON (
                        category_picture.category_id = category.category_id
                        AND category_picture.picture_id = picture.picture_id)
                    WHERE category.category_id='$cId'
                    AND picture.picture_id ='$pId'";
            $qry = mysql_query($sql);
        }elseif(isset($cId) && !isset($pId)){
            $sql = "SELECT * FROM category_picture
                    LEFT JOIN (category,picture)
                    ON (
                        category_picture.category_id = category.category_id
                        AND category_picture.picture_id = picture.picture_id)
                    WHERE category.category_id='$cId'";
            $qry = mysql_query($sql);
        }elseif(!isset($cId) && isset($pId)){
            $sql = "SELECT * FROM category_picture
                    LEFT JOIN (category,picture)
                    ON (
                        category_picture.category_id = category.category_id
                        AND category_picture.picture_id = picture.picture_id)
                    WHERE picture.picture_id ='$pId'";
            $qry = mysql_query($sql);
        }
        if(isset($qry)){
            while($res = mysql_fetch_assoc($qry)){
                $retArray[] = $res;
            }
        }
        return $retArray;
    }

    private function addCategoryPicture($cId=null,$pId=null){
        if(isset($cId) && isset($pId)){
            $sql = "INSERT INTO category_picture (category_id,picture_id)
                    VALUES ($cId,$pId)";
            return mysql_query($sql);
        }
        return false;
    }

    private function delCategoryPicture($cId=null,$pId=null){
        if(isset($cId) && isset($pId)){
            $sql = "DELETE category_picture FROM category_picture
                    WHERE category_picture.category_id = '$cId'
                    AND category_picture.picture_id = '$pId'";
            return mysql_query($sql);
        }elseif(isset($cId) && !isset($pId)){
            $sql = "DELETE category_picture FROM category_picture
                    WHERE category_picture.category_id = '$cId'";
            return mysql_query($sql);
        }elseif(!isset($cId) && isset($pId)){
            $sql = "DELETE category_picture FROM category_picture
                    WHERE category_picture.picture_id = $pId";
            return mysql_query($sql);
        }
        return false;
    }

    private function getNewsAll(){
        $retArray = array();
        $sql = "SELECT * FROM news ORDER BY news.news_date DESC";
        $qry = mysql_query($sql);

        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function getNews($column,$value){
        $retArray = array();
        $sql = "SELECT * FROM news WHERE news.news_".$column." = '$value'";
        $qry = mysql_query($sql);

        while($res = mysql_fetch_assoc($qry)){
            $retArray[] = $res;
        }
        return $retArray;
    }

    private function addNews($title,$text,$date){
        if(is_string($title) && isset($text) && isset($date)){
            $sql = "INSERT INTO news (news_title,news_text,news_date)
                    VALUES ('$title','$text','$date')";
            mysql_query($sql);
        }
        return false;
    }

    private function delNews($id){
        if(isset($id)){
            $sql = "DELETE news FROM news
                    WHERE news.news_id = '$id'";
            return mysql_query($sql);
        }
        return false;
    }
    /**
     * Login / User
     */

    private function setUserIsLocked($userid,$islocked = '1'){
            $sql = "UPDATE `user` SET `locked` =  '".$islocked."' WHERE `user`.`UserID` = '".$userid."' LIMIT 1 ;";
            mysql_query($sql);
    }

    private function getUserByUserAndPass($user,$pass){
        $sql = "SELECT `user`.`UserID`
        FROM `user` Where `User_MD5` = '".md5($user)."'
        AND `Password` = '".$pass."'
        AND `user`.`locked` = FALSE
        group by `user`.`UserID`";
        $qry= mysql_query($sql);
        $retArray=array();
        if(isset($qry)){
            while($res = mysql_fetch_assoc($qry)){
                $retArray[] = $res;
            }
        }
        return isset($retArray[0])?$retArray[0]:null;
    }


    private function deleteUser($userid){
            $sql = "DELETE FROM `user` WHERE `user`.`UserID` = '".$userid."'";
            mysql_query($sql);
    }


    private function getAccount($md5Name,$md5Pass){
            $sql = "SELECT `UserID` from `user` WHERE `user`.`User_MD5` = '".$md5Name."' AND `user`.`Password` = '".$md5Pass."' Limit 1;";
            $result = mysql_fetch_array(mysql_query($sql));
            return isset($result[0])?$result[0]:null;
    }
}
