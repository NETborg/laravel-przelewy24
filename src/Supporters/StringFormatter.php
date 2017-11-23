<?php 

namespace NetborgTeam\P24\Supporters;


class StringFormatter {
    
    private static $POLSKIE_ZNAKI = [
	'ą','ć','ę','ł','ń','ó','ś','ź','ż','Ą','Ć','Ę','Ł','Ń','Ó','Ś','Ź','Ż',' ',',','.','!','/','\\',':',';','"','\'','@','£','$','%','&','*','(',')','[',']','-','!','?','<','>'
    ];
    
    private static $ZAMIENNIKI = [
	'a','c','e','l','n','o','s','z','z','A','C','E','L','N','O','S','Z','Z','_'
    ];
    
    public static function makeMetaTitle($title) {
        $str = strtolower(str_replace(
                static::$POLSKIE_ZNAKI, 
                static::$ZAMIENNIKI, 
                trim($title)
        ));
        
        return $str;
    }
    
}