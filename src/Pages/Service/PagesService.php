<?php

namespace Pages\Service;

use Tlumx\ServiceContainer\ServiceContainer;

class PagesService
{
    const table_pages= 'pages';
    
    protected $container;
    
    protected $db;
    
    protected $tablePrefix;
    
    public function __construct(ServiceContainer $container)
    {
        $this->container = $container;
    }    
    
    public function getDb()
    {
        if(!$this->db) {
            $this->db = $this->container->get('db');
        }
        return $this->db;
    }
    
    public function getTablePrefix()
    {
        if(!$this->tablePrefix) {
            $config = $this->container->getConfig('db');
            $this->tablePrefix = isset($config['table_prefix']) ? 
                    $config['table_prefix'] : '';
        }
        
        return $this->tablePrefix;
    }
    
    public function getPagesTable()
    {
        return $this->getTablePrefix() . self::table_pages;
    }    
    
    public function getPage($pageKey, $lang)
    {
        $text = $this->getDb()->findOne("SELECT text FROM " . 
                $this->getPagesTable() . " WHERE page_key=:key AND lang=:lang"
                , ['key'=>$pageKey, 'lang'=>$lang]);
        
        return (is_null($text) ? '' : $text);      
    }
    
    public function savePage($pageKey, $lang, $text)
    {
        $count = $this->getDb()->findOne("SELECT COUNT(*) FROM " . 
                $this->getPagesTable() . " WHERE page_key=:key AND lang=:lang"
                , ['key'=>$pageKey, 'lang'=>$lang]);  
        $count = intval($count);
        
        if ($count !== 0) {
            $this->getDb()->update(
                    $this->getPagesTable(), 
                    ['text' => $text], 
                    ['page_key'=>$pageKey, 'lang'=>$lang]
            );            
        } else {
            $this->getDb()->insert($this->getPagesTable(), [
                'page_key'=>$pageKey, 
                'text' => $text, 
                'lang'=>$lang
            ]);           
        }        
    }    
}