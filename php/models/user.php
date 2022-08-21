<?php

class User {
    private $id;
    private $username;
    private $password;
    private $admin;

    function __construct($id, $username, $password, $admin)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->admin = $admin;
    }

    function correct_password($pass) {
        return $this->password == $pass;
    }

    function is_admin() {
        return $this->admin;
    }

    function username() {
        return $this->username;
    }

    function id() {
        return $this->id;
    }
}