<?php include('db_connect.php'); ?>
<?php
$qry = $conn->query("SELECT * FROM vacancy ");
$pos = [];
while ($row = $qry->fetch_assoc()) {
    $pos[$row['id']] = $row['position'];
}
$pid = 'all';
if (isset($_GET['pid']) && $_GET['pid'] !== 'all') {
    $pid = $_GET['pid'];
}
$position_id = 'all';
if (isset($_GET['position_id']) && $_GET['position_id'] !== 'all') {
    $position_id = $_GET['position_id'];
}

// Get status labels
$stats = $conn->query("SELECT * FROM recruitment_status ORDER BY id ASC");
$stat_arr = [];  // Removed the manual "New" entry
while ($row = $stats->fetch_assoc()) {
    $stat_arr[$row['id']] = $row['status_label'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Management</title>
    <!-- Add Bootstrap CSS if not already included in your header -->
    <style>
        .card {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .card-body {
            padding: 20px;
        }
        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0;
        }
        .action-buttons .btn {
            margin-left: 10px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filter-label {
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            padding-right: 10px;
        }
        .applicant-info {
            padding: 8px 0;
        }
        .applicant-name {
            font-weight: 600;
            color: #333;
        }
        .applicant-position {
            color: #666;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }
        .status-new { background-color: #17a2b8; }
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .status-filter-card .btn {
            margin-bottom: 8px;
            border-radius: 6px;
            text-align: left;
            padding: 8px 12px;
            transition: all 0.3s;
        }
        .status-filter-card .btn:hover {
            opacity: 0.9;
        }
        .status-filter-card .btn i {
            margin-right: 8px;
        }
        .btn-action {
            margin: 0 3px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content (Applications) -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="page-title">Application List</h5>
                    <div class="action-buttons">
                        <a href="print_applications.php?pid=<?php echo $pid; ?>&position_id=<?php echo $position_id; ?>" class="btn btn-sm btn-outline-info">
                            <i class="fa fa-print"></i> Print View
                        </a>
                        <button class="btn btn-sm btn-primary" type="button" id="new_application">
                            <i class="fa fa-plus"></i> New Applicant
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="row">
                            <div class="col-md-3 col-lg-2">
                                <div class="filter-label">Position:</div>
                            </div>
                            <div class="col-md-6 col-lg-5">
                                <select class="form-control" id="position_filter">
                                    <option value="all" <?php echo ($position_id == "all") ? "selected" : ''; ?>>All Positions</option>
                                    <?php foreach ($pos as $k => $v): ?>
                                        <option value="<?php echo $k; ?>" <?php echo ($position_id == $k) ? "selected" : ''; ?>><?php echo $v; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Applications Table -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="50%">Applicant Information</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="30%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;

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

                                $application = $conn->query("SELECT a.*, v.position FROM application a INNER JOIN vacancy v ON v.id = a.position_id $awhere ORDER BY a.id ASC");
                                
                                if ($application->num_rows > 0):
                                    while ($row = $application->fetch_assoc()):
                                        // Determine status color class
                                        $statusClass = "status-new"; // Default class
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="applicant-info">
                                            <div class="applicant-name"><?php echo ucwords($row['lastname'] . ', ' . $row['firstname'] . ' ' . $row['middlename']); ?></div>
                                            <div class="applicant-position">Position: <?php echo $row['position']; ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo isset($stat_arr[$row['process_id']]) ? $stat_arr[$row['process_id']] : 'New'; ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary btn-action view_application" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-outline-success btn-action edit_application" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-action delete_application" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                <tr>
                                    <td colspan="4" class="text-center">No applications found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Filter Sidebar -->
        <div class="col-lg-3">
            <div class="card status-filter-card">
                <div class="card-header">
                    <h5 class="page-title">Filter by Status</h5>
                </div>
                <div class="card-body">
                    <a href="index.php?page=applications&pid=all&position_id=<?php echo $position_id; ?>" class="btn btn-block <?php echo ($pid == 'all') ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                        <i class="fa fa-list"></i> All Applications
                    </a>
                    <?php 
                    // Only show statuses from database
                    foreach ($stat_arr as $key => $value): ?>
                        <?php if ($key != 1): // Skip the "All" status (ID = 1) ?>
                            <a href="index.php?page=applications&pid=<?php echo $key; ?>&position_id=<?php echo $position_id; ?>" class="btn btn-block <?php echo ($pid == $key) ? 'btn-primary' : 'btn-outline-secondary'; ?>">
                                <i class="fa fa-circle"></i> <?php echo $value; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable with better options
        $('table').dataTable({
            "responsive": true,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "pageLength": 10,
            "searching": true,
            "ordering": true,
            "info": true,
            "paging": true,
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "search": "Search applications:",
                "zeroRecords": "No matching applications found"
            }
        });

        // Event Handlers
        $('#new_application').click(function() {
            uni_modal("New Application", "manage_application.php", "mid-large");
        });

        $('.edit_application').click(function() {
            uni_modal("Edit Application", "manage_application.php?id=" + $(this).attr('data-id'), "mid-large");
        });

        $('.view_application').click(function() {
            uni_modal("Application Details", "view_application.php?id=" + $(this).attr('data-id'), "mid-large");
        });

        $('.delete_application').click(function() {
            _conf("Are you sure you want to delete this applicant?", "delete_application", [$(this).attr('data-id')]);
        });

        // Using direct links instead of click handlers for filters
        $('.filter_status').each(function() {
            var status_id = $(this).attr('data-id');
            $(this).attr('onclick', "window.location.href='index.php?page=applications&pid=" + status_id + "&position_id=<?php echo $position_id; ?>'");
        });

        // Use direct form submission for position filter
        $('#position_filter').change(function() {
            var position_id = $(this).val();
            window.location.href = 'index.php?page=applications&pid=<?php echo $pid; ?>&position_id=' + position_id;
        });
    });

    function delete_application(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_application',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Application successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>
</body>
</html>