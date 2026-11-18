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

    /** Validation for an optional bank account on admin/marketer user-create forms: both fields or neither. */
    public static function optionalRules(): array
    {
        return [
            'bank_name'      => ['nullable', 'required_with:account_number', \Illuminate\Validation\Rule::in(array_keys(config('etera.banks', [])))],
            'account_number' => ['nullable', 'required_with:bank_name', 'string', 'max:50'],
        ];
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
