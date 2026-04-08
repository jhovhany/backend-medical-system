<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical API Server Status</title>
    <style>
        :root {
            --bg: #f4f8ff;
            --card: #ffffff;
            --text: #1e293b;
            --muted: #64748b;
            --ok: #059669;
            --fail: #dc2626;
            --btn: #0f766e;
            --btn-hover: #115e59;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 20% 20%, #dbeafe 0%, transparent 40%),
                radial-gradient(circle at 80% 80%, #ccfbf1 0%, transparent 45%),
                var(--bg);
            padding: 24px;
        }

        .card {
            width: min(560px, 100%);
            background: var(--card);
            border-radius: 18px;
            padding: 28px;
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 1.7rem;
        }

        p {
            margin: 0 0 22px;
            color: var(--muted);
        }

        .status-line {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            background: #9ca3af;
            transition: background-color 0.25s ease;
        }

        .status-ok .dot { background: var(--ok); }
        .status-fail .dot { background: var(--fail); }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        button {
            appearance: none;
            border: 0;
            border-radius: 12px;
            padding: 12px 18px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            color: #ffffff;
            background: var(--btn);
            transition: transform 0.12s ease, background-color 0.2s ease;
        }

        button:hover { background: var(--btn-hover); }
        button:active { transform: translateY(1px); }

        .meta {
            margin-top: 16px;
            font-size: 0.9rem;
            color: var(--muted);
        }

        code {
            background: #f1f5f9;
            border-radius: 8px;
            padding: 2px 6px;
            color: #334155;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>Server Status</h1>
    <p>Use the button to check if the API is responding correctly.</p>

    <div id="statusLine" class="status-line">
        <span class="dot"></span>
        <span id="statusText">Not checked yet</span>
    </div>

    <div class="actions">
        <button id="checkStatusBtn" type="button">Check Server Status</button>
    </div>

    <div class="meta" id="metaText">
        Endpoint: <code>/api/health</code>
    </div>
</div>

<script>
    const statusLine = document.getElementById('statusLine');
    const statusText = document.getElementById('statusText');
    const metaText = document.getElementById('metaText');
    const checkStatusBtn = document.getElementById('checkStatusBtn');

    async function checkStatus() {
        statusLine.classList.remove('status-ok', 'status-fail');
        statusText.textContent = 'Checking...';

        try {
            const response = await fetch('/api/health', {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('Unexpected status code: ' + response.status);
            }

            const data = await response.json();

            statusLine.classList.add('status-ok');
            statusText.textContent = 'ONLINE';
            metaText.innerHTML = 'Last check: <code>' + new Date().toLocaleString() + '</code> | API timestamp: <code>' + (data.timestamp ?? 'n/a') + '</code>';
        } catch (error) {
            statusLine.classList.add('status-fail');
            statusText.textContent = 'OFFLINE';
            metaText.innerHTML = 'Last check: <code>' + new Date().toLocaleString() + '</code> | Error: <code>' + error.message + '</code>';
        }
    }

    checkStatusBtn.addEventListener('click', checkStatus);
</script>
</body>
</html>
