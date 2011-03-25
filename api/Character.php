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

/**
 * Class receives the data character. Most of the values ​​
 * contained in $storage. Access to individual characteristics 
 * can be obtained by calling it as a function, such as health()
 *
 * Equipment is stored in $storage['slot']. This is a general 
 * store, where the ID items. If you want to get the item in 
 * a separate slot , use the slot( slot_number [ ,true if you 
 * want to download full details of item ])
 *
 * Examples:
 * 
 * This is a common way to call the class
 * $Character = new \WOWAPI\API\Character('server-name', 'character-name');
 *
 * Health characters
 * $Character->health();
 * 
 * $Character->slot(12); // Item ID in 12 slot
 * $Character->slot(12, true); // Full information about the item taken from the site wowhead.com
 *
 */
class Character
{
    /**
	 * Store overhead
	 *
	 * @var string
	 */
    private $heap;
    
    private $doc;
    
    /**
     * Shared storage
     *
     * @var array;
     */
    static $storage;
    
    /**** Public zone ****/

    /**
     * Character constructor
     *
     * @param $serverName The name of the server where the character
     * @param $characterName The name of the character data which is necessary to obtain
     *
     */
    public function __construct($serverName, $characterName)
    {
        $url = 'http://eu.battle.net/wow/ru/character/%s/%s/simple';
        try 
        {
            $Page       = new \WOWAPI\SYSTEM\UrlRequest($url);
            $this->heap = $Page->load($serverName, $characterName);
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            return false;
        }
        
        $this->doc = new \WOWAPI\SYSTEM\Nokogiri($this->heap);
        
        $this->loadBase();
        $this->loadSecondary();
        $this->loadSummaryInventory();
        $this->loadSummaryTalents();
        return true;
    }
    
    /**
     * This magical method provides access to any parameter of the character
     */
    public function __call($name, $args)
    {
        if (isset(static::$storage[$name]))
        {
            if ($args) static::$storage[$name] = $args;
            return static::$storage[$name];
        }
    }
    
    /**
     * Method returns either the number or complete information 
     * about the items that wears on a character in a certain slot.
     
     * @param $number Slot number
     * @param $full true|false Flag to request information about the item
     */
    public function slot($number, $full = false)
    {
        if ( !isset(self::$storage['slot'][$number]) ) return false;
        
        $idItem = self::$storage['slot'][$number];
        return ($full) ? \WOWAPI\API\Item($idItem) : $idItem;
    }
    
    /**** Private zone ****/
    
    /**
     *
     */
    private function loadBase()
    {
        list(,,static::$storage['race'])  = array_values($this->doc->get('a.race')->toArray());
        list(,,static::$storage['class']) = array_values($this->doc->get('a.class')->toArray());
        list(,,static::$storage['spec'])  = array_values($this->doc->get('a.spec')->toArray());
        $level = array_values($this->doc->get('span.level')->toArray());
        static::$storage['level'] = $level[1]['#text'];
        
        return true;
    }
    
    /**
     * 
     */
    private function loadSecondary()
    {
        preg_match('/Summary.Stats\(\{(.*?)\}/sm', $this->heap, $sourceArray);
        preg_match_all('/\"(\S*)\"\: (\S*),/sm', $sourceArray[1], $matches);

        static::$storage  = array_combine(
            array_values($matches[1]), array_values($matches[2]));
            
        return true;
    }
    
    private function loadSummaryTalents()
    {
        $talents = $this->doc->get('#summary-talents')->toArray();
        foreach ($talents['ul'][0]['li'] as $talent)
        {
            if ( count($talent['a'][0]['span']['span']) < 3) continue;
            
            $type = strpos($talent['a'][0]['href'], 'primary') ? 'primary' : 'secondary';
            
            foreach ($talent['a'][0]['span']['span'] as $prop)
            {
                switch ($prop['class'])
                {
                    case 'roles':
                        $role = str_replace('icon-', '', $prop['span'][0]['class']);
                        break;
                        
                    case 'name-build':
                        $name = $prop['span'][0]['#text'];
                        break;
                }
            }
            static::$storage['talents'][$type] = array($name, $role);
        }        
        return true;
    }
    
    /**
     * Systemic treatment of items worn by characters
     */
    private function loadSummaryInventory()
    {
        $slots = $this->doc->get('div.summary-inventory')->toArray();
        
        foreach ($slots['div'] as $key => $slot)
        {       
            $item = $slot['div'][0]['div'][0]['a'][0];
            
            if ($item['class'] == 'empty')
            {
                $idItem = NULL;
            }
            else
            {
                preg_match('/i=(\d+)/', $item['data-item'], $dataItem);
                $idItem = $dataItem[1];
            }
            
            self::$storage['slot'][$slot['data-id']] = $idItem;
        }
        
        return true;
    }
}


