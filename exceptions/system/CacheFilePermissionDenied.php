<?php

namespace WOWAPI\EXCEPTIONS\SYSTEM;

class CacheFilePermissionDenied extends \Exception {

    function __construct()
    {
        $Message = \WOWAPI\SYSTEM\Messages::$CacheFilePermissionDenied;
        parent::__construct( $Message );
    }
}

