<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = request()->user();

        if (in_array($user->role, ['admin', 'satpam'], true)) {
            return redirect()->route('campus.admin.dashboard', [
                'campus' => $user->campus->slug,
            ]);
        }

        return redirect()->route('campus.my.history', [
            'campus' => $user->campus->slug,
        ]);
    }
}
