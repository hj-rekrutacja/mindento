<?php

namespace App\Http\Controllers;

use App\Delegation\Calculator;
use App\Delegation\Delegation;
use App\Http\Requests\StoreDelegationRequest;
use App\Worker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class DelegationController extends Controller
{
    public function index(Worker $worker): Collection|array
    {
        return $worker->delegations;
    }

    public function store(StoreDelegationRequest $request, Calculator $calculator): JsonResponse
    {
        $delegation = new Delegation($request->safe()->toArray());
        $delegation->amount_due = $calculator->calculate($delegation->start, $delegation->end, $delegation->country);
        $delegation->save();
        return response()->json($delegation->fresh(), 201);
    }
}
