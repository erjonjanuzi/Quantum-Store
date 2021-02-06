<?php
include_once 'Admin.php';
include_once 'User.php';
require_once 'UserMapper.php';
session_start();

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $password = $_POST['password'];
    $login = new LoginVerify($email, $password);
    $login->verifyData();
} if (isset($_POST['register'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['register-email'];
    $password = $_POST['register-password'];

    $register = new RegisterVerify($firstname, $lastname, $email, $password);
    $register->insertData();
} else {
    
}

class LoginVerify {
    private $email;
    private $password;

    public function __construct($email, $password){
        $this->email = $email;
        $this->password = $password;
    }

    public function verifyData(){
        if($this->emptyInputs($this->email, $this->password)){
            header("Location: ../views/llogaria.php?login=empty-fields");
        } else if ($this->correctLoginData($this->email, $this->password)){
            header("Location: ../views/index.php?login=success");
        } else{
            header("Location: ../views/llogaria.php?login=error");
        }
    }

    private function emptyInputs($email, $password){
        if(empty($email) || empty($password))
            return true;
        else
            return false;
    }
    
    private function correctLoginData($email, $password){
        $mapper = new UserMapper();
        $user = $mapper->getUserByEmail($email);
        if ($user == null || count($user) == 0) {
            return false;
        }
        else if (password_verify($password, $user['password'])) {
            print_r($user);
            if ($user['is_admin'] == 1) {
                $obj = new Admin($user['id'], $user['first_name'], $user['last_name'], $user['password'], $user['role']);
                $obj->setSession();
            } else {
                $obj = new User($user['id'], $user['first_name'], $user['last_name'], $user['password'], $user['role']);
                $obj->setSession();
            }
            return true;
        } else 
            return false;
    }
}

class RegisterVerify{
    private $firstname;
    private $lastname;
    private $email;
    private $password;
    private $confirmpassword;

    public function __construct($firstname, $lastname, $email, $password){
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->password = $password;
    }

    public function insertData(){
        $user = new User($this->firstname, $this->lastname, $this->email, $this->password, 0);
        $mapper = new UserMapper();
        $mapper->insertUser($user);
        $login = new LoginVerify($this->email, $this->password);
        $login->verifyData();
    }
}