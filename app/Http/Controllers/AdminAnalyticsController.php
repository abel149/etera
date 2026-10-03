<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PaidUser;
use App\Models\ProformaInvoice;
use Illuminate\Http\Request;

class AdminAnalyticsController extends Controller
{
    // ---------------------------
    // ADMIN ANALYTICS PAGE
    // ---------------------------
public function index()
{
    abort_unless(in_array(auth()->user()->role, ['admin', 'superadmin', 'accountant']), 403);

    $currentUser = auth()->user();

    $users = User::whereIn('role', ['garage', 'shop', 'insurance', 'operator'])->get();

    // Load all insurance_agent users in one query, grouped by parent_insurance_id
    $allAgents = User::where('role', 'insurance_agent')
        ->whereNotNull('parent_insurance_id')
        ->orderBy('name')
        ->get()
        ->groupBy('parent_insurance_id');

    $garageShopUsers = $this->processUsers(
        $users->whereIn('role', ['garage', 'shop'])
    );

    $insuranceUsers = $this->processInsuranceUsers(
        $users->where('role', 'insurance'),
        $allAgents
    );

    $operatorUsers = $this->processUsers(
        $users->where('role', 'operator')
    );

    $allUsers = $garageShopUsers
        ->merge($insuranceUsers)
        ->merge($operatorUsers)
        ->keyBy(fn ($u) => $u->user->id);

    switch ($currentUser->role) {
        case 'admin':
            return view('admin.analytics.index', compact(
                'garageShopUsers',
                'insuranceUsers',
                'operatorUsers',
                'allUsers'
            ));
        default:
            return view('accountant.dashboard', compact(
                'garageShopUsers',
                'insuranceUsers',
                'operatorUsers',
                'allUsers'
            ));
    }
}


    // ---------------------------
    // MARK USER AS PAID
    // ---------------------------
    public function markPaid($userId)
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'superadmin', 'accountant']), 403);

        $unpaid = PaidUser::where('user_id', $userId)
            ->where('is_paid', false)
            ->get();

        if ($unpaid->isEmpty()) {
            return back()->with('error', 'User has no remaining balance.');
        }

        foreach ($unpaid as $row) {
            $row->markAsPaid();
        }

        return back()->with('success', 'All unpaid entries marked as paid.');
    }

public function receivePayment($userId)
{
    abort_unless(in_array(auth()->user()->role, ['admin', 'superadmin', 'accountant']), 403);

    // Get all unpaid insurance invoices for this user
    $unpaid = ProformaInvoice::whereHas('proforma', function ($q) use ($userId) {
        $q->where('poster_id', $userId);
    })->where('is_paid', false)->get();

    if ($unpaid->isEmpty()) {
        return back()->with('error', 'User has no remaining balance.');
    }

    foreach ($unpaid as $row) {
        $row->markAsPaid(); // your existing model function
    }

    return back()->with('success', 'All unpaid invoices marked as paid.');
}


    // ---------------------------
    // PROCESS INSURANCE USERS (with agent aggregation)
    // ---------------------------
    private function processInsuranceUsers($insurances, $allAgents)
    {
        if ($insurances->isEmpty()) return collect();

        $insuranceIds = $insurances->pluck('id')->toArray();

        // Build flat list of all agent users + a map: agent_id → parent_insurance_id
        $allAgentUsers   = $allAgents->flatten(1);
        $agentIds        = $allAgentUsers->pluck('id')->toArray();
        $agentParentMap  = $allAgentUsers->pluck('parent_insurance_id', 'id');

        // All poster IDs (insurance accounts + all their agents) for batch loading
        $allPosterIds = array_merge($insuranceIds, $agentIds);

        // 1. PaidUser records for the insurance accounts themselves (agents don't earn commissions)
        $allPaidUsers = PaidUser::whereIn('user_id', $insuranceIds)
            ->get()
            ->groupBy('user_id');

        // 2. All invoices for proformas posted by insurance accounts OR any of their agents
        $allInvoices = ProformaInvoice::whereHas('proforma', function ($q) use ($allPosterIds) {
            $q->whereIn('poster_id', $allPosterIds)->where('insured', true);
        })
        ->with('proforma:id,poster_id')
        ->get();

        // Group invoices by agent's poster_id for the per-agent breakdown
        $invoicesByPosterId = $allInvoices->groupBy(fn ($inv) => $inv->proforma->poster_id);

        // Group ALL invoices by the root insurance id (agents mapped to their parent)
        $invoicesByInsuranceId = $allInvoices->groupBy(function ($inv) use ($agentParentMap, $insuranceIds) {
            $posterId = $inv->proforma->poster_id;
            return $agentParentMap->get($posterId, $posterId);
        });

        return $insurances->map(function ($user) use (
            $allPaidUsers, $invoicesByInsuranceId, $invoicesByPosterId, $allAgents, $agentParentMap
        ) {
            $agents = $allAgents->get($user->id, collect());

            /* === PaidUser (commissions) for this insurance account === */
            $paidUsers   = $allPaidUsers->get($user->id, collect());
            $totalEarned = $paidUsers->sum('amount');
            $totalPaid   = $paidUsers->where('is_paid', true)->sum('amount');
            $remaining   = $totalEarned - $totalPaid;

            /* === Invoices: insurance account + ALL agents combined === */
            $invoices      = $invoicesByInsuranceId->get($user->id, collect());
            $invoiceTotal  = $invoices->sum('total_amount');
            $invoicePaid   = $invoices->where('is_paid', true)->sum('total_amount');
            $invoiceUnpaid = $invoiceTotal - $invoicePaid;

            /* === Per-agent breakdown (already-loaded data, no extra queries) === */
            $agentBreakdown = $agents->map(function ($agent) use ($invoicesByPosterId) {
                $agentInvoices = $invoicesByPosterId->get($agent->id, collect());
                return (object) [
                    'id'      => $agent->id,
                    'name'    => $agent->name,
                    'total'   => (float) $agentInvoices->sum('total_amount'),
                    'paid'    => (float) $agentInvoices->where('is_paid', true)->sum('total_amount'),
                    'pending' => (float) $agentInvoices->where('is_paid', false)->sum('total_amount'),
                    'count'   => $agentInvoices->count(),
                ];
            })->values();

            return (object) [
                'user'  => $user,
                'role'  => $user->role,

                'filled_applications' => $paidUsers->whereNotNull('application_id')->count(),
                'filled_proformas'    => $paidUsers->whereNotNull('proforma_id')->count(),

                'total_earned' => $totalEarned,
                'total_paid'   => $totalPaid,
                'remaining'    => $remaining,

                // Combined insurance + agents invoice totals
                'insurance_proforma_count'  => $invoices->count(),
                'insurance_proforma_total'  => $invoiceTotal,
                'insurance_proforma_paid'   => $invoicePaid,
                'insurance_proforma_unpaid' => $invoiceUnpaid,

                'invoices'        => $invoices,
                'transactions'    => $paidUsers,
                'agents'          => $agents,
                'agent_breakdown' => $agentBreakdown,
            ];
        })->values();
    }

    // ---------------------------
    // PROCESS USERS
    // ---------------------------
    private function processUsers($users)
    {
        $userIds = $users->pluck('id')->toArray();
        if (empty($userIds)) return collect();

        // Batch load all PaidUser records in one query
        $allPaidUsers = PaidUser::whereIn('user_id', $userIds)
            ->get()
            ->groupBy('user_id');

        // Batch load all insurance invoices in one query (insurance users only)
        $insuranceUserIds = $users->where('role', 'insurance')->pluck('id')->toArray();
        $allInvoices = collect();
        if (!empty($insuranceUserIds)) {
            $allInvoices = ProformaInvoice::whereHas('proforma', function ($q) use ($insuranceUserIds) {
                $q->whereIn('poster_id', $insuranceUserIds)->where('insured', true);
            })
            ->with('proforma:id,poster_id')
            ->get()
            ->groupBy(fn ($inv) => $inv->proforma->poster_id);
        }

        return $users->map(function ($user) use ($allPaidUsers, $allInvoices) {

            /* ================= PaidUser ================= */
            $paidUsers = $allPaidUsers->get($user->id, collect());

            $totalEarned = $paidUsers->sum('amount');
            $totalPaid   = $paidUsers->where('is_paid', true)->sum('amount');
            $remaining   = $totalEarned - $totalPaid;

            /* ================= Insurance Proformas ================= */
            $invoiceCount  = 0;
            $invoiceTotal  = 0;
            $invoicePaid   = 0;
            $invoiceUnpaid = 0;
            $invoices = collect();

            if ($user->role === 'insurance') {
                $invoices      = $allInvoices->get($user->id, collect());
                $invoiceCount  = $invoices->count();
                $invoiceTotal  = $invoices->sum('total_amount');
                $invoicePaid   = $invoices->where('is_paid', true)->sum('total_amount');
                $invoiceUnpaid = $invoiceTotal - $invoicePaid;
            }

            return (object) [
                'user' => $user,
                'role' => $user->role,

                'filled_applications' => $paidUsers->whereNotNull('application_id')->count(),
                'filled_proformas'    => $paidUsers->whereNotNull('proforma_id')->count(),

                'total_earned' => $totalEarned,
                'total_paid'   => $totalPaid,
                'remaining'    => $remaining,

                // Insurance only
                'insurance_proforma_count'  => $invoiceCount,
                'insurance_proforma_total'  => $invoiceTotal,
                'insurance_proforma_paid'   => $invoicePaid,
                'insurance_proforma_unpaid' => $invoiceUnpaid,

                'invoices'      => $invoices,
                'transactions'  => $paidUsers,
            ];
        })->values();
    }
}
