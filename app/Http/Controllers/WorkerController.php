<?php

namespace App\Http\Controllers;

use App\Worker;
use Carbon\Carbon;

class WorkerController extends Controller
{
    public function store(): array
    {
        $a = Carbon::now();
        $a->diffInWeekdays();
        return Worker::factory()->create()->only('id');
    }
}
