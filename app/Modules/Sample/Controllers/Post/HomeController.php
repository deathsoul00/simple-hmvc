<?php
namespace App\Modules\Sample\Controllers\Post;

use Core\Registry;

class HomeController extends \Core\AbstractController
{
    public function index()
    {
        echo $this->getTemplate();
        echo Registry::get('template')->getAssignedVar('user');
        echo 'post controller';
    }
}