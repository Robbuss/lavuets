<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MollieWebhookController extends Controller
{
    public function handle()
    {
        return ['success' => true];
    }
}
