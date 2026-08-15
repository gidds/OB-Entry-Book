<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'OB Entry Book')</title>
    <style>
        :root { font-family: Arial, Helvetica, sans-serif; color: #1f2937; background: #f3f4f6; }
        * { box-sizing: border-box; }
        body { margin: 0; }
        header { background: #111827; color: #fff; padding: 1rem 1.25rem; }
        nav { display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; }
        nav strong { margin-right: .5rem; }
        nav a { color: #fff; text-decoration: none; }
        main { width: min(1200px, 100%); margin: 0 auto; padding: 1.25rem; }
        .panel { background: #fff; border-radius: .5rem; padding: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,.12); }
        .entry { padding: .9rem 0; border-bottom: 1px solid #e5e7eb; }
        .entry:last-child { border-bottom: 0; }
        .muted { color: #6b7280; font-size: .9rem; }
        .status { margin-bottom: 1rem; padding: .75rem; background: #dcfce7; border-radius: .35rem; }
        .error { margin-bottom: 1rem; padding: .75rem; background: #fee2e2; border-radius: .35rem; }
        label { display: block; margin-top: 1rem; font-weight: 600; }
        input, textarea { width: 100%; padding: .7rem; margin-top: .35rem; border: 1px solid #d1d5db; border-radius: .35rem; }
        textarea { min-height: 180px; resize: vertical; }
        button, .button { display: inline-block; margin-top: 1rem; padding: .7rem 1rem; border: 0; border-radius: .35rem; background: #111827; color: #fff; text-decoration: none; cursor: pointer; }
        .top-actions { display: flex; justify-content: space-between; gap: 1rem; align-items: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
<header>
    <nav>
        <strong>Digital Security Occurrence Book</strong>
        <a href="{{ route('entries.index') }}">Home</a>
        <a href="{{ route('entries.create') }}">Add Entry</a>
    </nav>
</header>
<main>
    @if(session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif

    @yield('content')
</main>
</body>
</html>
