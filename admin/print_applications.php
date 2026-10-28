<?php
// print_applications.php - Generates a printable HTML version of the application list

// Include database connection
include('db_connect.php');

// Get company settings
$company_query = $conn->query("SELECT * FROM company_settings WHERE id = 1");
$company_info = $company_query->fetch_assoc();

// Default values if no settings exist
$company_name = $company_info['company_name'] ?? 'YOUR COMPANY NAME';
$company_address = $company_info['company_address'] ?? '123 Company Address, City, State ZIP';
$company_phone = $company_info['company_phone'] ?? '123-456-7890';
$company_email = $company_info['company_email'] ?? 'contact@yourcompany.com';

// Get company settings
$company_query = $conn->query("SELECT * FROM company_settings WHERE id = 1");
$company_info = $company_query->fetch_assoc();

// Default values if no settings exist
$company_name = $company_info['company_name'] ?? 'YOUR COMPANY NAME';
$company_address = $company_info['company_address'] ?? '123 Company Address, City, State ZIP';
$company_phone = $company_info['company_phone'] ?? '123-456-7890';
$company_email = $company_info['company_email'] ?? 'contact@yourcompany.com';

// Get position filter if set
$position_id = 'all';
if (isset($_GET['position_id']) && $_GET['position_id'] !== 'all') {
    $position_id = $_GET['position_id'];
}

// Get process ID filter if set
$pid = 'all';
if (isset($_GET['pid']) && $_GET['pid'] !== 'all') {
    $pid = $_GET['pid'];
}

// Get position names
$qry = $conn->query("SELECT * FROM vacancy");
$pos = [];
while ($row = $qry->fetch_assoc()) {
    $pos[$row['id']] = $row['position'];
}

// Get recruitment status labels
$stats = $conn->query("SELECT * FROM recruitment_status ORDER BY id ASC");
$stat_arr = [0 => "New"];
while ($row = $stats->fetch_assoc()) {
    $stat_arr[$row['id']] = $row['status_label'];
}

// Build WHERE clause for filtering
$awhere = '';
if ($pid !== 'all') {
    $awhere = " WHERE a.process_id = '$pid' ";
}
if ($position_id !== 'all') {
    if (empty($awhere)) {
        $awhere = " WHERE a.position_id = '$position_id' ";
    } else {
        $awhere .= " AND a.position_id = '$position_id' ";
    }
}

// Query to get application list with the filters applied (now includes email)
$application = $conn->query("SELECT a.*, v.position FROM application a INNER JOIN vacancy v ON v.id = a.position_id $awhere ORDER BY a.id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application List - <?php echo htmlspecialchars($company_name); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .company-info {
            margin-bottom: 20px;
            line-height: 1.4;
        }
        .document-title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .date-section {
            text-align: right;
            margin-bottom: 20px;
        }
        .filter-info {
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }
        .applicant-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .applicant-table th, .applicant-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .applicant-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .applicant-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .email-col {
            max-width: 200px;
            word-break: break-all;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .settings-btn {
            padding: 8px 16px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
            text-decoration: none;
            display: inline-block;
        }
        .settings-btn:hover {
            background-color: #218838;
        }
        .settings-link {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }
        .settings-link:hover {
            background-color: #218838;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
                font-size: 11pt;
            }
            .applicant-table {
                font-size: 10pt;
            }
            .applicant-table th {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .applicant-table tr:nth-child(even) {
                background-color: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="no-print">
            <a href="company_settings.php" class="settings-link">⚙️ Edit Company Info</a>
            <button onclick="window.print();" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print This List</button>
            <button onclick="window.location.href='index.php?page=applications&pid=<?php echo $pid; ?>&position_id=<?php echo $position_id; ?>';" style="padding: 8px 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 5px;">Back to Applications</button>
        </div>
        
        <div class="header">
            <h1><?php echo htmlspecialchars($company_name); ?></h1>
            <div class="company-info">
                <?php echo nl2br(htmlspecialchars($company_address)); ?>
            </div>
            <div>Phone: <?php echo htmlspecialchars($company_phone); ?> | Email: <?php echo htmlspecialchars($company_email); ?></div>
        </div>
        
        <div class="document-title">APPLICATION LIST</div>
        
        <div class="date-section">
            <strong>Date Generated:</strong> <?php echo date("F j, Y"); ?>
        </div>
        
        <?php if ($position_id !== 'all' || $pid !== 'all'): ?>
        <div class="filter-info">
            <strong>Filters Applied:</strong>
            <?php if ($position_id !== 'all'): ?>
                <div>Position: <?php echo $pos[$position_id]; ?></div>
            <?php endif; ?>
            <?php if ($pid !== 'all'): ?>
                <div>Status: <?php echo $stat_arr[$pid]; ?></div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <table class="applicant-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position Applied</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                while ($row = $application->fetch_assoc()):
                    $full_name = ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']);
                ?>
                <tr>
                    <td><?php echo $i++; ?></td>
                    <td><?php echo $full_name; ?></td>
                    <td class="email-col"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo $row['position']; ?></td>
                    <td><?php echo $stat_arr[$row['process_id']]; ?></td>
                </tr>
                <?php endwhile; ?>
                
                <?php if ($i == 1): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">No applicants found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <div class="footer">
            <p>This document contains confidential information and is intended for internal use only.</p>
            <p>Generated on <?php echo date("F j, Y"); ?> | <?php echo htmlspecialchars($company_name); ?></p>
        </div>
    </div>
</body>
</html>