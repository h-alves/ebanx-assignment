<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Repositories\AccountRepository;

class AccountService
{
    protected AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function reset()
    {
        $this->accountRepository->reset();
        return true;
    }

    public function getBalance($accountId)
    {
        $account = $this->accountRepository->findById($accountId);
        
        if (!$account) {
            throw new AccountNotFoundException($accountId);
        }
        
        return $account->balance;
    }
    
    public function createAccount(array $data)
    {
        return $this->accountRepository->create($data);
    }
}