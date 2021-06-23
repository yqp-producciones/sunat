<?php
class IndexController extends Controller
{
    public function __construct(){ 
          
    }
    public function index(){
        $this->view('index',[]);
    }
    public function saludo(){
        echo 'hola mundo';
    }
}