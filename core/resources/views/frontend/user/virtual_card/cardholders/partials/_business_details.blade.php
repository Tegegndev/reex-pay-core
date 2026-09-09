<div class="row g-3 mt-1 business-fields">
	<div class="col-md-6">
		<label for="business_name" class="form-label">@lang('Business Name')</label>
		<input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $business->business_name ?? '') }}" placeholder="@lang('Enter business name')">
	</div>
	<div class="col-md-6">
		<label for="registration_number" class="form-label">@lang('Registration Number')</label>
		<input type="text" class="form-control" id="registration_number" name="registration_number" value="{{ old('registration_number', $business->registration_number ?? '') }}" placeholder="@lang('Enter registration number')">
	</div>
	<div class="col-md-6">
		<label for="tin" class="form-label">@lang('TIN')</label>
		<input type="text" class="form-control" id="tin" name="tin" value="{{ old('tin', $business->tin ?? '') }}" placeholder="@lang('Enter TIN')">
	</div>
	<div class="col-md-6">
		<label for="business_type" class="form-label">@lang('Business Type')</label>
		<input type="text" class="form-control" id="business_type" name="business_type" value="{{ old('business_type', $business->business_type ?? '') }}" placeholder="@lang('Enter business type')">
	</div>
	<div class="col-md-6">
		<label for="contact_email" class="form-label">@lang('Contact Email')</label>
		<input type="email" class="form-control" id="contact_email" name="contact_email" value="{{ old('contact_email', $business->contact_email ?? '') }}" placeholder="@lang('Enter contact email')">
	</div>
	<div class="col-md-6">
		<label for="contact_phone" class="form-label">@lang('Contact Phone')</label>
		<input type="text" class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $business->contact_phone ?? '') }}" placeholder="@lang('Enter contact phone')">
	</div>
	<div class="col-md-12">
		<label for="address_line1_b" class="form-label">@lang('Business Address Line 1')</label>
		<input type="text" class="form-control" id="address_line1_b" name="address_line1_b" value="{{ old('address_line1_b', $business->address_line1 ?? '') }}" placeholder="@lang('Business street address')">
	</div>
	<div class="col-md-12">
		<label for="address_line2_b" class="form-label">@lang('Business Address Line 2')</label>
		<input type="text" class="form-control" id="address_line2_b" name="address_line2_b" value="{{ old('address_line2_b', $business->address_line2 ?? '') }}" placeholder="@lang('Business address line 2')">
	</div>
	<div class="col-md-4">
		<label for="city_b" class="form-label">@lang('City')</label>
		<input type="text" class="form-control" id="city_b" name="city_b" value="{{ old('city_b', $business->city ?? '') }}" placeholder="@lang('Enter city')">
	</div>
	<div class="col-md-4">
		<label for="state_b" class="form-label">@lang('State')</label>
		<input type="text" class="form-control" id="state_b" name="state_b" value="{{ old('state_b', $business->state ?? '') }}" placeholder="@lang('Enter state')">
	</div>
	<div class="col-md-4">
		<label for="postal_code_b" class="form-label">@lang('Postal Code')</label>
		<input type="text" class="form-control" id="postal_code_b" name="postal_code_b" value="{{ old('postal_code_b', $business->postal_code ?? '') }}" placeholder="@lang('Enter postal/ZIP code')">
	</div>
	<div class="col-md-4">
		<label for="country_b" class="form-label">@lang('Country')</label>
		<select class="form-select" id="country_b" name="country_b">
			<option value="">@lang('Select Country')</option>
			@foreach($allCountries as $country)
				<option value="{{ $country['code']}}" {{ old('country_b', $business->country ?? '') == $country['code'] ? 'selected' : '' }}>
					{{ title($country['name']) }}
				</option>
			@endforeach
		</select>
	</div>
</div>
