<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 23.10.2014
 * Time: 22:18
 */

class Media{

    private $scenarioID;
    private $collectionID;
    private $elementID;
    private $version;
    private $parentID;
    private $owner;
    private $title;
    private $file;
    private $type;

    /**
     * @param null $scenarioID
     * @param null $collectionID
     * @param null $elementID
     * @param null $version
     * @param null $parentID The Media_id of the previous version
     * @param null $owner
     * @param null $title
     * @param null $file
     * @param null $type {"image","sound","video"}
     */
    public function __construct($scenarioID = null, $collectionID = null, $elementID = null, $version = null, $parentID = null, $owner = null, $title = null, $file = null, $type = null){
        $this->scenarioID = $scenarioID;
        $this->collectionID = $collectionID;
        $this->elementID = $elementID;
        $this->version = $version;
        $this->parentID = $parentID;
        $this->owner = $owner;
        $this->title = $title;
        $this->file = $file;
        $this->type = $type;
    }

    public function update(){
        $db = new dbConnector();
        $dbMedia = $db->doSql("getMediaBySCEID",array($this->scenarioID,$this->collectionID,$this->elementID));
        if(!empty($dbMedia)){
            $this->version = $dbMedia[0]["Version"];
            $this->parentID = $dbMedia[0]["Parent_id"];
            $this->owner = $dbMedia[0]["Owner"];
            $this->file = $dbMedia[0]["file"];
            return $this;
        }else{
            return false;
        }
    }

    public function getMediaEditLink(){
        //image editing
        $img = urlencode($this->file);
        $title = "&sID=".$this->scenarioID."&cID=".$this->collectionID."&eID=".$this->elementID;
        return $this->picMonkeyUrlBase()."&_import=$img&_title=$title";
    }

    private static function picMonkeyUrlBase(){
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
        return $url.$get;
    }

    public function __toString(){
        $output = "<img cID='$this->collectionID' eID='$this->elementID' sID='$this->scenarioID' title='$this->title' src='$this->file'/>";
        return $output;
    }

    /**
     * @return null
     */
    public function getScenarioID()
    {
        return $this->scenarioID;
    }

    /**
     * @param null $scenarioID
     */
    public function setScenarioID($scenarioID)
    {
        $this->scenarioID = $scenarioID;
    }

    /**
     * @return null
     */
    public function getElementID()
    {
        return $this->elementID;
    }

    /**
     * @param null $elementID
     */
    public function setElementID($elementID)
    {
        $this->elementID = $elementID;
    }

    /**
     * @return null
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param null $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return null
     */
    public function getParentID()
    {
        return $this->parentID;
    }

    /**
     * @param null $parentID
     */
    public function setParentID($parentID)
    {
        $this->parentID = $parentID;
    }

    /**
     * @return null
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param null $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param null $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param null $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param null $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getCollectionID()
    {
        return $this->collectionID;
    }

    /**
     * @param mixed $collectionID
     */
    public function setCollectionID($collectionID)
    {
        $this->collectionID = $collectionID;
    }
} 