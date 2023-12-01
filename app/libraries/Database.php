<?php

namespace App\Libraries;
use PDO, PDOException;

/*
 * PDO DB Class
 * Connect to DB
 * Create prepared statements
 * Bind Values
 * Return rows and results
 * 
 */

class Database
{

    private $dbh;
    private $error;


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
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    public function getConnection() {
        return $this->dbh;
    }

}
