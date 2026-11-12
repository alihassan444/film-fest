<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Upload too large</title>
</head>
<body>
    <main>
        <h1>Upload too large</h1>
        <p>{{ $message }}</p>
        <p>Your submission was not saved. Please return to the form and select a smaller receipt.</p>
        <a href="{{ route('form') }}">Return to ticket form</a>
    </main>
</body>
</html>
