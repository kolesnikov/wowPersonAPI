<?php

namespace WOWAPI\EXCEPTIONS\SYSTEM;

class CacheDirNotFound extends \Exception {

    function __construct()
    {
        $Message = \WOWAPI\SYSTEM\Messages::$ChacheDirNotFound;
        parent::__construct( $Message );
    }
}
