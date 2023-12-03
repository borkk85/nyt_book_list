<?php

namespace App\Libraries;
use PDO;
use PDOException;


class Database
{

    private $dbh;


    public function __construct()
    {
        // set DSN 
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME;        
            $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Create PDO Instance

        try {
            $this->dbh = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("DB connection failed: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->dbh;
    }

}
