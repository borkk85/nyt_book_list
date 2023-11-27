<?php

ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



class Book
{

    private $db;   

    public function __construct()
    {
        $this->db = new Database;
    }


    public function getBooks()
    {
        try {
            $sql = "SELECT * FROM books";
            $this->db->query($sql);
            $books = $this->db->resultSet();
            return $books;
        } catch (Exception $e) {
            
            echo "Error fetching books from database: " . $e->getMessage();
            return []; 
        }
    }
}
