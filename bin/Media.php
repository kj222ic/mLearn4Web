<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 22:18
 */

class Media{

    private $scenarioID;
    private $elementID;
    private $version;
    private $parentID;
    private $owner;
    private $title;
    private $file;
    private $type;

    /**
     * @param null $scenarioID
     * @param null $elementID
     * @param null $version
     * @param null $parentID The Media_id of the previous version
     * @param null $owner
     * @param null $title
     * @param null $file
     * @param null $type {"image","sound","video"}
     */
    public function __construct($scenarioID = null, $elementID = null, $version = null, $parentID = null, $owner = null, $title = null, $file = null, $type = null){

    }

    public function getMedia(){

    }
} 