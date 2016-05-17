<?php

namespace Application\Controller;

use Tlumx\Controller;

class AdminIndexController extends Controller
{
    public function indexAction()
    {
        echo $this->render();
    }    
    
    
    public function forbiddenAction()
    {
        echo $this->render();
    }
}