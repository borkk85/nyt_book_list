<?php


use App\Libraries\Controller;
use App\Models\Book;

class Pages extends Controller
{

  protected $bookModel;


  public function __construct()
  {
    $this->bookModel = new Book();
  }

  public function index($selected_cat = null)
  {
    $selected_cat = isset($_GET['category']) ? $_GET['category'] : null;
    $books = $this->bookModel->getAllBooks();

    $categories = array_unique(array_reduce($books, function ($call, $book) {
      return array_merge($call, explode(', ', $book['genres']));
    }, []));

    if ($selected_cat) {
      $books = array_filter($books, function ($book) use ($selected_cat) {
        return strpos($book['genres'], $selected_cat) !== false;
      });
    }

    $data = [
      'books' => $books,
      'categories' => $categories,
      'selected_cat' => $selected_cat
    ];

    $this->view('pages/index', $data);
  }


  public function about()
  {
    echo 'This is about';
  }
}
