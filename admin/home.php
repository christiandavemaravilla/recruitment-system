<?php include 'db_connect.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .dashboard-header {
            padding: 1.5rem 0;
            border-bottom: 1px solid #e3e6f0;
            margin-bottom: 1.5rem;
        }
        .dashboard-title {
            color: #5a5c69;
            font-weight: 700;
            font-size: 1.75rem;
            line-height: 1.2;
        }
        .card {
            border: none;
            border-radius: 0.35rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2rem;
            opacity: 0.4;
        }
        .stat-card-primary {
            background: linear-gradient(135deg, #4e73df 0%, #2246bd 100%);
        }
        .stat-card-warning {
            background: linear-gradient(135deg, #f6c23e 0%, #dda20a 100%);
        }
        .stat-card-success {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        }
        .stat-card-info {
            background: linear-gradient(135deg, #36b9cc 0%, #258391 100%);
        }
        .stat-count {
            font-size: 2rem;
            font-weight: 700;
        }
        .stat-label {
            text-transform: uppercase;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.05rem;
        }
        .quick-actions {
            margin-top: 2rem;
        }
        .quick-action-btn {
            padding: 1rem;
            border-radius: 0.35rem;
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <!-- Dashboard Header -->
    <div class="row dashboard-header">
        <div class="col-12">
            <h1 class="dashboard-title">
                <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
            </h1>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-count">
                                <?php 
                                    $applicant = $conn->query("SELECT * FROM application where process_id = 0 ");
                                    echo $applicant->num_rows;
                                ?>
                            </div>
                            <div class="stat-label">New Applicants</div>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small><i class="fas fa-info-circle mr-1"></i>Pending review</small>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-count">
                                <?php 
                                    $vacancies = $conn->query("SELECT * FROM vacancy where status = 1");
                                    echo $vacancies->num_rows;
                                ?>
                            </div>
                            <div class="stat-label">Active Vacancies</div>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small><i class="fas fa-clock mr-1"></i>Currently open</small>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stat-count">
                                <?php 
                                    $hired = $conn->query("SELECT * FROM application where process_id = 2");
                                    echo $hired->num_rows;
                                ?>
                            </div>
                            <div class="stat-label">Hired Applicants</div>
                        </div>
                        <div class="card-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-white-50">
                    <small><i class="fas fa-calendar-check mr-1"></i>Total hired</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row quick-actions">
        <div class="col-12">
            <h4 class="text-muted mb-3"><i class="fas fa-bolt mr-2"></i>Quick Actions</h4>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="?page=applications" class="text-decoration-none">
                <div class="card quick-action-btn bg-white border-left-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-primary">
                                <i class="fas fa-file-alt fa-2x"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold">New Application</div>
                                <div class="text-muted small">Create a new applicant entry</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="?page=vacancy" class="text-decoration-none">
                <div class="card quick-action-btn bg-white border-left-warning h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-warning">
                                <i class="fas fa-briefcase fa-2x"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold">Manage Vacancies</div>
                                <div class="text-muted small">View or create job positions</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <a href="?page=applications" class="text-decoration-none">
                <div class="card quick-action-btn bg-white border-left-success h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="mr-3 text-success">
                                <i class="fas fa-list-alt fa-2x"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold">Applications List</div>
                                <div class="text-muted small">Review all applications</div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        
    
</div>

<!-- Bootstrap core JavaScript-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>