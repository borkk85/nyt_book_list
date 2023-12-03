<?php

require_once __DIR__ .  '../../../vendor/autoload.php';
require_once __DIR__ . '../../../app/bootstrap.php';


use App\Libraries\Database;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use Spatie\GuzzleRateLimiterMiddleware\RateLimiterMiddleware;


function logMessage($message)
{
    $logFile = 'C:\\xampp\\php\\logs\\php_error_log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}



class BookApi
{

    private $db;
    private $client;

    public function __construct()
    {
         $database = new Database();
         $this->db = $database->getConnection();
         $stack = HandlerStack::create();
         $stack->push(RateLimiterMiddleware::perMinute(5));
         $this->client = new Client(['handler' => $stack]);
    }


    public function fetchData($urls) {
        $response = [];
        echo "Starting fetchData...\n";
        foreach ($urls as $url) {
            echo "Fetching URL: $url\n";
            try {
                $apiResponse = $this->client->request('GET', $url);
                if ($apiResponse->getStatusCode() == 200) {
                    $content = $apiResponse->getBody()->getContents();
                    $response[] = json_decode($content, true);
                    sleep(12); 
                }
            } catch (GuzzleException $e) {
                echo "Error fetching data from URL: $url - Error: " . $e->getMessage();
            }
        }
        echo "Finished processing.\n";
        return $response;
        
    }


    public function getBooks()
    {


        $apiKey = $_ENV['NYT_API_KEY'];

        $db_col['title'] = '';
        $db_col['isbn13'] = 'primary_isbn13';
        $db_col['isbn10'] = 'primary_isbn10';
        $db_col['author'] = '';
        $db_col['description'] = '';
        $db_col['publisher'] = '';
        $db_col['published_date'] = '';
        $db_col['contributor'] = '';
        $db_col['price'] = '';
        $db_col['age_group'] = '';
        $db_col['book_image_url'] = 'book_image';
        $db_col['amazon_product_url'] = '';
        $db_col['rank'] = '';
        $db_col['rank_last_week'] = '';
        $db_col['weeks_on_list'] = '';
        

    
        $genresURL = "https://api.nytimes.com/svc/books/v3/lists/names.json?api-key=" . $apiKey;
        $genresData = $this->fetchData([$genresURL]);

        $genres = array_column($genresData[0]['results'], 'list_name');
        $genres = str_replace(' ', '-', $genres);
       

        $update_terms = [];
        $update_terms[] = "`id` = LAST_INSERT_ID(`id`)";


        foreach (array_keys($db_col) as $col) {
            $update_terms[] = "`$col` = VALUES(`$col`)";
        }
        

        $sql_genre = "INSERT IGNORE INTO genres (name) VALUE (?)";

        $stmt_genre = $this->db->prepare($sql_genre);


        $sql_book_genre = "INSERT IGNORE INTO book_genre (book_id, genre_id) VALUES (?, ?)";

        $stmt_book_genre = $this->db->prepare($sql_book_genre);


        $sql = "INSERT INTO books (`" . implode('`, `', array_keys($db_col)) . "`) VALUE (" . implode(', ', array_fill(0, count($db_col), '?')) . ")
		ON DUPLICATE KEY UPDATE
		" . implode(', ', $update_terms);


        $stmt_books = $this->db->prepare($sql);

        $buyLink_sql = "INSERT INTO buy_links (book_id, name, url) VALUES (?, ?, ?)";

        $stmt_links = $this->db->prepare($buyLink_sql);


        foreach ($genres as $genre) {
            
            $offset = 0;
            $total_books = 0;
            $max_per_page = 20;
            
           $stmt_genre->execute([$genre]);
           $genre_last_id = $this->db->lastInsertId();
           
            do {
                $genresApi = "https://api.nytimes.com/svc/books/v3/lists/current/" . urlencode($genre) . ".json?api-key=" . $apiKey . "&offset=" . $offset;
                $booksData = $this->fetchData([$genresApi]);
                // var_dump($booksData);
                if (!empty($booksData) && isset($booksData[0]['results']['books'])) {

                    foreach ($booksData[0]['results']['books'] as $bookDetail) {
                        // print_r($bookDetail);
                        $bookDetail['published_date'] = $booksData[0]['results']['published_date'];

                        $params = [];

                        foreach ($db_col as $key => $input) {
                            $in = empty($input) ? $key : $input;
                            $params[] = $bookDetail[$in];
                        }

                        $stmt_books->execute($params);
                        $last_id = $this->db->lastInsertId();

                        $stmt_book_genre->execute([
                            $last_id, $genre_last_id
                        ]);
    

                        foreach ($bookDetail['buy_links'] as $buyLink) {                           
                                $stmt_links->execute([
                                    $last_id, $buyLink['name'], $buyLink['url']
                                ]);
                            
                        }
                    }
                 
                    $total_books = $booksData['num_results'] ?? 0;
                    $offset += $max_per_page;
                    
                }

            } while ($offset < $total_books);
        }
    }

}

$database = new Database();
$db = $database->getConnection();
$booksApi = new BookApi($db);
$booksApi->getBooks();
