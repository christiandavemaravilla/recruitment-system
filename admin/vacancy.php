<?php include('db_connect.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vacancy Management</title>
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
        .vacancy-info {
            padding: 8px 0;
        }
        .position-title {
            font-weight: 600;
            color: #333;
            font-size: 1.05rem;
        }
        .position-description {
            color: #666;
            font-style: italic;
            font-size: 0.9rem;
            max-height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .availability {
            font-weight: 500;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .btn-action {
            margin: 0 3px;
        }
        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 400px;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="page-title">Vacancy List</h5>
                    <div class="action-buttons">
                        <button class="btn btn-primary" type="button" id="new_vacancy">
                            <i class="fa fa-plus"></i> New Vacancy
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="5%" class="text-center">#</th>
                                    <th width="40%">Vacancy Information</th>
                                    <th width="20%" class="text-center">Availability</th>
                                    <th width="15%" class="text-center">Status</th>
                                    <th width="20%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $vacancies = $conn->query("SELECT * FROM vacancy ORDER BY id ASC");
                                
                                if ($vacancies->num_rows > 0):
                                    while($row = $vacancies->fetch_assoc()):
                                        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
                                        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                                        $desc = strtr(html_entity_decode($row['description']), $trans);
                                        $desc = str_replace(array("<li>","</li>"), array("",","), $desc);
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="vacancy-info">
                                            <div class="position-title"><?php echo $row['position']; ?></div>
                                            <div class="position-description"><?php echo strip_tags($desc); ?></div>
                                        </div>
                                    </td>
                                    <td class="text-center availability">
                                        <?php echo $row['availability']; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if($row['status'] == 1): ?>
                                            <span class="status-badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="status-badge badge-danger">Closed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-info btn-action view_vacancy" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary btn-action edit_vacancy" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-action delete_vacancy" type="button" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php 
                                    endwhile; 
                                else:
                                ?>
                                <tr>
                                    <td colspan="5" class="text-center">No vacancies found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                "info": "Showing _START_ to _END_ of _TOTAL_ vacancies",
                "search": "Search vacancies:",
                "zeroRecords": "No matching vacancies found"
            }
        });

        // Event Handlers
        $("#new_vacancy").click(function() {
            uni_modal("New Vacancy", "manage_vacancy.php", "mid-large");
        });
        
        $(".edit_vacancy").click(function() {
            uni_modal("Edit Vacancy", "manage_vacancy.php?id=" + $(this).attr('data-id'), "mid-large");
        });
        
        $(".view_vacancy").click(function() {
            uni_modal("Vacancy Details", "view_vacancy.php?id=" + $(this).attr('data-id'), "mid-large");
        });
        
        $('.delete_vacancy').click(function() {
            _conf("Are you sure you want to delete this vacancy?", "delete_vacancy", [$(this).attr('data-id')]);
        });
    });

    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    function delete_vacancy($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_vacancy',
            method: 'POST',
            data: {id: $id},
            success: function(resp) {
                if(resp == 1) {
                    alert_toast("Vacancy successfully deleted", 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                }
                end_load();
            },
            error: function(err) {
                console.log(err);
                alert_toast("An error occurred", 'error');
                end_load();
            },
            complete: function() {
                end_load();
                $('.spinner-border').hide();
            }
        });
    }
</script>
</body>
</html>