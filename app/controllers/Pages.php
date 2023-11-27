<?php


class Pages extends Controller
{

  protected $bookModel;


  public function __construct()
  {
    $this->bookModel = $this->model('Book');
  }

  public function index() {
    try {

        $books = $this->bookModel->getBooks();

        print_r($books);

        $this->view('pages/index', ['books' => $books]);
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}




  public function about() {
    echo 'This is about';
  }

}
