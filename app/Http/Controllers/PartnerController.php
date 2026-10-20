<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use App\Models\User;

class PartnerController extends Controller
{
    public function index()
    {
    }

    public function store(Request $request)
    {

        $request->validate([
            'partners'   => 'required|array|min:1',
            'partners.*' => 'integer',
        ]);

        $ownerId = auth()->user()->insuranceId();

        $validIds = User::whereIn('id', $request->partners)
            ->whereIn('role', ['shop', 'garage'])
            ->where('approved', true)
            ->pluck('id');

        $existing = Partner::where('insurance_id', $ownerId)
            ->whereIn('partner_id', $validIds)
            ->pluck('partner_id');

        $now  = now();
        $rows = $validIds->diff($existing)->map(fn ($id) => [
            'insurance_id' => $ownerId,
            'partner_id'   => $id,
            'added_by'     => auth()->id(),
            'created_at'   => $now,
            'updated_at'   => $now,
        ])->values()->all();

        if ($rows) {
            Partner::insert($rows);
        }

        return redirect()->back()->with('success', count($rows) . ' partner(s) added.');
    }

    public function destroy($partnerId)
    {
        Partner::where('insurance_id', auth()->user()->insuranceId())
            ->findOrFail($partnerId)
            ->delete();

        return redirect()->back()->with('success', 'Partner removed successfully.');
    }

}
