<?php
function checkMaintenanceMode($allow_admin_bypass = true) {
    $maintenance_file = __DIR__ . '/maintenance_mode.txt';
    $maintenance_mode = 'off';
    if (file_exists($maintenance_file)) {
        $maintenance_mode = trim(file_get_contents($maintenance_file));
    }
    if ($maintenance_mode === 'on') {
        $is_admin = isset($_SESSION['admin_email']);
        if (!$is_admin) {
            showMaintenancePage();
        } elseif ($is_admin && !$allow_admin_bypass) {
            showMaintenancePage();
        }
    }
}

function showMaintenancePage() {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Under Maintenance - Avestra Travel Agency</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', 'Roboto', 'Arial', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .maintenance-container {
                background: white;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                padding: 60px 40px;
                max-width: 600px;
                text-align: center;
                animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            }
            
            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .maintenance-icon {
                font-size: 80px;
                margin-bottom: 30px;
                animation: spin 3s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            h1 {
                font-size: 2.5em;
                color: #22304a;
                margin-bottom: 15px;
                font-weight: 700;
            }
            
            p {
                font-size: 1.1em;
                color: #718096;
                line-height: 1.8;
                margin-bottom: 20px;
            }
            
            .status {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 25px;
                border-radius: 8px;
                font-weight: 600;
                display: inline-block;
                margin-top: 30px;
            }
            
            .info-text {
                font-size: 0.95em;
                color: #a0b4c8;
                margin-top: 40px;
                padding-top: 30px;
                border-top: 1px solid #e2e8f0;
            }
        </style>
    </head>
    <body>
        <div class="maintenance-container">
            <div class="maintenance-icon">🔧</div>
            <h1>Maintenance is On</h1>
            <p>Wait a few minutes</p>
            <p>We're currently performing scheduled maintenance to improve your experience with Avestra Travel Agency.</p>
            <div class="status">Status: Maintenance in Progress</div>
            <div class="info-text">
                <p>Thank you for your patience. Please try again later.</p>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}
?>
