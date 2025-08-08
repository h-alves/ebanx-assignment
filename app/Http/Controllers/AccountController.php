<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFoundException;
use App\Services\AccountService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    protected AccountService $accountService;

    public function __construct(AccountService $accountService)
    {
        $this->accountService = $accountService;
    }

    public function reset()
    {
        $this->accountService->reset();
        return response('OK', Response::HTTP_OK);
    }

    public function balance(Request $request)
    {
        $accountid = $request->query('account_id');
        try {
            $balance = $this->accountService->getBalance($accountid);
        } catch (AccountNotFoundException $e) {
            return response('0', Response::HTTP_NOT_FOUND);
        }

        return response($balance, Response::HTTP_OK);
    }
}
