<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Hotspot Aktif - Countdown</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
        }
        .container {
            text-align: center;
            background: rgba(255,255,255,0.1);
            padding: 50px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        #countdown-display {
            font-size: 72px;
            font-weight: bold;
            margin: 20px 0;
            font-family: 'Courier New', monospace;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.4);
            letter-spacing: 10px;
        }
        #status-message {
            margin-top: 20px;
            font-size: 1.2em;
            color: #f0f0f0;
            min-height: 30px;
        }
        .loading {
            animation: pulse 1.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔥 Hotspot Aktif</h1>
        <div id="countdown-display" class="loading">Loading...</div>
        <p id="status-message">Menginisialisasi...</p>
    </div>

    <script>
        // Ambil durasi dari controller
        const durationSeconds = <?php echo json_encode($duration ?? 60, 15, 512) ?>;
    </script>
    <script src="<?php echo e(asset('js/countdown.js')); ?>"></script>
</body>
</html><?php /**PATH C:\laragon\www\wifi\resources\views/countdown.blade.php ENDPATH**/ ?>