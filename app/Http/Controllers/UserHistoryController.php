<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserHistoryController extends Controller
{
    public function index(Request $request): View
    {
        return view('my.history', [
            'tab' => $request->query('tab', 'reports'),
            'reports' => Item::query()
                ->where('user_id', $request->user()->id)
                ->with('category')
                ->latest()
                ->paginate(10, ['*'], 'reports_page'),
            'claims' => Claim::query()
                ->where('user_id', $request->user()->id)
                ->with(['item.category'])
                ->latest()
                ->paginate(10, ['*'], 'claims_page'),
        ]);
    }
}
