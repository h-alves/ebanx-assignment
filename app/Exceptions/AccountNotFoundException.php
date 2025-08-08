<?php

namespace App\Exceptions;

use Exception;

class AccountNotFoundException extends Exception
{
    public function __construct($accountId = null)
    {
        $message = $accountId 
            ? "Account with ID {$accountId} not found." 
            : "Account not found.";
        
        parent::__construct($message);
    }
}
