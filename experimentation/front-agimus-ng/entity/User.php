<?php

class User {
    
    private $id;
    private $username;
    private $password;
    private $email;
    private $roles;

/*
    public function __construct($id,$username, $password=NULL, $email=NULL, $roles='')
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->roles = $roles;
        //$this->salt = md5(uniqid(null, true));
    }
*/
    //GETTERS --------------------------------------------------

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getRoles()
    {
        return explode(',', $this->roles);      
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getEmail()
    {
        return $this->email;
    }
    
    // SETTERS --------------------------------------------------

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Set roles
     *
     * @param string $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }


    public function isAdmin() {
        return strpos($this->roles,'ROLE_ADMIN') !== false;
    }
}
