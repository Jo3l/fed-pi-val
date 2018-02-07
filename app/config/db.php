<?php

class db{
    // Properties
    private $dbhost = 'localhost';
    private $dbuser = 'editor';
    private $dbpass = 'extrabot22';
    private $dbname = 'fedpival';
    private $pdo = null;
    private $result = null;

    // Connect
    public function connect($constr){ return $this->__construct($constr); }
    
    public function __construct($constr=''){
        if(empty($constr)) $connect_str = "mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8";
        $this->pdo = new PDO($connect_str, $this->dbuser, $this->dbpass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $this->pdo;
    }
    
    public function sql($sql) { return $this->result= $this->pdo->query($sql); }
    
    public function get() { return $this->result->fetch(PDO::FETCH_OBJ); }
    
    public function all() { return $this->result->fetchAll(PDO::FETCH_OBJ); }
    
    public function array() { return $this->result->fetchAll(PDO::FETCH_ASSOC); }
    
}
