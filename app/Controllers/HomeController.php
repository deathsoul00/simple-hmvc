<?php
namespace App\Controllers;

use Core\Registry;
use Core\Module;
use Core\AbstractController;

class HomeController extends AbstractController
{
    public function index()
    {
        $this->setTemplate('home.html');
        $this->setLayout('index');

        Registry::get('template')->assignVar('user', 'John Doe');
        Registry::get('template')->assignVar('main', 'John Doe');
        Registry::get('template')->assignVar('users', ['John Doe', 'Maria Doe']);
    }
}