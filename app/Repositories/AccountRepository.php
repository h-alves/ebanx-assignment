<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository
{
    public function reset()
    {
        Account::truncate();
    }

    public function findById($accountId)
    {
        return Account::find($accountId);
    }

    public function create($data)
    {
        return Account::create($data);
    }

    public function update($account, $data)
    {
        $account->update($data);
        return $account;
    }

    public function delete($accountId)
    {
        return Account::destroy($accountId);
    }

    public function firstOrCreate($data)
    {
        return Account::firstOrCreate($data);
    }
}