<?php

namespace App\Services;

use App\Exceptions\AccountNotFoundException;
use App\Repositories\AccountRepository;

class EventService
{
    protected AccountRepository $accountRepository;

    public function __construct(AccountRepository $accountRepository)
    {
        $this->accountRepository = $accountRepository;
    }

    public function deposit($accountId, $amount)
    {
        $account = $this->accountRepository->findById($accountId);
        
        if ($account) {
            $this->accountRepository->update($account, [
                'balance' => $account->balance + $amount
            ]);
        } else {
            $account = $this->accountRepository->create([
                'id' => $accountId,
                'balance' => $amount,
            ]);
        }

        return [
            'destination' => [
                'id' => $account->id,
                'balance' => $account->balance,
            ],
        ];
    }

    public function withdraw($accountId, $amount)
    {
        $account = $this->accountRepository->findById($accountId);
        
        if (!$account || $account->balance < $amount) {
            throw new AccountNotFoundException($accountId);
        }

        $this->accountRepository->update($account, [
            'balance' => $account->balance - $amount
        ]);
        
        return [
            'origin' => [
                'id' => $account->id,
                'balance' => $account->balance,
            ],
        ];
    }

    public function transfer($originAccountId, $destinationAccountId, $amount)
    {
        $originAccount = $this->accountRepository->findById($originAccountId);
        $destinationAccount = $this->accountRepository->firstOrCreate([
            'id' => $destinationAccountId,
        ]);

        if (!$originAccount || $originAccount->balance < $amount) {
            throw new AccountNotFoundException($originAccountId);
        }

        $this->accountRepository->update($originAccount, [
            'balance' => $originAccount->balance - $amount
        ]);

        $this->accountRepository->update($destinationAccount, [
            'balance' => $destinationAccount->balance + $amount
        ]);

        return [
            'origin' => [
                'id' => $originAccount->id,
                'balance' => $originAccount->balance,
            ],
            'destination' => [
                'id' => $destinationAccount->id,
                'balance' => $destinationAccount->balance,
            ],
        ];
    }
}