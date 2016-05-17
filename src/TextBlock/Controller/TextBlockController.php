<?php

namespace TextBlock\Controller;

use Tlumx\Controller;

class TextBlockController extends Controller
{
    protected $languages;
    
    protected $service;
    
    public function textblockAction()
    {
        $service = $this->getTextBlockService();
        $langs = $this->getLanguages()->getLanguages();
        
        $routerResult = $this->getServiceProvider()->getRequest()->getAttribute('router_result');
        $currRouteParams = $routerResult->getParams();        
        $currLang = isset($currRouteParams['lang']) ? strval($currRouteParams['lang']) : '';
        if(!array_key_exists($currLang, $langs)) {
            $currLang = $this->getLanguages()->getCurrentLanguage();
        }
        $currRouteHandler = $routerResult->getHandler();
        $block = isset($currRouteHandler['block']) ? strval($currRouteHandler['block']) : '';
        $blockKey = null;
        $blockName = '';
        $routeName = '';
        switch ($block) {
            case 'information':
                $blockKey = 'information';
                $blockName = 'information';
                $routeName = 'textblock_information';
                break;          
            default :
                $blockKey = null;                
        }
        
        if($blockKey === null) {
            $this->redirectToRoute('index');
            return;
        }
        
        $text = $service->getTextBlock($blockKey, $currLang);
        
        if($this->getServiceProvider()->getRequest()->getMethod() == 'POST') {
            $postText = trim(strval(isset($_POST['block-text']) ? $_POST['block-text'] : ''));
            $postText = strip_tags($postText);
            $postText = stripcslashes($postText);            
            
            if(strlen($postText) < 999) {
                $service->saveTextBlock($blockKey, $currLang, $postText);
                $text = $postText;
                $this->getView()->isSave = 'The data is stored';                
            }            
        }        
                
        $this->getView()->blockName = $blockName;
        $this->getView()->routeName = $routeName;
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
    
    public function getTextBlockService()
    {
        if(!$this->service) {
            $this->service = $this->getServiceProvider()->get('textblock_service');
        }
        
        return $this->service;
    }    
}