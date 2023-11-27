<?php 


class BooksApi {

    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

  public function fetchApiData($urls) {
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
            $httpStatus = curl_getinfo($request, CURLINFO_HTTP_CODE);                if ($httpStatus == 200) {
            
                if($httpStatus == 200) {
    
                    $content = curl_multi_getcontent($request);
                    $response[] = json_decode($content, true);
                }  else {
    
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
        $booksApi = new BooksApi();

        $apiKey = 'ARHkk1cQ4Lch4aZqljolCMn9fvIr0v0A';

        $db_col['title'] = '';
        $db_col['isbn13'] = 'primary_isbn13';
        $db_col['isbn10'] = 'primary_isbn10';
        $db_col['author'] = '';
        $db_col['description'] = '';
        $db_col['publisher'] = '';
        $db_col['published_date'] = '';
        $db_col['rank'] = '';
        $db_col['rank_last_week'] = '';
        $db_col['weeks_on_list'] = '';
        $db_col['contributor'] = '';
        $db_col['price'] = '';
        $db_col['age_group'] = '';
        $db_col['book_image_url'] = 'book_image';
        $db_col['amazon_product_url'] = '';


        // $response = file_get_contents($genresURL);
        $genresURL = "https://api.nytimes.com/svc/books/v3/lists/names.json?api-key=" . $apiKey;
        $genresData = $booksApi->fetchApiData([$genresURL]);

        print_r($genresData);

        $genres = array_column($genresData[0]['results'], 'list_name');
        $genres = str_replace(' ', '-', $genres);

        print_r($genres);
        
        $sqlGenre = "INSERT IGNORE INTO genres (name) VALUE (:name)";

        foreach ($genres as $genre) {
           
            $this->db->query($sqlGenre);
            $this->db->bind(':name', $genre);

            try {
                $this->db->execute();
            } catch (Exception $e) {
                echo "Error inserting genre: " . $e->getMessage();
            }
        }

        $update_terms = [];
        $update_terms[] = "`id` = LAST_INSERT_ID(`id`)";


        foreach (array_keys($db_col) as $col) {
            $update_terms[] = "`$col` = VALUES(`$col`)";
        }

        $sql = "INSERT INTO books (`" . implode('`, `', array_keys($db_col)) . "`) VALUE (" . implode(', ', array_fill(0, count($db_col), '?')) . ")
		ON DUPLICATE KEY UPDATE
		" . implode(', ', $update_terms);

        $stmt = $this->db->query($sql);

        $buyLink_sql = "INSERT INTO buy_links (book_id, name, url) VALUES (:book_id, :name, :url)";

        $stmt_buy_links = $this->db->query($buyLink_sql);


        foreach ($genres as $genre) {

            $offset = 0;
            $totalBooks = 0;
            $maxResultsPerPage = 20;

            do {
                $genresApi = "https://api.nytimes.com/svc/books/v3/lists/current/" . urlencode($genre) . ".json?api-key=" . $apiKey . "&offset=" . $offset;
                $booksData = $booksApi->fetchApiData([$genresApi]);
                print_r($booksData);
                if (!empty($booksData) && isset($booksData['results']['books'])) {
                    foreach ($booksData['results']['books'] as $bookDetail) {
                        $bookDetail['published_date'] = $booksData['results']['published_date'];

                        $params = [];
    
                        foreach ($db_col as $key => $input) {
                            $in = empty($input) ? $key : $input;
                            $params[] = $bookDetail[$in];
                        }
    
                        try {
                            $stmt->execute($params);
                            $last_id = $this->db->lastInsertId();
    
                            foreach ($bookDetail['buy_links'] as $buyLink) {
                                // set_time_limit(30); 
                                $stmt_buy_links->execute([
                                    'book_id' => $last_id,
                                    'name' => $buyLink['name'],
                                    'url' => $buyLink['url']
                                ]);
                            }
                        } catch (Exception $e) {
                            echo "Error inserting book: " . $e->getMessage();
                        }
                    }
        
                    $totalBooks = $booksData['num_results'] ?? 0;
                    $offset += $maxResultsPerPage;
                }
        
        
            } while ($offset < $totalBooks);

        }
    }





}




