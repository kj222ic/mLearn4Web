<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 23:23
 */

require_once("./bin/mLearn4WebAPI.php");

if(!isset($_GET["id"])){
    echo "no scenario id";
    exit;
}

$ml4wClient = new mLearn4WebAPI();
$scenarioData = $ml4wClient->getDataFromScenario($_GET["id"]);
$scenarioScreen = $ml4wClient->getScenario($_GET["id"]);

$output = "<h2>$scenarioScreen->title</h2>";
foreach($scenarioData as $entry){
    $output .= "<div>";
    $output .=   "<h3>".$entry->groupname."</h3>";
    foreach($entry->data as $screenID=>$screen){
        foreach($screen as $dataItem){
            $output .= "<p>Type: $dataItem->type</br>";
            if(is_array($dataItem->value)){
                $output .= "<ul>";
                foreach($dataItem->value as $valueItem){
                    $output .= "<li>$valueItem</li>";
                }
                $output .= "</ul>";
            }else{
                $output .= ($dataItem->type=='image')?"<img width='200' src='http://celtest1.lnu.se:3030/$dataItem->value'/>":$dataItem->value;
            }

            $output .= "</p>";
        }
    }
    $output .= "</div>";
}

$output .= "<a href='?'>back to main</a>";
echo $output;
