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

  public function index() {
    
          $books = $this->bookModel->getAllBooks();


        $this->view('pages/index', $books);

}


  public function about() {
    echo 'This is about';
  }

}
