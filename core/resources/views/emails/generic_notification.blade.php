<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ $title }}</title>
	<style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 40px 0;
            color: #333;
        }
        .mail-container {
            background-color: #ffffff;
            max-width: 640px;
            margin: auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
        }
        .mail-header {
            background-color: #0d6efd;
            padding: 30px 20px 20px;
            color: #fff;
            text-align: center;
        }
        .mail-header img {
            max-height: 60px;
            margin-bottom: 12px;
        }
        .mail-header h1 {
            font-size: 24px;
            margin: 0;
        }
        .mail-body {
            padding: 40px;
            line-height: 1.75;
            font-size: 16px;
            color: #212529;
        }
        .mail-body h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #0d6efd;
        }
        .mail-body p {
            margin-bottom: 20px;
        }
        .mail-footer {
            background-color: #f1f3f5;
            padding: 25px 40px;
            text-align: center;
            font-size: 13px;
            color: #6c757d;
        }
        .mail-footer a {
            color: #0d6efd;
            text-decoration: none;
        }
	</style>
</head>
<body>
<div class="mail-container">
	<div class="mail-header">
		<img src="{{ asset(setting('logo')) }}" alt="{{ setting('site_title') }} Logo">
		<h1>{{ setting('site_title') }}</h1>
	</div>
	<div class="mail-body">
		<h2>{{ $title }}</h2>
		<p>{!! nl2br(e($body)) !!}</p>
	</div>
	<div class="mail-footer">
		<p>{{ __('This is an automated message from') }} <strong>{{ setting('site_title') }}</strong>. {{ __('If you did not expect this message, please ignore it.') }}</p>
		<p>&copy; {{ date('Y') }} {{ setting('site_title') }}. {{ __('All rights reserved') }}.</p>
	</div>
</div>
</body>
</html>