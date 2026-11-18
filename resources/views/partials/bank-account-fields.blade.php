{{-- Optional payout bank account (validated by BankAccount::optionalRules()).
     Pass $bankAccount (BankAccount|null) on edit forms to prefill; clearing both fields removes it. --}}
@php
    $bankAccount   = $bankAccount ?? null;
    $selectedBank  = old('bank_name', $bankAccount?->bank_name);
    $bankOptions   = config('etera.banks', []);
    if ($selectedBank && !array_key_exists($selectedBank, $bankOptions)) {
        $bankOptions = [$selectedBank => $selectedBank] + $bankOptions; // keep legacy value selectable
    }
@endphp
<input type="hidden" name="bank_account_form" value="1">

<div class="col-md-6">
    <label for="bank_name" class="form-label">Bank / Payment Provider <small class="text-muted">(optional)</small></label>
    <select name="bank_name" id="bank_name" class="form-select @error('bank_name') is-invalid @enderror">
        <option value="">Select bank or Telebirr</option>
        @foreach($bankOptions as $value => $label)
            <option value="{{ $value }}" @selected($selectedBank === $value)>{{ $label }}</option>
        @endforeach
    </select>
    @error('bank_name')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

<div class="col-md-6">
    <label for="account_number" class="form-label">Account Number <small class="text-muted">(optional)</small></label>
    <input name="account_number" id="account_number" type="text" inputmode="numeric" maxlength="50"
           class="form-control @error('account_number') is-invalid @enderror"
           value="{{ old('account_number', $bankAccount?->account_number) }}" placeholder="Bank account or Telebirr number">
    @error('account_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
