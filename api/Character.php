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
    /**
	 * Store overhead
	 * @var string
	 */
    private $heap;
    
    /**
     * @var array;
     */
    static $storage;
    
    /**** Public zone ****/

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
    
    public function __call($name, $args)
    {
        if (isset(self::$storage[$name]))
        {
            if ($args) self::$storage[$name] = $args;
            return number_format(self::$storage[$name], 2, '.', ' ');
        }
    }
    
    public function slot($number, $full = false)
    {
        if ( !isset(self::$storage['slot'][$number]) ) return false;
        
        $idItem = self::$storage['slot'][$number];
        return ($full) ? \WOWAPI\SYSTEM\Factory::getItemData($idItem) : $idItem;
    }
    
    /**** Private zone ****/
    
    private function loadSummaryInventory()
    {
        // TODO Exception if heap is empty
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


