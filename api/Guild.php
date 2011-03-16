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

class Guild
{
    /**
	 * Store overhead
	 * @var string
	 */
    private $heap;
    
    /**
     * Guild name
     * @var string
     */
    public $name;
    
    /**
     * Number of guild members
     * @var integer
     */
    public $totalMembers;
    
    function __construct($serverName, $guildName)
    {
        $url = 'http://eu.battle.net/wow/ru/guild/%s/%s/';
        try 
        {
            $Page   = new \WOWAPI\SYSTEM\UrlRequest($url);
            $this->heap  = $Page->load($serverName, $guildName);
            
            $this->initial();
        }
        catch(\Exception $e)
        {
            echo $e->getMessage();
            return false;
        }
        
        return true;
    }
    
    private function initial()
    {
        $doc = new \WOWAPI\SYSTEM\Nokogiri($this->heap);
        
        $rowFormat = $doc->get('div.profile-guild-info div.name')->toArray();
        $this->name = $rowFormat['a']['#text'];
        
        $rowFormat = $doc->get('span.members')->toArray();
        preg_match( '/\d+/', $rowFormat['#text'], $totalMembers );
        $this->totalMembers = intval($totalMembers[0]);
    }
}


