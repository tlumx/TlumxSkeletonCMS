<?php

namespace Application;

class Languages
{
    protected $defaultLanguage = '';
    
    protected $languages = array();
    
    protected $currentLanguage = '';
    
    public function setLanguages(array $languages)
    {
        $this->languages = $languages;
    }
    
    public function getLanguages()
    {
        return $this->languages;
    }

    public function setDefaultLanguage($language)
    {
        $this->defaultLanguage = $language;
    }    
    
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }
    
    public function setCurrentLanguage($language)
    {
        $this->currentLanguage = $language;
    }
    
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }    
    
    public function isCurrentDefault()
    {
        return $this->currentLanguage == $this->defaultLanguage;
    }
}