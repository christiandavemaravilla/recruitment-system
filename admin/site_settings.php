<?php
include 'db_connect.php';
$qry = $conn->query("SELECT * from system_settings limit 1");
if($qry->num_rows > 0){
	foreach($qry->fetch_array() as $k => $val){
		$meta[$k] = $val;
	}
}
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm rounded-lg">
                <div class="card-header bg-white border-0">
                    <h4 class="card-title text-primary mb-0">System Settings</h4>
                </div>
                <div class="card-body">
                    <form action="" id="manage-settings" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label fw-bold">System Name</label>
                                    <input type="text" class="form-control rounded-pill" id="name" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label fw-bold">Email Address</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-envelope"></i></span>
                                                </div>
                                                <input type="email" class="form-control border-left-0" id="email" name="email" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="contact" class="form-label fw-bold">Contact Number</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-light border-right-0"><i class="fa fa-phone"></i></span>
                                                </div>
                                                <input type="text" class="form-control border-left-0" id="contact" name="contact" value="<?php echo isset($meta['contact']) ? $meta['contact'] : '' ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mb-4">
                                    <label for="about" class="form-label fw-bold">About Content</label>
                                    <textarea name="about" class="text-jqte"><?php echo isset($meta['about_content']) ? $meta['about_content'] : '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
                                <i class="fa fa-save mr-2"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Custom Styles */
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .input-group-text {
        background-color: #f8f9fa;
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-primary:hover {
        background-color: #2e59d9;
        border-color: #2653d4;
    }
    
    .text-primary {
        color: #4e73df !important;
    }
    
    .card {
        border: none;
    }
    
    /* Style for jQTE editor */
    .jqte {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        margin-top: 5px;
    }
    
    .jqte_toolbar {
        background-color: #f8f9fa;
        border-bottom: 1px solid #ced4da;
    }
</style>

<script>
    function displayImg(input, _this) {
        // Function removed
    }
    
    $('.text-jqte').jqte();
    
    $('#manage-settings').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_settings',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            error: err => {
                console.log(err);
            },
            success: function(resp){
                if(resp == 1){
                    alert_toast('Data successfully saved.', 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                }
            }
        });
    });
</script>