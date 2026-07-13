<?php

namespace App\Http\Controllers;

use App\Models\ApplicationPdf;
use App\Models\Inbox;
use App\Models\Partial;
use App\Models\Proforma;
use App\Models\ProformaApplication;
use App\Models\ProformaPartPrice;
use App\Models\User;
use App\Notifications\ProformaApplicationReceived;
use App\Services\ProformaGroupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProformaApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // This method is not yet implemented.
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // This method is not yet implemented.
    }

    /**
     * Store a newly created resource in storage.
     * This method handles the complex logic of submitting a price quote.
     */
    public function store(Request $request, Proforma $proforma)
    {
        try {
            // Wrap everything in a transaction with row-level locking to prevent race conditions
            return \Illuminate\Support\Facades\DB::transaction(function () use ($request, $proforma) {

                // Lock the proforma row to prevent simultaneous applications
                $proforma = Proforma::where('id', $proforma->id)->lockForUpdate()->first();

                if (!$proforma || !in_array($proforma->status, ['pending', 'published', 'opened'])) {
                    $redirectUrl = auth()->user()->role === 'garage' ? '/garage/proformas' : '/spare-part-shops/proformas';
                    return redirect($redirectUrl)->with('error', 'This proforma is no longer accepting applications.');
                }

                // Step 1: Determine proforma type
                $requiredGarages = (int) ($proforma->required_number_of_garages ?? 0);
                $requiredShops = (int) ($proforma->required_number_of_shops ?? 0);
                $isEteraChereta = ($requiredGarages + $requiredShops) === 0;

                // Step 1b: Logging
                Log::info('Price quote submission: started', [
                    'proforma_id' => $proforma->id ?? null,
                    'user_id' => auth()->id(),
                    'role' => auth()->user()->role ?? null,
                    'applications_count' => $proforma->applications()->count(),
                ]);

                $totalApplications = $proforma->applications()->count();
                $isInboxedUser = $proforma->inboxes()->where('user_id', auth()->id())->exists();
                $hasInboxedUsers = $proforma->inboxes()->exists();

                // Step 2: Validate the request data based on the user's role.
                $isEncrypted = $request->boolean('prices_encrypted', false);

                if (auth()->user()->role === 'garage') {
                    if ($isEncrypted) {
                        $request->validate(['encrypted_amount' => 'required|string']);
                    } else {
                        $request->validate([
                            'amount' => 'required|numeric|min:1',
                            'discount' => 'nullable|numeric|min:0|max:100',
                        ], [
                            'amount.required' => 'Price is required.',
                            'amount.numeric' => 'Price must be a valid number.',
                            'amount.min' => 'Price must be at least 1.',
                            'discount.numeric' => 'Discount must be a valid number.',
                            'discount.min' => 'Discount cannot be negative.',
                            'discount.max' => 'Discount cannot exceed 100%.',
                        ]);
                    }
                } else { // 'shop' role
                    // Check for PDF early so we can bypass price validation for PDF-only
                    $hasPdf = $request->filled('encrypted_pdf') || $request->filled('pdf_data');

                    if ($isEncrypted && !$hasPdf) {
                        $request->validate(['encrypted_total' => 'required|array']);
                    } elseif ($isEncrypted && $hasPdf) {
                        // PDF-only with encrypted=1 flag: encrypted_total is optional
                    } else {
                        $request->validate([
                            'total' => 'nullable|array',
                            'total.*' => 'nullable|numeric|min:1',
                            'discount' => 'nullable|numeric|min:0|max:100',
                        ], [
                            'total.*.numeric' => 'Unit price must be a valid number.',
                            'total.*.min' => 'Unit price must be at least 1. Leave the field blank if you do not carry this part.',
                            'discount.numeric' => 'Discount must be a valid number.',
                            'discount.min' => 'Discount cannot be negative.',
                            'discount.max' => 'Discount cannot exceed 100%.',
                        ]);

                        $hasAtLeastOnePrice = collect($request->input('total', []))
                            ->filter(fn($v) => $v !== null && floatval($v) > 0)
                            ->isNotEmpty();

                        if (!$hasAtLeastOnePrice && !$hasPdf) {
                            return redirect()->back()
                                ->withErrors(['total' => 'Please enter a price for at least one part. Leave fields blank only for parts you do not carry.'])
                                ->withInput();
                        }
                    }
                }

                Log::info('Price quote submission: validation passed', [
                    'proforma_id' => $proforma->id,
                    'role' => auth()->user()->role ?? null,
                    'discount' => $request->discount ?? null,
                    'shop_parts_count' => is_array($request->total ?? null) ? count($request->total) : null,
                ]);

                // Resolve $hasPdf for garage role (shop sets it above)
                $hasPdf = $hasPdf ?? ($request->filled('encrypted_pdf') || $request->filled('pdf_data'));

                // Step 2b: Insurance proformas require encrypted submissions — always.
                // Exception: a PDF-only submission counts as acceptable (PDF is encrypted client-side).
                if (!$isEncrypted && optional($proforma->poster)->role === 'insurance' && !$hasPdf) {
                    $redirectUrl = auth()->user()->role === 'garage' ? '/garage/proformas' : '/spare-part-shops/proformas';
                    return redirect($redirectUrl)
                        ->withErrors(['general' => 'Encrypted price submission is required for this proforma. Please contact the insurance.'])
                        ->withInput();
                }

                // Step 3: Calculate the final amount (0 placeholder when encrypted).
                $finalAmount = 0;
                $discount = $request->discount ?? 0;

                if ($isEncrypted) {
                    // Encrypted mode: amount is a ciphertext; store 0 as numeric placeholder
                    $finalAmount = 0;
                } elseif (auth()->user()->role === 'garage') {
                    $initialPrice = $request->amount;
                    $discountAmount = ($initialPrice * $discount) / 100;
                    $finalAmount = $initialPrice - $discountAmount;
                    $finalAmount = max($finalAmount, 1);
                } else { // 'shop' role
                    $totalAmount = 0;
                    foreach ($proforma->parts->sortBy('id')->values() as $index => $part) {
                        $unitPrice = floatval($request->total[$index] ?? 0);
                        if ($unitPrice > 0) {
                            $quantity = $part->quantity ?? 1;
                            $partTotal = $unitPrice * $quantity;
                            $totalAmount += $partTotal;
                        }
                    }
                    $discountAmount = ($totalAmount * $discount) / 100;
                    $finalAmount = $totalAmount - $discountAmount;
                    // PDF-only submission has no price; avoid forcing min:1
                    $isPdfOnlyCalc = $hasPdf && $totalAmount == 0;
                    if (!$isPdfOnlyCalc) {
                        $finalAmount = max($finalAmount, 1);
                    }
                }

                Log::info('Price quote submission: totals computed', [
                    'proforma_id' => $proforma->id,
                    'final_amount' => $finalAmount,
                    'discount' => $discount,
                    'role' => auth()->user()->role ?? null,
                ]);

                // Step 4: Detect inbox source AND group BEFORE deleting inbox
                $role     = auth()->user()->role;
                $ownInbox = $proforma->inboxes()->where('user_id', auth()->id())->first();
                $isInsuranceInboxed = $ownInbox && $ownInbox->source === 'insurance';
                $isAdminInboxed     = $ownInbox && $ownInbox->source === 'admin';
                $inboxGroup         = $ownInbox?->inbox_group; // 1, 2, 3, or null (legacy)

                $applicationSource = $isInsuranceInboxed ? 'partner' : ($isAdminInboxed ? 'admin' : 'public');

                // Step 4a: For shop submissions, resolve the actual group number.
                $groupService = new ProformaGroupService();
                $isPartialApplication = false;

                if ($role === 'shop' && $requiredShops > 0) {
                    // Check if shop is responding to a Partial broadcast notification
                    $ownPartial = Partial::where('proforma_id', $proforma->id)
                        ->where('user_id', auth()->id())
                        ->where('active', true)
                        ->first();

                    Log::info('Partial check', [
                        'proforma_id'     => $proforma->id,
                        'user_id'         => auth()->id(),
                        'own_partial_id'  => $ownPartial?->id,
                        'own_partial_group' => $ownPartial?->inbox_group,
                        'own_partial_active' => $ownPartial?->active,
                        'inbox_group_before' => $inboxGroup,
                    ]);

                    if ($ownPartial) {
                        // Partial mode: use the group from the Partial record
                        $inboxGroup = $ownPartial->inbox_group;
                        $isPartialApplication = true;
                    } elseif ($inboxGroup === null) {
                        // Null-group (admin-float) or public browse: try empty group first
                        $inboxGroup = $groupService->autoAssignGroup($proforma);

                        // Admin-floated shops: fall back to first incomplete group when all slots
                        // already have some prices (partial fills)
                        if ($inboxGroup === null && $isAdminInboxed) {
                            $inboxGroup = $groupService->findFirstIncompleteGroup($proforma);
                        }

                        if ($inboxGroup === null) {
                            return redirect()->back()->with('error', 'All available slots are currently being filled. You may receive a notification if additional pricing is needed.');
                        }
                    }
                    // Insurance-inboxed with real group number: $inboxGroup already set correctly
                }

                // For non-group proformas (required_number_of_shops = 0), the inbox_group on the
                // shop's inbox record is just a counter with no slot meaning.  Writing it into
                // proforma_part_prices would hit the unique constraint when two shops share the
                // same counter value.  Use null so the constraint is bypassed for normal flow.
                $priceGroup = ($requiredShops > 0) ? $inboxGroup : null;

                // Step 4b: Create a new application record.
                $appData = [
                    'application_by'    => auth()->id(),
                    'from'              => $role,
                    'amount'            => $finalAmount,
                    'discount'          => $isEncrypted ? 0 : $discount,
                    'notes'             => $request->filled('notes') ? trim($request->notes) : null,
                    'application_source'=> $applicationSource,
                    'inbox_group'       => $inboxGroup,
                ];
                if ($isEncrypted && $request->filled('encrypted_amount')) {
                    $appData['encrypted_amount']   = $request->encrypted_amount;
                    $appData['amount_is_encrypted'] = true;
                }
                $application = $proforma->applications()->create($appData);

                // Remove own inbox record (insurance or admin)
                \App\Models\Inbox::where('user_id', auth()->id())
                    ->where('proforma_id', $proforma->id)
                    ->delete();

                // Chereta (legacy null-group only): when quota of insurance partners applied, clear null-group inboxes.
                // Per-group chereta is now deferred to after prices are saved (fires on group completion).
                if ($isInsuranceInboxed && $inboxGroup === null) {
                    $roleUserIds = User::where('role', $role)->pluck('id');
                    $partnerApplied = $proforma->applications()
                        ->where('from', $role)
                        ->where('application_source', 'partner')
                        ->count();
                    $quota = $role === 'shop'
                        ? (int) ($proforma->insurance_shop_quota ?? 1)
                        : (int) ($proforma->insurance_garage_quota ?? 1);
                    if ($partnerApplied >= $quota) {
                        $proforma->inboxes()
                            ->where('source', 'insurance')
                            ->whereNull('inbox_group')
                            ->whereIn('user_id', $roleUserIds)
                            ->delete();
                    }
                }

                Log::info('Price quote submission: application created', [
                    'proforma_id' => $proforma->id,
                    'application_id' => $application->id,
                    'from' => $application->from,
                    'amount' => $application->amount,
                ]);

                // Step 4b: Handle PDF upload
                if ($hasPdf) {
                    try {
                        if ($request->filled('encrypted_pdf')) {
                            ApplicationPdf::create([
                                'application_id'    => $application->id,
                                'storage_type'      => 'encrypted',
                                'encrypted_pdf'     => $request->encrypted_pdf,
                                'encrypted_aes_key' => $request->encrypted_aes_key,
                                'aes_iv'            => $request->aes_iv,
                                'original_filename' => $request->pdf_filename ?? 'quotation.pdf',
                            ]);
                        } elseif ($request->filled('pdf_data')) {
                            ApplicationPdf::create([
                                'application_id'    => $application->id,
                                'storage_type'      => 'plain',
                                'encrypted_pdf'     => $request->pdf_data,
                                'original_filename' => $request->pdf_filename ?? 'quotation.pdf',
                            ]);
                        }
                        Log::info('Application PDF stored', ['application_id' => $application->id]);
                    } catch (\Exception $e) {
                        Log::error('Failed to store application PDF: ' . $e->getMessage());
                    }
                }

                // Step 5: Handle voice note uploads.
                if ($request->has('voice_note') && !empty($request->voice_note)) {
                    try {
                        $voiceNoteData = $request->voice_note;
                        if (strpos($voiceNoteData, 'data:audio') === 0) {
                            $base64Data = explode(',', $voiceNoteData)[1];
                            $audioData = base64_decode($base64Data);
                            $filename = 'voice_note_' . time() . '_' . uniqid() . '.webm';
                            $path = 'voice_notes/' . $filename;
                            Storage::disk('public')->put($path, $audioData);

                            $application->addMediaFromDisk($path, 'public')
                                ->toMediaCollection('voice_notes');
                            
                            Log::info('Voice note uploaded successfully', [
                                'application_id' => $application->id,
                                'filename' => $filename,
                                'path' => $path
                            ]);
                        }
                    } catch (\Exception $e) {
                        Log::error('Error uploading voice note: ' . $e->getMessage());
                    }
                }

                // Step 6: Calculate progress for notifications
                $requiredShopsForNotif = (int) ($proforma->required_number_of_shops ?? 0);
                $requiredGaragesForNotif = (int) ($proforma->required_number_of_garages ?? 0);
                $totalRequired = $requiredShopsForNotif + $requiredGaragesForNotif;
                $currentCount = $proforma->applications()->count();

                // Step 6a: Send Telegram notification to the poster about the new application
                try {
                    if ($proforma->poster && !empty($proforma->poster->telegram_chat_id)) {
                        $telegram = new \App\Services\TelegramService();
                        $telegram->sendApplicationReceivedNotification(
                            $proforma->poster->telegram_chat_id,
                            $proforma,
                            auth()->user()->role
                        );
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to send application received Telegram notification', [
                        'proforma_id' => $proforma->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Step 7: Save individual part prices for shops.
                $isPdfOnly = $hasPdf
                    && !$request->filled('encrypted_total')
                    && empty(array_filter($request->input('total', [])));

                $filledPartsCount = 0;
                $skippedPartsCount = 0;

                if (auth()->user()->role === 'shop' && !$isPdfOnly) {
                    $partsProcessed = 0;
                    $totalPartsCount = $proforma->parts()->count();

                    // Pre-fetch already-priced car_part_ids for this group to skip locked parts.
                    // Only count rows with a real price (unit_price > 0 OR encrypted) so that
                    // legacy zero-price rows never permanently block a part from being priced.
                    // $priceGroup is null for normal (non-group) proformas; skip the lookup.
                    $alreadyPricedCarPartIds = ($priceGroup !== null)
                        ? ProformaPartPrice::where('proforma_id', $proforma->id)
                            ->where('inbox_group', $priceGroup)
                            ->where(function ($q) {
                                $q->where('unit_price', '>', 0)
                                  ->orWhere('price_is_encrypted', true);
                            })
                            ->pluck('car_part_id')
                            ->toArray()
                        : [];

                    foreach ($proforma->parts->sortBy('id')->values() as $index => $part) {
                        $quantity = $part->quantity ?? 1;

                        // Use the ProformaPart's own id as the CarPart name so each part always
                        // maps to a unique car_part_id — prevents duplicate-key errors when
                        // multiple proforma parts share the same component/category name.
                        $resolvedCarPartId = \App\Models\CarPart::firstOrCreate([
                            'name' => 'ppart_' . $part->id,
                        ], [
                            'component' => $part->component ?: 'Mechanical Parts',
                        ])->id;

                        // Skip if this part is already priced in this group (locked by an earlier shop)
                        if (in_array($resolvedCarPartId, $alreadyPricedCarPartIds)) {
                            $skippedPartsCount++;
                            continue;
                        }

                        if ($isEncrypted) {
                            $encryptedPrice = $request->encrypted_total[$index] ?? null;
                            // Skip entirely if no encrypted value provided for this slot
                            if (!$encryptedPrice) { continue; }
                            $application->prices()->create([
                                'car_part_id'          => $resolvedCarPartId,
                                'proforma_id'          => $proforma->id,
                                'inbox_group'          => $priceGroup,
                                'quantity'             => $quantity,
                                'unit_price'           => 0,
                                'part_total'           => 0,
                                'encrypted_unit_price' => $encryptedPrice,
                                'encrypted_part_total' => null,
                                'price_is_encrypted'   => true,
                            ]);
                            $partsProcessed++; $filledPartsCount++;
                        } else {
                            $unitPrice = floatval($request->total[$index] ?? 0);
                            // Skip blank/zero-price entries — don't pollute the group with
                            // zero rows that would block partial triggering and the unique constraint.
                            if ($unitPrice <= 0) { continue; }
                            $partTotal = $unitPrice * $quantity;
                            $application->prices()->create([
                                'car_part_id' => $resolvedCarPartId,
                                'proforma_id' => $proforma->id,
                                'inbox_group' => $priceGroup,
                                'quantity'    => $quantity,
                                'unit_price'  => $unitPrice,
                                'part_total'  => $partTotal,
                            ]);
                            $partsProcessed++; $filledPartsCount++;
                        }
                    }

                    // Track partial fill stats on the application record
                    $application->update([
                        'filled_parts_count' => $filledPartsCount,
                        'total_parts_count'  => $totalPartsCount,
                        'is_partial'         => $filledPartsCount < $totalPartsCount,
                    ]);

                    if ($skippedPartsCount > 0) {
                        Log::info('Price quote submission: some parts were already priced in this group', [
                            'proforma_id'        => $proforma->id,
                            'inbox_group'        => $inboxGroup,
                            'skipped_parts'      => $skippedPartsCount,
                            'filled_parts'       => $filledPartsCount,
                        ]);
                    }

                    // Per-group chereta: if the group is now complete, delete remaining group inboxes
                    if ($inboxGroup !== null && $groupService->isGroupComplete($proforma, $inboxGroup)) {
                        $proforma->inboxes()
                            ->where('source', 'insurance')
                            ->where('inbox_group', $inboxGroup)
                            ->delete();
                        Partial::deactivateGroup($proforma->id, $inboxGroup);

                        Log::info('Price quote submission: group complete, chereta fired', [
                            'proforma_id' => $proforma->id,
                            'inbox_group' => $inboxGroup,
                        ]);
                    }
                }

                // Clear any Partial records for this shop on this proforma (one-submission rule)
                Partial::clearForUser($proforma->id, auth()->id());

                // Step 8: Check if the proforma should be closed.
                // For shop-only insurance proformas: close when all required GROUPS are fully priced.
                // For garage/etera proformas: use existing application-count logic (unchanged).
                $garageApplicationsCount = $proforma->applications()->where('from', 'garage')->count();

                $garageRequirementMet = $requiredGarages === 0 || $garageApplicationsCount >= $requiredGarages;

                if ($requiredShops > 0 && !$isEteraChereta) {
                    // New: count groups where all parts are priced
                    $totalPartsForClose = $proforma->parts()->count();
                    $completeGroupCount = $totalPartsForClose > 0
                        ? ProformaPartPrice::where('proforma_id', $proforma->id)
                            ->select('inbox_group')
                            ->groupBy('inbox_group')
                            ->havingRaw('COUNT(DISTINCT car_part_id) >= ?', [$totalPartsForClose])
                            ->count()
                        : 0;
                    $shopRequirementMet = $completeGroupCount >= $requiredShops;
                } else {
                    $shopApplicationsCount = $proforma->applications()->where('from', 'shop')->count();
                    $shopRequirementMet = $requiredShops === 0 || $shopApplicationsCount >= $requiredShops;
                }

                if (!$isEteraChereta && $garageRequirementMet && $shopRequirementMet) {
                    $closingService = new \App\Services\ProformaClosingService();
                    $closingService->closeProforma($proforma, auth()->id());

                    Log::info('Price quote submission: proforma closed via service (requirements met)', [
                        'proforma_id' => $proforma->id,
                        'application_id' => $application->id,
                    ]);
                }

                // Step 8b: Post-submit partial trigger — check if this group now needs broadcast help.
                // Runs outside the closing check so partials fire even when the proforma stays open.
                if ($role === 'shop' && $inboxGroup !== null && !($garageRequirementMet && $shopRequirementMet)) {
                    $groupService->checkAndTriggerPartials($proforma, $inboxGroup);
                }

                // Step 9: Redirect with a success message.
                $redirectUrl = route('role.proformas');
                Log::info('Price quote submission: completed', [
                    'proforma_id' => $proforma->id,
                    'application_id' => $application->id,
                    'redirect' => $redirectUrl,
                ]);
                return redirect($redirectUrl)->with('success', 'Price quote submitted successfully!');

            }); // end DB::transaction

        } catch (\Throwable $e) {
            Log::error('Price quote submission: failed', [
                'proforma_id' => $proforma->id ?? null,
                'user_id' => auth()->id() ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Failed to submit price quote. Please try again.');
        }
    }
    
    /**
     * Display the specified resource.
     * This method is not yet implemented.
     */
    public function show(ProformaApplication $proformaApplication)
    {
        // This method is intentionally empty. The logic to set a proforma as "not new"
        // should be in the ProformaController's `show` method, as that is when a proforma is viewed.
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProformaApplication $proformaApplication)
    {
        // This method is not yet implemented.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProformaApplication $proformaApplication)
    {
        // This method is not yet implemented.
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProformaApplication $proformaApplication)
    {
        // This method is not yet implemented.
    }
}
