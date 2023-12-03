<?php

namespace App\Models;
use App\Libraries\Database;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;

class Book
{

    private $db;
    private $client;

    public function __construct()
    {
         $database = new Database();
         $this->db = $database->getConnection();
         $stack = HandlerStack::create();
         $stack->push(RateLimiterMiddleware::perSecond(12));
         $this->client = new Client(['handler' => $stack]);
    }


    public function fetchData($urls) {
        $response = [];

        foreach ($urls as $url) {
            try {
                $apiResponse = $this->client->request('GET', $url);
                if ($apiResponse->getStatusCode() == 200) {
                    $content = $apiResponse->getBody()->getContents();
                    $response[] = json_decode($content, true);
                }
            } catch (GuzzleException $e) {
                echo "Error fetching data from URL: $url - Error: " . $e->getMessage();
            }
        }
        if ($this->db) {
            echo "Database connection established.\n";
        } else {
            echo "Failed to establish database connection.\n";
        }

        return $response;
    }


    public function getAllBooks()
    {
            $sql = "SELECT 
                b.*, 
                GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') AS genres, 
                GROUP_CONCAT(DISTINCT bl.name SEPARATOR ', ') AS buy_link_names, 
                GROUP_CONCAT(DISTINCT bl.url SEPARATOR ', ') AS buy_link_urls
            FROM books b
            LEFT JOIN book_genre bg ON b.id = bg.book_id
            LEFT JOIN genres g ON bg.genre_id = g.id
            LEFT JOIN buy_links bl ON b.id = bl.book_id
            GROUP BY b.id";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
    }
}
