<?php 
include('db_connect.php');
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM users where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<div class="container-fluid">
	
	<form action="" id="manage-user" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		<div class="form-group">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($meta['name']) ? $meta['name']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="username">Username</label>
			<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="password">Password</label>
			<input type="password" name="password" id="password" class="form-control" value="<?php echo isset($meta['password']) ? $meta['password']: '' ?>" required>
		</div>
		<div class="form-group">
			<label for="type">User Type</label>
			<select name="type" id="type" class="custom-select">
				<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected': '' ?>>Admin</option>
				<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected': '' ?>>Staff</option>
			</select>
		</div>
		<div class="form-group">
			<label for="profile_image">Profile Picture</label>
			<div class="custom-file">
				<input type="file" class="custom-file-input" id="profile_image" name="profile_image" accept="image/*">
				<label class="custom-file-label" for="profile_image">Choose file</label>
			</div>
			<?php if(isset($meta['profile_image']) && !empty($meta['profile_image'])): ?>
			<div class="mt-2">
				<div class="img-thumbnail" style="width: 100px; height: 100px; overflow: hidden;">
					<img src="assets/img/profile_images/<?php echo $meta['profile_image']; ?>" alt="Profile Image" style="width: 100%; height: auto;">
				</div>
			</div>
			<?php endif; ?>
		</div>
	</form>
</div>
<script>
	// Add this to make the file input show the selected filename
	$('.custom-file-input').on('change', function() {
		var fileName = $(this).val().split('\\').pop();
		$(this).next('.custom-file-label').html(fileName);
		
		// Preview image
		if(this.files && this.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				if($('.img-thumbnail').length > 0) {
					$('.img-thumbnail img').attr('src', e.target.result);
				} else {
					$('#profile_image').after('<div class="mt-2"><div class="img-thumbnail" style="width: 100px; height: 100px; overflow: hidden;"><img src="' + e.target.result + '" alt="Profile Preview" style="width: 100%; height: auto;"></div></div>');
				}
			}
			reader.readAsDataURL(this.files[0]);
		}
	});
	
	$('#manage-user').submit(function(e){
		e.preventDefault();
		start_load();
		
		// Create FormData object to handle file uploads
		var formData = new FormData(this);
		
		$.ajax({
			url:'ajax.php?action=save_user',
			method:'POST',
			data: formData,
			processData: false,
			contentType: false,
			success:function(resp){
				if(resp == 1){
					alert_toast("Data successfully saved", 'success');
					setTimeout(function(){
						location.reload();
					}, 1500);
				}
			}
		});
	});
</script>

<style>
	.custom-file-label::after {
		content: "Browse";
	}
	
	.img-preview {
		max-width: 100px;
		max-height: 100px;
		margin-top: 10px;
	}
</style>