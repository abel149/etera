<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bank_name',
        'account_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Validation for an optional bank account on admin/marketer user forms: both fields or neither.
     * $currentBank keeps a legacy bank name (not in config) valid when editing.
     */
    public static function optionalRules(?string $currentBank = null): array
    {
        $allowed = array_keys(config('etera.banks', []));
        if ($currentBank) {
            $allowed[] = $currentBank;
        }

        return [
            'bank_name'      => ['nullable', 'required_with:account_number', \Illuminate\Validation\Rule::in($allowed)],
            'account_number' => ['nullable', 'required_with:bank_name', 'string', 'max:50'],
        ];
    }

    /** The account shown on proformas/invoices (first by id). */
    public static function primaryFor(User $user): ?self
    {
        return $user->bankAccounts()->orderBy('id')->first();
    }

    /**
     * Edit forms: update / create / remove the user's primary account.
     * Only runs when the form actually contains the bank fields (bank_account_form marker),
     * so self-service profile updates that share the same endpoint never touch bank data.
     */
    public static function syncFromRequest(User $user, \Illuminate\Http\Request $request): void
    {
        if (!$request->has('bank_account_form')) {
            return;
        }

        $account = self::primaryFor($user);

        if (!$request->filled('bank_name') && !$request->filled('account_number')) {
            $account?->delete();
            return;
        }

        $values = ['bank_name' => $request->bank_name, 'account_number' => trim($request->account_number)];
        $account ? $account->update($values) : $user->bankAccounts()->create($values);
    }

    /** Attach the submitted bank account (if any) to $user. */
    public static function createFromRequest(User $user, \Illuminate\Http\Request $request): void
    {
        if ($request->filled('bank_name') && $request->filled('account_number')) {
            $user->bankAccounts()->create([
                'bank_name'      => $request->bank_name,
                'account_number' => trim($request->account_number),
            ]);
        }
    }
}
