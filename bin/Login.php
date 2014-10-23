<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 04.10.14
 * Time: 13:57
 * To change this template use File | Settings | File Templates.
 */

use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;

require_once("User.php");
require_once('./lib/Twitter/twitteroauth/twitteroauth.php');

class Login {
    static private $instance = null;
    protected $currentUser;
    protected $fbSession;
    protected $fbHelper;
    protected $googleHelper;
    protected $twHelper;

    private function __construct(){
    }

    /**
     * @return Login|mixed|null
     */
    static public function getInstance(){
        if (null === self::$instance) {
            if (session_id() == "") {
                session_start();
            }
            if (isset($_SESSION['singeltonLoginInstance']) && !isset($_GET['logout'])) {
                self::$instance = unserialize($_SESSION['singeltonLoginInstance']);
                if (isset($_GET['code'])) {
                    /*
                     * FACEBOOK
                     */
                    FacebookSession::setDefaultApplication(config::$FB_CONSUMER_KEY, config::$FB_CONSUMER_SECRET);
                    self::$instance->fbSession = self::$instance->fbHelper->getSessionFromRedirect();
                    if (isset(self::$instance->fbSession)) {
                        self::$instance->getUserResourcesFromFacebook();
                        /*
                         * GOOGLE
                         */
                    } elseif (self::$instance->googleHelper->authenticate($_GET['code'])) {
                        self::$instance->getUserResourcesFromGoogle();
                    } else {
                        echo "hijack";
                    }
                    //refresh to avoid the error "this key is already used"
                    header("Location: index.php");

                    /*
                     * TWITTER
                     */
                } elseif (isset($_GET['oauth_verifier']) &&
                    isset($_GET['oauth_token']) &&
                    $_GET['oauth_token'] == self::$instance->twHelper->token->key){
                    self::$instance->getUserResourcesFromTwitter();
                }
            }else{
                self::$instance = new self;
                self::$instance->currentUser = new User();

                // create Google client
                $client = new Google_Client();
                $client->setClientId(config::$GOOGLE_CONSUMER_KEY);
                $client->setClientSecret(config::$GOOGLE_CONSUMER_SECRET);
                $client->setRedirectUri(config::$MY_URL);
                $client->setScopes("https://www.googleapis.com/auth/userinfo.profile");
                self::$instance->googleHelper = $client;

                // create Twitter client
                self::$instance->twHelper = new TwitterOAuth(config::$TWITTER_CONSUMER_KEY, config::$TWITTER_CONSUMER_SECRET);

                // create Facebook client
                FacebookSession::setDefaultApplication(config::$FB_CONSUMER_KEY, config::$FB_CONSUMER_SECRET);
                self::$instance->fbHelper = new FacebookRedirectLoginHelper(config::$MY_URL);
            }

        }
        return self::$instance;
    }

    public function __destruct(){
        $_SESSION['singeltonLoginInstance'] = serialize($this);
    }

    /**
     * @return string
     */
    public function __toString(){
        if ($this->currentUser->isVerified()) {
            $output = "";
            $logoutLink = "";
            switch($this->getUser()->getRole()){
                case "student":
                    //$logoutLink = $this->fbHelper->getLogoutUrl($this->fbSession,config::$myUrl."?logout=fb");
                    $logoutLink = config::$MY_URL."?logout=fb";
                    break;
                case "teacher":
                    $logoutLink = config::$MY_URL."?logout=google";
                    //@TODO redirect is not working with lnu account
                    //$logoutLink = "https://accounts.google.com/logout?continue=".config::$myUrl."?logout=google";
                    break;
                case "admin":
                    $logoutLink = config::$MY_URL."?logout=twitter";
            }
            $output .= '<li><a href="'.$logoutLink.'">logout</a></li>';
        }else{
            $output = '<li><a href="'.$this->fbHelper->getLoginUrl().'">Facebook login</a></li>';
            $output .= '<li><a href="'.$this->googleHelper->createAuthUrl().'">Google login</a></li>';
            $output .= '<li><a href="'.$this->createTwitterUrl().'">Twitter login</a></li>';
        }
        return $output;
    }

    /**
     * @return User
     */
    public function getUser(){
        return $this->currentUser;
    }

    /**
     * @return String
     */
    protected function createTwitterUrl(){
        if(!isset($this->twHelper->token->key)){
            $temporary_credentials = $this->twHelper->getRequestToken(config::$MY_URL);
            $this->twHelper = new TwitterOAuth(
                config::$TWITTER_CONSUMER_KEY,
                config::$TWITTER_CONSUMER_SECRET,
                $temporary_credentials["oauth_token"],
                $temporary_credentials["oauth_token_secret"]);
        }
        $temporary_credentials["oauth_token"] = $this->twHelper->token->key;
        $temporary_credentials["oauth_token_secret"] = $this->twHelper->token->secret;
        $redirect_url = $this->twHelper->getAuthorizeURL($temporary_credentials);
        return $redirect_url;
    }

    /**
     * @return TwitterOAuth
     */
    public function getTwHelper()
    {
        return $this->twHelper;
    }

    /**
     * @return Google_Client
     */
    public function getGoogleHelper()
    {
        return $this->googleHelper;
    }

    /**
     * @return FacebookRedirectLoginHelper
     */
    public function getFbHelper()
    {
        return $this->fbHelper;
    }

    private function getUserResourcesFromFacebook(){
        try {
            $response = (new FacebookRequest($this->fbSession, 'GET', '/me'))->execute();
            $object = $response->getGraphObject();
            $name = $object->getProperty('name');
            $role = "student";
            $id = $object->getProperty('id');
            $this->setUser($name,$role,$id);
        } catch (FacebookRequestException $ex) {
            echo $ex->getMessage();
        } catch (\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * @param $name
     * @param $roleName
     * @param $id
     */
    protected function setUser($name,$roleName,$id){
        $db = new dbConnector();
        $account = $db->doSql("getAccountByProfileId",array($id));
        $role = $db->doSql("getRoleByName",array($roleName));
        $user = new User();
        $user->setUsername($name);
        $user->setRole($roleName);
        $user->setProfileId($id);
        if($user->getUsername()!= null &&
            $user->getRole() != null &&
            $user->getProfileId() != null){
            $id = $user->getProfileId();
            if(is_null($account[0])){
                $db->doSql("addAccount",array($user->getProfileId(),$user->getUsername(),"",$role[0]["Role_id"]));
            }
            $user->verify();
            $this->currentUser = $user;
        }

    }

    private function getUserResourcesFromGoogle(){
        if ($this->googleHelper->verifyIdToken()) {
            $token = $this->googleHelper->getAccessToken();
            $token = json_decode($token);
            $q = 'https://www.googleapis.com/oauth2/v1/userinfo?access_token='.$token->access_token;
            $json = file_get_contents($q);
            $data = json_decode($json,true);
            $this->setUser($data["name"],"teacher",$data["id"]);
        }
    }

    private function getUserResourcesFromTwitter()
    {
        $token_credentials = $this->twHelper->getAccessToken($_REQUEST['oauth_verifier']);
        $this->twHelper = new TwitterOAuth(
            config::$TWITTER_CONSUMER_KEY,
            config::$TWITTER_CONSUMER_SECRET,
            $token_credentials['oauth_token'],
            $token_credentials['oauth_token_secret']);

        $data = $this->twHelper->get("account/verify_credentials");
        if ($data->errors[0]->code == 32) {

        } else {
            $this->setUser($data->name, "admin", $data->id);
        }
        //refresh to avoid the error "this key is already used"
        header("Location: index.php");
    }
}
