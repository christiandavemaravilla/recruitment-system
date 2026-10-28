<?php
// company_settings.php - Page to manage company information

// Include database connection
include('db_connect.php');

// Handle form submission
if ($_POST) {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $company_address = $conn->real_escape_string($_POST['company_address']);
    $company_phone = $conn->real_escape_string($_POST['company_phone']);
    $company_email = $conn->real_escape_string($_POST['company_email']);
    
    // Check if settings already exist
    $check = $conn->query("SELECT * FROM company_settings WHERE id = 1");
    
    if ($check->num_rows > 0) {
        // Update existing settings
        $update_query = "UPDATE company_settings SET 
                        company_name = '$company_name',
                        company_address = '$company_address', 
                        company_phone = '$company_phone',
                        company_email = '$company_email',
                        updated_at = NOW()
                        WHERE id = 1";
        
        if ($conn->query($update_query)) {
            $success_msg = "Company information updated successfully!";
        } else {
            $error_msg = "Error updating company information: " . $conn->error;
        }
    } else {
        // Insert new settings
        $insert_query = "INSERT INTO company_settings (id, company_name, company_address, company_phone, company_email, created_at, updated_at) 
                        VALUES (1, '$company_name', '$company_address', '$company_phone', '$company_email', NOW(), NOW())";
        
        if ($conn->query($insert_query)) {
            $success_msg = "Company information saved successfully!";
        } else {
            $error_msg = "Error saving company information: " . $conn->error;
        }
    }
}

// Get current company settings
$settings_query = $conn->query("SELECT * FROM company_settings WHERE id = 1");
$settings = $settings_query->fetch_assoc();

// Default values if no settings exist
$company_name = $settings['company_name'] ?? 'YOUR COMPANY NAME';
$company_address = $settings['company_address'] ?? '123 Company Address, City, State ZIP';
$company_phone = $settings['company_phone'] ?? '123-456-7890';
$company_email = $settings['company_email'] ?? 'contact@yourcompany.com';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Settings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        .form-group textarea {
            resize: vertical;
            height: 80px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        .btn-success:hover {
            background-color: #218838;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .alert-error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .preview-section {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }
        .preview-section h3 {
            margin-top: 0;
            color: #007bff;
        }
        .company-preview {
            text-align: center;
            border: 2px solid #333;
            padding: 15px;
            border-radius: 5px;
            background-color: white;
        }
        .company-preview h2 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .button-group {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Company Settings</h1>
            <p>Manage your company information for reports and documents</p>
        </div>
        
        <?php if (isset($success_msg)): ?>
        <div class="alert alert-success">
            <?php echo $success_msg; ?>
        </div>
        <?php endif; ?>
        
        <?php if (isset($error_msg)): ?>
        <div class="alert alert-error">
            <?php echo $error_msg; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="company_name">Company Name:</label>
                <input type="text" id="company_name" name="company_name" value="<?php echo htmlspecialchars($company_name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="company_address">Company Address:</label>
                <textarea id="company_address" name="company_address" required><?php echo htmlspecialchars($company_address); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="company_phone">Phone Number:</label>
                <input type="text" id="company_phone" name="company_phone" value="<?php echo htmlspecialchars($company_phone); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="company_email">Email Address:</label>
                <input type="email" id="company_email" name="company_email" value="<?php echo htmlspecialchars($company_email); ?>" required>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-primary">💾 Save Company Information</button>
                <a href="print_applications.php" class="btn btn-success">📄 Back to Print Page</a>
                <a href="index.php" class="btn btn-secondary">🏠 Back to Dashboard</a>
            </div>
        </form>
        
        <div class="preview-section">
            <h3>📋 Preview</h3>
            <p>This is how your company information will appear on printed documents:</p>
            <div class="company-preview">
                <h2><?php echo htmlspecialchars($company_name); ?></h2>
                <div><?php echo nl2br(htmlspecialchars($company_address)); ?></div>
                <div>Phone: <?php echo htmlspecialchars($company_phone); ?> | Email: <?php echo htmlspecialchars($company_email); ?></div>
            </div>
        </div>
    </div>

    <script>
        // Live preview update as user types
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = ['company_name', 'company_address', 'company_phone', 'company_email'];
            
            inputs.forEach(function(inputId) {
                document.getElementById(inputId).addEventListener('input', updatePreview);
            });
            
            function updatePreview() {
                const name = document.getElementById('company_name').value || 'YOUR COMPANY NAME';
                const address = document.getElementById('company_address').value || '123 Company Address, City, State ZIP';
                const phone = document.getElementById('company_phone').value || '123-456-7890';
                const email = document.getElementById('company_email').value || 'contact@yourcompany.com';
                
                const preview = document.querySelector('.company-preview');
                preview.innerHTML = `
                    <h2>${escapeHtml(name)}</h2>
                    <div>${escapeHtml(address).replace(/\n/g, '<br>')}</div>
                    <div>Phone: ${escapeHtml(phone)} | Email: ${escapeHtml(email)}</div>
                `;
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        });
    </script>
</body>
</html>