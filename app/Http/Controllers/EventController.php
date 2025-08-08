<?php

namespace App\Http\Controllers;

use App\Exceptions\AccountNotFoundException;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EventController extends Controller
{
    protected EventService $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function event(Request $request)
    {
        $type = $request->input('type');
        $amount = $request->input('amount');

        switch($type) {
            case 'deposit':
                return $this->deposit($request, $amount);
            case 'withdraw':
                return $this->withdraw($request, $amount);
            case 'transfer':
                return $this->transfer($request, $amount);
            default:
                return response()->json([
                    'error' => 'Invalid event type.'
                ], Response::HTTP_BAD_REQUEST);
        }
    }

    protected function deposit(Request $request, $amount)
    {
        $accountId = $request->input('destination');
        $response = $this->eventService->deposit($accountId, $amount);
        return response()->json($response, Response::HTTP_CREATED);
    }

    protected function withdraw(Request $request, $amount)
    {
        $accountId = $request->input('origin');
        try {
            $response = $this->eventService->withdraw($accountId, $amount);
        } catch (AccountNotFoundException $e) {
            return response('0', Response::HTTP_NOT_FOUND);
        }
        return response()->json($response, Response::HTTP_CREATED);
    }

    protected function transfer(Request $request, $amount)
    {
        $originAccountId = $request->input('origin');
        $destinationAccountId = $request->input('destination');
        try {
            $response = $this->eventService->transfer($originAccountId, $destinationAccountId, $amount);
        } catch (AccountNotFoundException $e) {
            return response('0', Response::HTTP_NOT_FOUND);
        }
        return response()->json($response, Response::HTTP_CREATED);
    }
}
