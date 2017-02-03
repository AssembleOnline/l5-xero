<?php

namespace Assemble\l5xero\Exceptions;

class InvalidTypeException extends \Exception {


    public function __construct($message = null, $code = null, $previous = null) {

        if($message == null)
        {
            $message = "Application type does not exist";
        }
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

}