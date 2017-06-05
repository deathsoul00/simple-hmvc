<?php
namespace App\Controllers;

use Core\Registry;
use Core\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        $this->setTemplate('home');
        $this->setLayout('index');
        echo Registry::get('request')->request->get('sample');
        Registry::get('template')->assignVar('user', 'John Doe');
    }
}