<?php
namespace App\Modules\Sample\Hooks;

class Sample
{
    public function sampleHook(&$a, &$b)
    {
        $a = 'bb';
        $b = 'dd';
    }
}