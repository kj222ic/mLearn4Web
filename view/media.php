<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 24.10.2014
 * Time: 16:32
 */
define('DOCUMENT_ROOT', dirname(realpath(__FILE__)).'/');

require_once(DOCUMENT_ROOT."../bin/Media.php");

$url= "http://www.picmonkey.com/service";
$param = array(
    "_apikey"       => Config::$PICMONKEY_KEY,
    "_export_agent" => "browser",
    "_export"       => urlencode(Config::$MY_URL."picMonkey.php"),
    "_export_method"=> "GET"
);
$get = "?";
foreach($param as $key=>$value){
    $get .= "$key=$value&";
}
$staticQuery = $url.$get;


$scenarios = $login->getScenarioHolder()->getScenarios();
$output = "";

if(isset($_GET["scenarioID"]) && isset($_GET["elementID"])){
    /* CERTAIN MEDIA FROM SCENARIO */

    foreach($scenarios as $scenario) {
        if ($scenario->getScenarioID() != $_GET["scenarioID"]) {
            continue;
        } else {
            foreach ($scenario->getDataCollection() as $collection) {
                foreach ($collection->getElements() as $element) {
                    if ($element->getID() != $_GET["elementID"]) {
                        continue;
                    } else {
                        $media = $element->getMedia();

                        $output .= "<div>";
                        $output .= "<h3>" . $element->getTitle() . "</h3>";
                        $output .= $media->__toString();

                        //image editing
                        $img = urlencode($media->getFile());
                        $title = "&sID=".$scenario->getScenarioID()." &cID=".$collection->getID()." &eID=".$media->getElementID();
                        $query = $staticQuery."&_import=$img&_title=$title";

                        $output .=  "<a target='_blank' href='$query'>edit Image</a>";
                        $output .= "</div>";
                    }
                }
            }
        }
    }
}elseif(isset($_GET["scenarioID"])){
    /* MEDIA FROM SCENARIO */

    foreach($scenarios as $scenario){
        if($scenario->getScenarioID()!=$_GET["scenarioID"]){
            continue;
        }else{
            $output = "<h2>".$scenario->getTitle()."</h2>";
            foreach($scenario->getDataCollection() as $collection){
                foreach($collection->getElements() as $element){
                    $media = $element->getMedia();
                    if(empty($media)) continue;

                    $output .= "<div>";
                    $output .=      "<h3>".$element->getTitle()."</h3>";
                    $output .=      $media->__toString();

                    //image editing
                    $img = urlencode($media->getFile());
                    $title = "&sID=".$scenario->getScenarioID()." &cID=".$collection->getID()." &eID=".$media->getElementID();
                    $query = $staticQuery."&_import=$img&_title=$title";

                    $output .=  "<a target='_blank' href='$query'>edit Image</a>";
                    $output .= "</div>";
                }
            }
        }
    }
}else{
    /* ALL MEDIA */

    $output = "<h2>All media</h2>";
    $output .= "<div>";
    // O(n³)
    foreach($scenarios as $scenario){
        foreach($scenario->getDataCollection() as $collection){
            foreach($collection->getElements() as $element){
                $media = $element->getMedia();
                if(empty($media)) continue;

                $output .= "<div>";
                $output .=      "<h3>".$element->getTitle()."</h3>";
                $output .=      $media->__toString();

                //image editing
                $img = urlencode($media->getFile());
                $title = "&sID=".$scenario->getScenarioID()."&cID=".$collection->getID()."&eID=".$media->getElementID();
                $query = $staticQuery."&_import=$img&_title=$title";

                $output .=  "<a target='_blank' href='$query'>edit Image</a>";
                $output .= "</div>";
            }
        }
    }
    $output .= "</div>";
}

echo $output;
