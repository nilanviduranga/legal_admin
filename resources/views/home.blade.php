<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Protection Law AI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f8fafc;
            color: #1f2937;
        }
        .container {
            max-width: 760px;
            margin: 64px auto;
            padding: 32px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        h1 {
            margin-top: 0;
            font-size: 28px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin: 24px 0;
        }
        .stat {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            background: #f9fafb;
        }
        .label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }
        .value {
            font-size: 22px;
            font-weight: 700;
        }
        .note {
            color: #4b5563;
            margin-top: 20px;
        }
        .admin-link {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 16px;
            background: #d97706;
            color: #ffffff;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
        }
        .admin-link:hover {
            background: #b45309;
        }
    </style>
</head>
<body>
    <main class="container">
        <h1>Consumer Protection Law AI</h1>
        <p>Backend schema and models are configured for Acts, Amendments, versioned Law Sections, and chat sessions/messages.</p>

        <section class="stats">
            <article class="stat">
                <div class="label">Acts</div>
                <div class="value">{{ $actsCount }}</div>
            </article>
            <article class="stat">
                <div class="label">All Law Sections</div>
                <div class="value">{{ $sectionsCount }}</div>
            </article>
            <article class="stat">
                <div class="label">Current Versions</div>
                <div class="value">{{ $currentSectionsCount }}</div>
            </article>
        </section>

        <p class="note">Use the admin panel to manage acts, law sections, chat data, and admin users.</p>
        <a class="admin-link" href="{{ route('admin.acts.index') }}" style="margin-right:8px">Law Admin</a>
    </main>
</body>
</html>
