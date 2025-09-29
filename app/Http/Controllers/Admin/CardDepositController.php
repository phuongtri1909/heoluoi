<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

use App\Models\CardDeposit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CardDepositController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = CardDeposit::with(['user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by card type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('request_id', 'like', "%{$search}%")
                    ->orWhere('serial', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $cardDeposits = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pages.deposits.card.index', compact('cardDeposits'));
    }
}
