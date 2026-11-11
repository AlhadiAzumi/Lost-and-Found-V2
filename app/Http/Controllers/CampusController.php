<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use Illuminate\View\View;

class CampusController extends Controller
{
    public function index(): View
    {
        return view('campuses.index', [
            'campuses' => Campus::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }
}
