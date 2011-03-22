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

class Item
{
    function __construct($Item)
    {
        if (intval($Item) > 0)
        {
            $page   = new \WOWAPI\SYSTEM\UrlRequest('http://ru.wowhead.com/item=%s&xml');
            $page->addMeta = false;

            $xml    = new \SimpleXMLElement( $page->load($Item) );
            $return;
            
            $return['name']             = (String) $xml->item->name;
            $return['level']            = (String) $xml->item->level;
            $return['gearScore']        = (String) $xml->item->gearScore;
            $return['quality']          = (String) $xml->item->quality;
            $return['class']            = (String) $xml->item->class;
            $return['subclass']         = (String) $xml->item->subclass;
            $return['inventorySlot']    = (String) $xml->item->inventorySlot;
            $return['link']             = (String) $xml->item->link;
            
            $return['basic'] = json_decode('{'.(String)$xml->item->json.'}');
            $return['equip'] = json_decode('{'.(String)$xml->item->jsonEquip.'}');
            
            return $return;
        }
    }
}


