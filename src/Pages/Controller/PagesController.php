<?php

namespace Pages\Controller;

use Tlumx\Controller;

class PagesController extends Controller
{
    protected $languages;
    
    protected $pagesService;
    
    public function homeAction()
    {
        $service = $this->getPagesService();
        $langs = $this->getLanguages()->getLanguages();        
        $routerResult = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $currRouteParams = $routerResult->getParams();      
                
        $currLang = isset($currRouteParams['lang']) ? strval($currRouteParams['lang']) : '';
        if(!array_key_exists($currLang, $langs)) {
            $currLang = $this->getLanguages()->getCurrentLanguage();
        }
        
        $text = $service->getPage('home', $currLang);
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $postText = strval(isset($_POST['home-text']) ? $_POST['home-text'] : '');            
            $postText = stripcslashes($postText);                        
            $service->savePage('home', $currLang, $postText);            
            $text = $postText;
            $this->getView()->isSave = 'The data is stored';
        }
        
        $this->getView()->currLang = $currLang;
        $this->getView()->text = $text;
        echo $this->render();        
    }
    
    public function aboutAction()
    {
        $service = $this->getPagesService();
        $langs = $this->getLanguages()->getLanguages();        
        $routerResult = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $currRouteParams = $routerResult->getParams();              
        
        $currLang = isset($currRouteParams['lang']) ? strval($currRouteParams['lang']) : '';
        if(!array_key_exists($currLang, $langs)) {
            $currLang = $this->getLanguages()->getCurrentLanguage();
        }
        
        $text = $service->getPage('about', $currLang);
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $postText = strval(isset($_POST['about-text']) ? $_POST['about-text'] : '');
            $postText = stripcslashes($postText);                        
            $service->savePage('about', $currLang, $postText);            
            $text = $postText;
            $this->getView()->isSave = 'The data is stored';
        }
        
        $this->getView()->currLang = $currLang;
        $this->getView()->text = $text;
        echo $this->render();        
    }
    
    public function getLanguages()
    {        
        if(!$this->languages) {
            $this->languages = $this->getServiceProvider()->get('languages');
        }
        
        return $this->languages;
    }
    
    public function getPagesService()
    {
        if(!$this->pagesService) {
            $this->pagesService = $this->getServiceProvider()->get('pages_service');
        }
        
        return $this->pagesService;
    }    
}