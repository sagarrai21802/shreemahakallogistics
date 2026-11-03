<?php

namespace App\Http\Controllers;

use Database\Seeders\DemoDataSeeder;
use Illuminate\Http\RedirectResponse;

class DemoDataController extends Controller
{
    public function reset(): RedirectResponse
    {
        abort_if(app()->environment('production'), 403);

        (new DemoDataSeeder)->run();

        return redirect()
            ->route('dashboard')
            ->with('status', 'Demo data has been reset.');
    }
}
