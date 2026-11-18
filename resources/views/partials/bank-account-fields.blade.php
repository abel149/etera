{{-- Optional payout bank account (validated by BankAccount::optionalRules()) --}}
<div class="col-md-6">
    <label for="bank_name" class="form-label">Bank / Payment Provider <small class="text-muted">(optional)</small></label>
    <select name="bank_name" id="bank_name" class="form-select @error('bank_name') is-invalid @enderror">
        <option value="">Select bank or Telebirr</option>
        @foreach(config('etera.banks', []) as $value => $label)
            <option value="{{ $value }}" @selected(old('bank_name') === $value)>{{ $label }}</option>
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
           value="{{ old('account_number') }}" placeholder="Bank account or Telebirr number">
    @error('account_number')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
