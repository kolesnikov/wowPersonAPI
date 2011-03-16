<?php
/**
 * 
 * @author Eugene Kolesnikov <ivancarevich@gmail.com>
 * @package wowAPI
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

namespace WOWAPI\API;

class Character
{
    static $storage;
    
    function __construct($serverName, $characterName)
    {
        $url = 'http://eu.battle.net/wow/ru/character/%s/%s/simple';
        try 
        {
            $Page       = new \WOWAPI\SYSTEM\UrlRequest($url);
            $pageData   = $Page->load($serverName, $characterName);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            return false;
        }

        preg_match('/Summary.Stats\(\{(.*?)\}/sm', $pageData, $sourceArray);
        preg_match_all('/\"(\S*)\"\: (\S*),/sm', $sourceArray[1], $matches);
        
        self::$storage  = array_combine(
            array_values($matches[1]), array_values($matches[2]));
        
        return true;
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


