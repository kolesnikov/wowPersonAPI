<?php
namespace WOWAPI\SYSTEM;

class UrlRequest
{
    static $requestUrl;
    
    function __construct($url)
    {
        if ( !is_dir(CACHE_DIR) )
            throw new \WOWAPI\EXCEPTIONS\SYSTEM\CacheDirNotFound();
            
        self::$requestUrl = $url;
    }
    
    /**
     *
     * Loads the specified page with the parameter passed
     *
     * @param Any number of variables, or nothing at all
     * @return string The text of the loaded page
     */
    function load()
    {   
        $url = (func_get_args()) 
            ? vsprintf(self::$requestUrl, func_get_args()) 
            : self::$requestUrl
            
        $dataFile = CACHE_DIR . md5( $url ) . '.cache';
        
        if ($this->isValid($dataFile))
        {
            $rowData = file_get_contents( $dataFile );
        }
        else
        {
            $rowData = file_get_contents( $url );
            $this->writeCache($dataFile, $rowData);
        }

        return $rowData;
    }
    
    /**
     *
     * @param string $file The path to the file
     * @return boolean Whether the cache is valid
     */
    private function isValid( $file )
    {
        if ( is_file($file) )
        {   
            // Cache is valid
            if ( ($mTime + CACHE_TIME) > time() ) 
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
     *
     * @param string $file The path to the cache file
     * @param string $data Data to write
     */
    private function writeCache($file, $data)
    {
        $cache      = fopen($cacheFile, 'w');
        if (!fwrite($cache, $rowData))
            throw new \WOWAPI\EXCEPTIONS\SYSTEM\CacheFilePermissionDenied();
        fclose($cache);
        return true;
    }
}


