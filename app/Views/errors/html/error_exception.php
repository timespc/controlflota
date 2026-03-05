<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d32f2f;
            margin-bottom: 20px;
        }
        .error-message {
            background: #ffebee;
            padding: 15px;
            border-left: 4px solid #d32f2f;
            margin: 20px 0;
        }
        .file-info {
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Error</h1>
        <div class="error-message">
            <strong><?= esc($title ?? 'An error occurred') ?></strong>
            <?php if (isset($message)): ?>
                <p><?= esc($message) ?></p>
            <?php endif; ?>
        </div>
        <?php if (isset($file) && isset($line)): ?>
            <div class="file-info">
                <strong>File:</strong> <?= esc($file) ?><br>
                <strong>Line:</strong> <?= esc($line) ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>


