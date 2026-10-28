<?php
session_start();
?>

<section id="" class="mt-5">
    <?php include 'admin/db_connect.php'; ?>
</section>

<?php
$qry = $conn->query("SELECT * FROM vacancy WHERE id=" . $_GET['id'])->fetch_array();
foreach ($qry as $k => $v) {
    $$k = $v;
}
?>
<div class="container mb-4 pt-4" style="box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.2); border-radius: 10px;">
    <div class="card border-0">
        <div class="card-body p-5">
            <div class="text-center mb-4">
                <h2 class="font-weight-bold"><?php echo $position; ?></h2>
                <p class="text-muted">
                    <i>Needed: <strong><?php echo $availability; ?></strong></i>
                </p>
                <?php if ($status == 1): ?>
                    <span class="badge bg-success p-2">Hiring</span>
                <?php else: ?>
                    <span class="badge bg-danger p-2">Closed</span>
                <?php endif; ?>
            </div>
            <hr>
            <div class="mb-4">
                <?php echo html_entity_decode($description); ?>
            </div>
            <hr>
            <div class="text-center">
                <?php if ($status == 1): ?>
                    <a href="submit_application.php?id=<?php echo $_GET['id']; ?>" class="btn btn-primary btn-lg">Apply Now</a>
                <?php else: ?>
                    <button class="btn btn-secondary btn-lg" disabled>Application Closed</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
