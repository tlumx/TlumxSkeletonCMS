<?php

namespace Application\Security;

class Password
{
    public static function generateSalt()
    {
        if (extension_loaded('openssl')) {
            $salt = openssl_random_pseudo_bytes(17);
        } else {
            $salt = pack('N4', mt_rand(), mt_rand(), mt_rand(), mt_rand());
        }
        
        return substr(str_replace('+', '.', base64_encode($salt)), 0, 22);
    }
    
    public static function generatePassword($length = 8, 
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;        
    }
    
    public static function create($password, $cost = 10)
    {
        $salt = self::generateSalt();
        if(!$salt) {
            throw new \RuntimeException("Can't create password.");
        }
        
        $cost = (int) $cost;
        if($cost == 0) {
            throw new \RuntimeException("'Cost' must be not '0' value.");
        }
        
        return crypt($password, "$2y$" . $cost . '$' . $salt);
    }
    
    public static function verify($password, $hashedPassword)
    {
        return crypt($password, $hashedPassword) == $hashedPassword;
    }    
}
