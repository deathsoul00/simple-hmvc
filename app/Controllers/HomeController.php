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
        echo Registry::get('request')->request->get('sample');
        $a = 'aa';
        $b = 'cc';
        Module::hook('sample', $a, $b);
        
        var_dump($a, $b);

        Registry::get('template')->assignVar('user', 'John Doe');
    }
}