<?php

namespace TextBlock\Service;

use Tlumx\ServiceContainer\ServiceContainer;

class TextBlockService
{
    const table_textblock= 'textblock';
    
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
    
    public function getTextBlockTable()
    {
        return $this->getTablePrefix() . self::table_textblock;
    }    
    
    public function getTextBlock($key, $lang)
    {
        $text = $this->getDb()->findOne("SELECT text FROM " . 
                $this->getTextBlockTable() . " WHERE block_key=:block_key AND lang=:lang"
                , ['block_key'=>$key, 'lang'=>$lang]);
        
        return (is_null($text) ? '' : $text);      
    }
    
    public function saveTextBlock($key, $lang, $text)
    {
        $count = $this->getDb()->findOne("SELECT COUNT(*) FROM " . 
                $this->getTextBlockTable() . " WHERE block_key=:block_key AND lang=:lang"
                , ['block_key'=>$key, 'lang'=>$lang]);  
        $count = intval($count);
        
        if ($count !== 0) {
            $this->getDb()->update(
                    $this->getTextBlockTable(), 
                    ['text' => $text], 
                    ['block_key'=>$key, 'lang'=>$lang]
            );            
        } else {
            $this->getDb()->insert($this->getTextBlockTable(), [
                'block_key'=>$key, 
                'text' => $text, 
                'lang'=>$lang
            ]);           
        }        
    }    
}