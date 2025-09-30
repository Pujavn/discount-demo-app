<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DiscountDemoController extends Controller
{
    public function index()
    {
        // Ensure a demo user exists
        $user = User::first() ?? User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        // Fetch all active discounts for listing
        $discounts = \PujaNaik\UserDiscount\Models\Discount::where('active', true)
            ->orderByDesc('id')
            ->get();

        // Seed one if empty (to make the page self-explanatory)
        if ($discounts->isEmpty()) {
            \PujaNaik\UserDiscount\Models\Discount::create([
                'name' => 'Welcome 10%',
                'slug' => 'welcome10',
                'active' => true,
                'percent' => 10,
                'per_user_cap' => 3,
            ]);
            $discounts = \PujaNaik\UserDiscount\Models\Discount::where('active', true)->get();
        }

        // Show audits for the FIRST discount by default (or none)
        $selected = $discounts->first();

        $usageCount = $selected ? DB::table('discount_audits')
            ->where('discount_id', $selected->id)
            ->where('user_id', $user->id)
            ->where('action', 'applied')
            ->count() : 0;

        $audits = $selected ? DB::table('discount_audits')
            ->where('discount_id', $selected->id)
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get() : collect();

        return view('discount-demo', [
            'user'       => $user,
            'discounts'  => $discounts,    // list for table & dropdown
            'selected'   => $selected,     // currently focused discount (for audits/usage)
            'usageCount' => $usageCount,
            'audits'     => $audits,
            'result'     => null,
        ]);
    }

    public function apply(Request $request)
    {
        $data = $request->validate([
            'amount_rupees'   => ['required','numeric','min:0'],
            'application_key' => ['nullable','string','max:255'],
            'discount_slug'   => ['required','string','max:255'], // pick which discount to apply
        ]);

        $user = User::first() ?? User::factory()->create([
            'name' => 'Demo User', 'email' => 'demo@example.com'
        ]);

        // Find the chosen discount by slug (404 if not found)
        $discount = \PujaNaik\UserDiscount\Models\Discount::where('slug', $data['discount_slug'])
            ->where('active', true)
            ->firstOrFail();

        // Assign (idempotent)
        \Discounts::assign($user, $discount);

        // Apply
        $subtotalMinor = (int) round(((float) $data['amount_rupees']) * 100);
        $key = $data['application_key'] ?: ('ORDER#'.now()->timestamp);
        $appliedMinor = (int) \Discounts::apply($user, $subtotalMinor, $key);

        // Recompute usage/audits (for the selected discount)
        $usageCount = DB::table('discount_audits')
            ->where('discount_id', $discount->id)
            ->where('user_id', $user->id)
            ->where('action', 'applied')
            ->count();

        $audits = DB::table('discount_audits')
            ->where('discount_id', $discount->id)
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Keep the full list visible
        $discounts = \PujaNaik\UserDiscount\Models\Discount::where('active', true)
            ->orderByDesc('id')
            ->get();

        return view('discount-demo', [
            'user'       => $user,
            'discounts'  => $discounts,
            'selected'   => $discount,
            'usageCount' => $usageCount,
            'audits'     => $audits,
            'result'     => [
                'subtotalMinor' => $subtotalMinor,
                'appliedMinor'  => $appliedMinor,
                'payableMinor'  => max(0, $subtotalMinor - $appliedMinor),
                'key'           => $key,
            ],
        ]);
    }
}
