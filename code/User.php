<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kevin
 * Date: 04.10.14
 * Time: 13:59
 * To change this template use File | Settings | File Templates.
 */

class User {
    protected $username;
    protected $isVerified = false;
    protected $profile_id;
    protected $role;

    public function __construct(){
    }

    public function __destruct(){
    }

    public function getUsername(){
        return $this->username;
    }

    /**
     * @param String $p_username
     * @param String $p_provider
     */
    public function setUsername($p_username){
        $this->username = $p_username;
    }

    public function isVerified(){
        return $this->isVerified;
    }

    public  function verify(){
        $this->isVerified = true;
    }

    /**
     * @return String
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param String $provider
     */
    public function setRole($provider)
    {
        $this->role = $provider;
    }

    /**
     * @return mixed
     */
    public function getProfileId()
    {
        return $this->profile_id;
    }

    /**
     * @param mixed $profile_id
     */
    public function setProfileId($profile_id)
    {
        $this->profile_id = $profile_id;
    }
}