<?php

namespace WOWAPI\EXCEPTIONS\SYSTEM;

class ChacheDirNotFound extends \Exception {

    function __construct()
    {
        $Message = \WOWAPI\SYSTEM\Messages::$ChacheDirNotFound;
        parent::__construct( $Message );
    }
}
