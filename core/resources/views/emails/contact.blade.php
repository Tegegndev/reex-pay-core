<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>{{ __('New Contact Message') }}</title>
</head>
<body style="margin: 0; padding: 20px; background-color: #f9f9f9; font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif; color: #333;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width: 600px; margin: auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
	<tr>
		<td style="background-color: #4F46E5; padding: 20px; text-align: center;">
			<h2 style="margin: 0; color: #ffffff; font-size: 24px;">{{ __('New Contact Message') }}</h2>
		</td>
	</tr>
	<tr>
		<td style="padding: 30px 20px;">
			<p style="margin: 0 0 15px;"><strong>{{ __('Name') }}:</strong> {{ $name }}</p>
			<p style="margin: 0 0 15px;"><strong>{{ __('Email') }}:</strong> {{ $email }}</p>
			<p style="margin: 0 0 15px;"><strong>{{ __('Phone') }}:</strong> {{ $phone }}</p>
			<p style="margin: 0 0 15px;"><strong>{{ __('Subject') }}:</strong> {{ $subject }}</p>
			<p style="margin: 20px 0 10px;"><strong>{{ __('Message') }}:</strong></p>
			<div style="padding: 15px; background-color: #f1f5f9; border-radius: 5px; color: #555; font-size: 14px; line-height: 1.6;">
				{!! nl2br(e($body)) !!}
			</div>
		</td>
	</tr>
	<tr>
		<td style="background-color: #f3f4f6; text-align: center; padding: 15px; font-size: 12px; color: #888;">
			{{ __('This email was sent from the contact form.') }}
		</td>
	</tr>
</table>
</body>
</html>
