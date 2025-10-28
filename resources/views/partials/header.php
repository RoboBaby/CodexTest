<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Prompt Editor', ENT_QUOTES) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f7f7f7; color: #222; }
        header { background: #0f172a; color: #fff; padding: 1rem 2rem; }
        header h1 { margin: 0; font-size: 1.5rem; }
        nav a { color: #cbd5f5; margin-right: 1rem; text-decoration: none; font-weight: bold; }
        nav a:hover { text-decoration: underline; }
        main { padding: 2rem; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1.5rem; background: #fff; }
        th, td { border: 1px solid #d1d5db; padding: 0.75rem; text-align: left; }
        th { background: #1e293b; color: #fff; }
        form { margin-bottom: 1.5rem; background: #fff; padding: 1rem; border: 1px solid #d1d5db; border-radius: 8px; }
        label { display: block; font-weight: bold; margin-bottom: 0.25rem; }
        input[type="text"], textarea, select, input[type="number"] { width: 100%; padding: 0.5rem; margin-bottom: 1rem; border: 1px solid #d1d5db; border-radius: 4px; }
        input[type="checkbox"] { margin-right: 0.5rem; }
        button { background: #2563eb; border: none; color: #fff; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; }
        button.secondary { background: #64748b; }
        .message { background: #ecfccb; padding: 0.75rem 1rem; border: 1px solid #84cc16; border-radius: 6px; margin-bottom: 1rem; }
        .error { color: #dc2626; font-size: 0.875rem; margin-top: -0.75rem; margin-bottom: 1rem; }
        .stacked { display: flex; gap: 1rem; flex-wrap: wrap; }
        .stacked > * { flex: 1 1 240px; }
        .actions { display: flex; gap: 0.5rem; }
        .section-group { background: #fff; border: 1px solid #d1d5db; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; }
        .section-group h3 { margin-top: 0; }
        pre { background: #0f172a; color: #e0f2fe; padding: 1rem; border-radius: 8px; overflow-x: auto; }
    </style>
</head>
<body>
<header>
    <h1>AskGVT Prompt Editor</h1>
    <nav>
        <a href="/versions">Prompt Versions</a>
        <a href="/sections">Sections</a>
    </nav>
</header>
<main>
