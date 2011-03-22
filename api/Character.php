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

        preg_match('/Summary.Stats\(\{(.*?)\}/sm', $this->heap, $sourceArray);
        preg_match_all('/\"(\S*)\"\: (\S*),/sm', $sourceArray[1], $matches);
        
        self::$storage  = array_combine(
            array_values($matches[1]), array_values($matches[2]));
        
        $this->loadSummaryInventory();
        
        return true;
    }
    
    /**
     * This magical method provides access to any parameter of the character
     */
    public function __call($name, $args)
    {
        if (isset(self::$storage[$name]))
        {
            if ($args) self::$storage[$name] = $args;
            return number_format(self::$storage[$name], 2, '.', ' ');
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
     * Systemic treatment of items worn by characters
     */
    private function loadSummaryInventory()
    {
        $doc = new \WOWAPI\SYSTEM\Nokogiri($this->heap);
        $slots = $doc->get('div.summary-inventory')->toArray();
        
        $summaryInventory;
        
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


