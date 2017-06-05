<?php
namespace App\Modules\Sample\Controllers\Pre;

use Core\Registry;

class HomeController extends \Core\AbstractController
{
    public function index()
    {
        Registry::get('request')->request->set('sample', 1);
        echo 'pre controller';
    }
}