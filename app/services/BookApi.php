<?php

namespace App\Services;
use App\Libraries\Database;


function logMessage($message) {
    $logFile = 'C:\\xampp\\php\\logs\\php_error_log';
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . "\n", FILE_APPEND);
}



class BookApi
{

    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    

    public function fetchData($urls)
    {
        $ch = curl_multi_init();
        $requests = [];
        $response = [];


        foreach ($urls as $i => $url) {
            $requests[$i] = curl_init($url);
            curl_setopt($requests[$i], CURLOPT_URL, $url);
            curl_setopt($requests[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($requests[$i], CURLOPT_TIMEOUT, 10);
            curl_setopt($requests[$i], CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($requests[$i], CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($requests[$i], CURLOPT_SSL_VERIFYPEER, false);
            curl_multi_add_handle($ch, $requests[$i]);
        }


        $active = null;
        do {
            curl_multi_exec($ch, $active);
        } while ($active > 0);


        foreach ($requests as $request) {
            $httpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);
            if ($httpStatus == 200) {

                if ($httpStatus == 200) {

                    $content = curl_multi_getcontent($request);
                    $response[] = json_decode($content, true);
                } else {

                    echo "Error fetching data for URL: " . curl_getinfo($request, CURLINFO_EFFECTIVE_URL) . " - HTTP status: " . $httpStatus;
                }

                curl_multi_remove_handle($ch, $request);
                curl_close($request);
            }

            curl_multi_close($ch);
            return $response;
        }
    }



    public function getBooks()
    {

        $apiKey = getenv('NYT_API_KEY');

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
        $db_col['category'] = '';

        // $response = file_get_contents($genresURL);
        $genresURL = "https://api.nytimes.com/svc/books/v3/lists/names.json?api-key=" . $apiKey;
        $genresData = $this->fetchData([$genresURL]);


        $genres = array_column($genresData[0]['results'], 'list_name');
        $genres = str_replace(' ', '-', $genres);
        

        $sql_genre = "INSERT INTO genres (name) VALUES (?) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id), name=VALUES(name)";

        $stmt_genre = $this->db->prepare($sql_genre);


        foreach ($genres as $genre) {

            $stmt_genre->execute([$genre]);
        }

        $update_terms = [];
        $update_terms[] = "`id` = LAST_INSERT_ID(`id`)";


        foreach (array_keys($db_col) as $col) {
            $update_terms[] = "`$col` = VALUES(`$col`)";
        }


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
                      

            do {
                $genresApi = "https://api.nytimes.com/svc/books/v3/lists/current/" . urlencode($genre) . ".json?api-key=" . $apiKey . "&offset=" . $offset;
                $booksData = $this->fetchData([$genresApi]);
       
                if (!empty($booksData) && isset($booksData[0]['results']['books'])) {

             

                    foreach ($booksData[0]['results']['books'] as $bookDetail) {
                        print_r($bookDetail);
                        $bookDetail['published_date'] = $booksData[0]['results']['published_date'];
                        $params = [];

                        foreach ($db_col as $key => $input) {
                            if ($key === 'category') {
                                $params[] = $genre; 
                            } else {
                                $in = empty($input) ? $key : $input;
                            $params[] = $bookDetail[$in];
                            }
                        }


                        $stmt_books->execute($params);
                        $last_id = $this->db->lastInsertId();
                        

                        foreach ($bookDetail['buy_links'] as $buyLink) {                           
                                $stmt_links->execute([
                                    $last_id, $buyLink['name'], $buyLink['url']
                                ]);
                            
                        }
                    }
                 
                    $total_books = $booksData['num_results'] ?? 0;
                    $offset += $max_per_page;
                    // sleep(10);
                }

            } while ($offset < $total_books);
        }
    }



}

$database = new Database(); 
$booksApi = new BookApi($database->getConnection()); 
$booksApi->getBooks();