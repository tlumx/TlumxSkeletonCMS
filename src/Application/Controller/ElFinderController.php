<?php

namespace Application\Controller;

use Tlumx\Controller;

class ElFinderController extends Controller
{
    public function indexAction()
    {
        echo $this->render();
    }    
    
    public function connectorAction()
    {
        $elfinderConfig = $this->getServiceProvider()->getConfig('elfinder');
        $connector = new \elFinderConnector(new \elFinder($elfinderConfig));
        $connector->run();        
    }   
    
    public function tinymceAction()
    {
        $this->enableLayout(false);
        echo $this->render();        
    }    
}