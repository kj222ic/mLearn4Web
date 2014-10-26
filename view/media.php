<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 24.10.2014
 * Time: 16:32
 */
define('DOCUMENT_ROOT', dirname(realpath(__FILE__)).'/');

require_once(DOCUMENT_ROOT."../code/Media.php");



$scenarioHolder = $login->getScenarioHolder();
$scenarios = $scenarioHolder->getScenarios();
$output = "";

if(isset($_GET["sID"]) && isset($_GET["cID"]) && isset($_GET["eID"])) {
    /* CERTAIN MEDIA FROM SCENARIO */

    $scenario = $scenarioHolder->getScenarioByID($_GET["sID"]);
    $collection = $scenario->getCollectionByID($_GET["cID"]);
    $element = $collection->getElementByID($_GET["eID"]);
    $media = $element->getMedia();
    empty($collection)?exit:null;

    $output = "<h3>".$scenario->getTitle()."</h3>";
    $output .=  "<h4>".$collection->getGroupname()."</h4>";

    $output .= "<div>";
    $output .= "<h5>" . $element->getTitle()."</h5>";
    $output .= $media->__toString();

    $output .= "<a target='_blank' href='" . $media->getMediaEditLink() . "'>edit Image</a>";
    $output .= "</div>";

}elseif(isset($_GET["sID"]) && isset($_GET["cID"])){

    $scenario = $scenarioHolder->getScenarioByID($_GET["sID"]);
    $collection = $scenario->getCollectionByID($_GET["cID"]);
    empty($collection)?exit:null;

    $output = "<h3>".$scenario->getTitle()."</h3>";
    $output .=  "<h4>".$collection->getGroupname()."</h4>";

    foreach($collection->getElements() as $element){
        $media = $element->getMedia();
        if(empty($media)) continue;

        $link = "?page=".$_GET['page']."&sID=".$media->getScenarioID()."&cID=".$media->getCollectionID()."&eID=".$media->getElementID();

        $output .= "<div>";
        $output .=      "<h5><a href='$link'>".$element->getTitle()."</a></h5>";
        $output .=      $media->__toString();
        $output .=      "<a target='_blank' href='".$media->getMediaEditLink()."'>edit Image</a>";
        $output .= "</div>";
    }

}elseif(isset($_GET["sID"])){
    /* MEDIA FROM SCENARIO */

    $scenario = $scenarioHolder->getScenarioByID($_GET["sID"]);
    empty($scenario)?exit:null;

    $output = "<h3>".$scenario->getTitle()."</h3>";

    foreach($scenario->getDataCollection() as $collection){

        $link = "?page=".$_GET['page']."&sID=".$scenario->getScenarioID()."&cID=".$collection->getId();
        $output .=  "<h4><a href='$link'>".$collection->getGroupname()."</a></h4>";

        foreach($collection->getElements() as $element){
            $media = $element->getMedia();
            if(empty($media)) continue;

            $link = "?page=".$_GET['page']."&sID=".$media->getScenarioID()."&cID=".$media->getCollectionID()."&eID=".$media->getElementID();

            $output .= "<div>";
            $output .=      "<h5><a href='$link'>".$element->getTitle()."</a></h5>";
            $output .=      $media->__toString();
            $output .=      "<a target='_blank' href='".$media->getMediaEditLink()."'>edit Image</a>";
            $output .= "</div>";
        }
    }

}else{
    /* ALL MEDIA */

    $output = "<h2>All media</h2>";
    $output .= "<div>";
    // O(n³)
    foreach($scenarios as $scenario){

        $link = "?page=".$_GET['page']."&sID=".$scenario->getScenarioID();
        $output .=  "<h3><a href='$link'>".$scenario->getTitle()."</a></h3>";

        foreach($scenario->getDataCollection() as $collection){

            $link = "?page=".$_GET['page']."&sID=".$scenario->getScenarioID()."&cID=".$collection->getId();
            $output .=  "<h4><a href='$link'>".$collection->getGroupname()."</a></h4>";

            foreach($collection->getElements() as $element){
                $media = $element->getMedia();
                if(empty($media)) continue;

                $link = "?page=".$_GET['page']."&sID=".$media->getScenarioID()."&cID=".$media->getCollectionID()."&eID=".$media->getElementID();

                $output .= "<div>";
                $output .=      "<h5><a href='$link'>".$element->getTitle()."</a></h5>";
                $output .=      $media->__toString();
                $output .=      "<a target='_blank' href='".$media->getMediaEditLink()."'>edit Image</a>";
                $output .= "</div>";
            }
        }
    }
    $output .= "</div>";
}

echo $output;
