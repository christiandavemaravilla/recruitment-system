<?php include 'db_connect.php'; ?>

<?php
	if (isset($_GET['id'])) {
		$application = $conn->query("SELECT a.*, v.position FROM application a INNER JOIN vacancy v ON v.id = a.position_id WHERE a.id = " . $_GET['id'])->fetch_array();
		foreach ($application as $k => $v) {
			$$k = $v;
		}
		$fname = explode('_', $resume_path);
		unset($fname[0]);
		$fname = implode("", $fname);
	}

	$qry = $conn->query("SELECT * FROM vacancy");
	while ($row = $qry->fetch_assoc()) {
		$pos[$row['id']] = $row['position'];
	}

	$rs = $conn->query("SELECT * FROM recruitment_status");
	while ($row = $rs->fetch_assoc()) {
		$stat[$row['id']] = $row['status_label'];
	}
?>

<div class="container-fluid">
	<form id="manage-application">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">

		<div class="col-md-12">
			<div class="row form-group">
				<div class="col-md-6">
					<label class="control-label">Position</label>
					<select class="custom-select browser-default select2" name="position_id">
						<option value=""></option>
						<?php foreach ($pos as $k => $v): ?>
							<option value="<?php echo $k; ?>" <?php echo isset($position_id) && $position_id == $k ? "selected" : ''; ?>>
								<?php echo $v; ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-4">
					<label class="control-label">Last Name</label>
					<input type="text" class="form-control" name="lastname" required value="<?php echo isset($lastname) ? $lastname : ''; ?>">
				</div>
				<div class="col-md-4">
					<label class="control-label">First Name</label>
					<input type="text" class="form-control" name="firstname" required value="<?php echo isset($firstname) ? $firstname : ''; ?>">
				</div>
				<div class="col-md-4">
					<label class="control-label">Middle Name</label>
					<input type="text" class="form-control" name="middlename" required value="<?php echo isset($middlename) ? $middlename : ''; ?>">
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-4">
					<label class="control-label">Gender</label>
					<select name="gender" class="custom-select browser-default">
						<option <?php echo isset($gender) && $gender == 'Male' ? "selected" : ''; ?>>Male</option>
						<option <?php echo isset($gender) && $gender == 'Female' ? "selected" : ''; ?>>Female</option>
					</select>
				</div>
				<div class="col-md-4">
					<label class="control-label">Email</label>
					<input type="email" class="form-control" name="email" required value="<?php echo isset($email) ? $email : ''; ?>">
				</div>
				<div class="col-md-4">
					<label class="control-label">Contact</label>
					<input type="text" class="form-control" name="contact" required value="<?php echo isset($contact) ? $contact : ''; ?>">
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-10">
					<label class="control-label">Address</label>
					<textarea name="address" cols="30" rows="3" required class="form-control"><?php echo isset($address) ? $address : ''; ?></textarea>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-10">
					<label class="control-label">Cover Letter</label>
					<textarea name="cover_letter" cols="30" rows="3" placeholder="(Optional)" class="form-control"><?php echo isset($cover_letter) ? $cover_letter : ''; ?></textarea>
				</div>
			</div>

			<div class="row form-group">
				<div class="col-md-10">
					<label class="control-label">Resume</label>
					<input type="file" class="form-control" name="resume" accept=".doc, .pdf">
				</div>
			</div>

			<?php if (isset($id)): ?>
				<div class="row form-group">
					<div class="col-md-12">
						<label class="control-label">Message</label>
						<textarea class="form-control" name="message" id="message" rows="3" placeholder="Enter your message..."></textarea>
					</div>
				</div>
				<div class="row form-group">
					<div class="col-md-6">
						<label class="control-label">Status</label>
						<select class="custom-select browser-default select2" name="status" id="status">
							<?php foreach ($stat as $k => $v): ?>
								<option value="<?php echo $k; ?>" <?php echo isset($process_id) && $process_id == $k ? "selected" : ''; ?>>
									<?php echo $v; ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-6 d-flex align-items-end">
						<button type="button" class="btn btn-primary w-100" id="sendNotification">Send Notification</button>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</form>
</div>

<script>
$(document).ready(function(){
    $('.select2').select2({
        width:"100%",
        placeholder:'Please select here'
    });

    // ✅ This handles the form submission
    $('#manage-application').submit(function(e){
        e.preventDefault(); // Prevent normal form submission
        start_load(); // Optional: show loading animation (if defined)

        $.ajax({
            url:'ajax.php?action=save_application',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            error: err => {
                console.log(err);
                end_load(); // Optional: hide loading animation
            },
            success: function(resp){
                if(resp == 1){
                    alert_toast('Application successfully submitted or updated.', 'success');
                    setTimeout(function(){
                        location.reload(); // ✅ Reload to stay on the same page
                    }, 1000);
                } else {
                    alert('Something went wrong. Please try again.');
                    end_load(); // Optional: hide loading animation
                }
            }
        });
    });

    // ✅ This handles the Send Notification button
    $('#sendNotification').click(function() {
        let email = $('[name="email"]').val();
        let status = $('#status').val();
        let message = $('#message').val();

        if (email && status && message) {
            $.ajax({
                url: 'ajax.php?action=send_notification',
                type: 'POST',
                data: { email: email, status: status, message: message },
                success: function(response) {
                    console.log('AJAX Response:', response);
                    if (response.trim() === '1') {
                        alert('Notification sent/updated successfully!');
                        setTimeout(() => {
                            location.reload(); // ✅ Reload to stay on the same page
                        }, 1000);
                    } else {
                        alert('Failed to process notification.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('AJAX Error: ' + error);
                }
            });
        } else {
            alert('All fields are required.');
        }
    });
});
</script>
