<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../public/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>❌ Error</h1>
            <p>Something went wrong</p>
        </div>
        
        <div class="content">
            <div class="error">
                <h2><?php echo $errorTitle ?? 'Application Error'; ?></h2>
                <p><?php echo $errorMessage ?? 'An unexpected error occurred. Please try again.'; ?></p>
                
                <?php if (isDevelopment() && isset($errorDetails)): ?>
                    <details>
                        <summary>Error Details (Development Mode)</summary>
                        <pre><?php echo htmlspecialchars($errorDetails); ?></pre>
                    </details>
                <?php endif; ?>
            </div>
            
            <div class="actions">
                <a href="../public/index.php" class="btn">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>

