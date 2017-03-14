<?php namespace Impero\Impero\Controller;

use Pckg\Framework\Controller;

class Impero extends Controller
{

    public function getIndexAction()
    {
        return view('index');
    }

    public function getIntroAction()
    {
        return view('intro');
    }

}