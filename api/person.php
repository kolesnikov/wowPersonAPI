<?php
/**
 * wowCharacter class
 * 
 * @copyright 2011 - Eugene Kolesnikov
 * @version 0.1
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 * 		http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software 
 * distributed under the License is distributed on an "AS IS" BASIS, 
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. 
 * See the License for the specific language governing permissions and 
 * limitations under the License.
 * 
 */

class wowCharacter
{
    static $storage;
    
    function __construct($serverName, $characterName)
    {
        $cacheFile = dirname( __FILE__ ).
            DIRECTORY_SEPARATOR.md5($serverName.$characterName).'.cache';

        if ( is_file($cacheFile) )
        {
            $rowData = file_get_contents($cacheFile);
        }
        else
        {
            $rowData = file_get_contents(
                'http://eu.battle.net/wow/ru/character/'.
                $serverName.'/'.
                $characterName.'/simple');
                
            $cache  = fopen($cacheFile, 'w');
            fwrite($cache, $rowData);
            fclose($cache);
        }
        
        preg_match('/Summary.Stats\(\{(.*?)\}/sm', $rowData, $sourceArray);
        preg_match_all('/\"(\S*)\"\: (\S*),/sm', $sourceArray[1], $matches);
        
        $storage  = array_combine(
            array_values($matches[1]), array_values($matches[2]));
        
    }
    
    public function __call($name, $args)
    {
        if (isset(self::$storage[$name]))
        {
            if ($args) self::$storage[$name] = $args;
            return number_format(self::$storage[$name], 2, '.', ' ');
        }
    }
}


