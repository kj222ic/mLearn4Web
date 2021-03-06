<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 03.10.14
 * Time: 20:55
 * To change this template use File | Settings | File Templates.
 */

date_default_timezone_set('UTC');


require_once("autoload.php");
require_once("./code/Config.php");
require_once("./code/Login.php");
require_once("./code/dbConnector.php");
require_once("./code/mLearn4WebAPI.php");
require_once("./code/Media.php");

$login = Login::getInstance();
$user = $login->getUser();
/**
 * PUT
 */
/*
    // The data to send to the API
    $postData = array(
        "data" =>
            array(
                "screen1" =>
                    array(
                        "elementId" => "cl3",
                        "type" => "location",
                        "value" => ""
                    ),array(
                        "elementId" => "ci2",
                        "type" => "image",
                        "value" => "/files/24796-gll960"
                    )
            )
    );

    $str_data = json_encode($postData);
    $serviceHost = "http://celtest1.lnu.se:3030";
    $baseUrlAPI = $serviceHost . "/mlearn4web";
    $datasetPost = $baseUrlAPI . "/updatedata";
    $url = $datasetPost . '/54477d4af70096dc60645676';

    $headers= array('Accept: application/json','Content-Type: application/json');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$str_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($ch);
    curl_close($ch);
*/
?>


<!DOCTYPE HTML>
<html class="<?php echo ($user->isVerified()==true)?$user->getRole():"" ?>">
    <head>
        <link href="css/main.css" type="text/css" rel="stylesheet">
    </head>
    <body>
        <div class="body_frame">
            <header>
                <h1>mLearn4Web</h1>
                <div class="user_info">
                    <?php
                        if($user->isVerified()==true){
                            echo "<span>Hello ".$user->getUsername()."</span>";
                            echo "<span>Role: ".$user->getRole()."</span>";
                            echo "<span>ID: ".$user->getProfileId()."</span>";
                        }
                        echo $login;
                    ?>
                </div>
                <ul class="nav">
                    <?php

                        echo "<li><a href='?page=scenario'>Scenario</a></li>";
                        if($user->isVerified()==true) {
                            echo "<li><a href='?page=media'>Media</a></li>";
                        }
                    ?>
                </ul>
            </header>
            <section>
            <?php
                isset($_GET["page"])?$page=$_GET["page"]:$page="";
                switch($page){
                    case "scenario" : include("./view/scenario.php"); break;
                    case "media" :
                        if($user->isVerified()==true)
                            include("./view/media.php");
                        break;
                    case "" : break;
                }
            ?>
            </section>
        </div>
    </body>
</html>
