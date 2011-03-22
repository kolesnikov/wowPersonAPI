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

namespace WOWAPI\SYSTEM;

/**
 * This class provides the ability to retrieve pages from the original data
 *
 */
class UrlRequest
{
    /**
     * Store the template addresses a remote page
     *
     * @var String
     */
    static $requestUrl;
    
    /**
     * Determines whether to add the title to the grant for the correct 
     * character set definition
     *
     * @var Bool
     */
    public $addMeta = true;
    
    /**
     * Header helps to define the encoding for DOM
     *
     * @var String
     */
    static $meta = '<meta http-equiv="content-type" content="text/html; charset=utf-8">';
    
    
    /**
     * UrlRequest constructor
     * 
     * @var String Template page address
     */
    function __construct($url)
    {
        if ( !is_dir(CACHE_DIR) )
            throw new \WOWAPI\EXCEPTIONS\SYSTEM\CacheDirNotFound();
            
        self::$requestUrl = $url;
    }
    
    /**
     * Loads the specified page with the parameter passed
     *
     * @param Any number of variables, or nothing at all
     * @return String The text of the loaded page
     */
    function load()
    {
        $url = (func_get_args()) 
            ? vsprintf(self::$requestUrl, func_get_args()) 
            : self::$requestUrl;
            
        $dataFile = CACHE_DIR . md5( $url ) . '.cache';
        
        if ($this->isValid($dataFile))
        {
            $rowData = file_get_contents( $dataFile );
        }
        else
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt(
                $curl, 
                CURLOPT_HTTPHEADER, 
                array('Content-type: text/xml;charset="utf-8"')
            ); 
            $rowData = curl_exec($curl); 
            $this->writeCache($dataFile, $rowData);
        }
        $rowData = ($this->addMeta) ? self::$meta . $rowData : $rowData;
        return $rowData;
    }
    
    /**
     * Method that determines the relevance of the cache
     *
     * @param string $file The path to the file
     * @return boolean Whether the cache is valid
     */
    private function isValid( $file )
    {
        if ( is_file($file) )
        {   
            // Cache is valid
            if ( (filemtime($file) + CACHE_TIME) > time() ) 
            {
                return true;
            }
            else
            // Cache is not valid and file is exist
            {
                if ( !unlink($file) )
                    throw new \WOWAPI\EXCEPTIONS\SYSTEM\CacheFilePermissionDenied();
                
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * The method of writing data into the cache file
     *
     * @param string $file The path to the cache file
     * @param string $data Data to write
     */
    private function writeCache($file, $data)
    {
        $cache      = fopen($file, 'w');
        
        if (!fwrite($cache, $data))
            throw new \WOWAPI\EXCEPTIONS\SYSTEM\CacheFilePermissionDenied();
        
        fclose($cache);
        return true;
    }
}


