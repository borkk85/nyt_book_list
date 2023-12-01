<?php

use App\Libraries\Controller;


class Pages extends Controller
{

  protected $bookModel;


  public function __construct()
  {
    $this->bookModel = $this->model('Book');
  }

  public function index() {
    
          $books = $this->bookModel->getBooks();

        print_r($books);

        // $this->view('pages/index', ['books' => $books]);

}


  public function about() {
    echo 'This is about';
  }

}
