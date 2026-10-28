<?php ?>

<div class="container-fluid py-4">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h4 class="m-0">User Management</h4>
		<button class="btn btn-primary rounded-pill" id="new_user">
			<i class="fa fa-plus-circle mr-2"></i> Add New User
		</button>
	</div>
	
	<div class="card shadow">
		<div class="card-header bg-white">
			<h5 class="card-title mb-0">User List</h5>
		</div>
		<div class="card-body px-0">
			<div class="table-responsive">
				<table class="table table-hover">
					<thead class="bg-light">
						<tr>
							<th class="text-center" width="5%">#</th>
							<th width="45%">Name</th>
							<th width="35%">Username</th>
							<th class="text-center" width="15%">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php
							include 'db_connect.php';
							$users = $conn->query("SELECT * FROM users order by name asc");
							$i = 1;
							while($row= $users->fetch_assoc()):
						?>
						<tr>
							<td class="text-center">
								<?php echo $i++ ?>
							</td>
							<td>
								<div class="d-flex align-items-center">
									<?php if(!empty($row['profile_image'])): ?>
										<div class="avatar avatar-sm rounded-circle mr-3">
											<img src="assets/img/profile_images/<?php echo $row['profile_image'] ?>" alt="Profile" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
										</div>
									<?php else: ?>
										<div class="avatar avatar-sm bg-primary rounded-circle text-white mr-3">
											<?php echo substr($row['name'], 0, 1); ?>
										</div>
									<?php endif; ?>
									<?php echo $row['name'] ?>
								</div>
							</td>
							<td>
								<span class="text-muted">@<?php echo $row['username'] ?></span>
							</td>
							<td class="text-center">
								<div class="dropdown">
									<button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
										Actions
									</button>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item edit_user" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>
											<i class="fa fa-edit mr-2"></i> Edit
										</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_user text-danger" href="javascript:void(0)" data-id='<?php echo $row['id'] ?>'>
											<i class="fa fa-trash mr-2"></i> Delete
										</a>
									</div>
								</div>
							</td>
						</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<style>
	.avatar {
		width: 30px;
		height: 30px;
		display: flex;
		align-items: center;
		justify-content: center;
		font-weight: bold;
		overflow: hidden;
	}
	.table th, .table td {
		padding: 1rem;
		vertical-align: middle;
	}
	.dropdown-item {
		padding: 0.5rem 1rem;
	}
	.dropdown-item:hover {
		background-color: #f8f9fa;
	}
	.dropdown-item.text-danger:hover {
		background-color: #fff5f5;
	}
</style>

<script>
	$('#new_user').click(function(){
		uni_modal('<i class="fa fa-plus-circle"></i> Add New User', 'manage_user.php');
	});
	
	$('.edit_user').click(function(){
		uni_modal('<i class="fa fa-edit"></i> Edit User Details', 'manage_user.php?id='+$(this).attr('data-id'));
	});
	
	$('.delete_user').click(function(){
		_conf("Are you sure you want to remove this user?", "delete_user", [$(this).attr('data-id')]);
	});
	
	function delete_user($id){
		start_load();
		$.ajax({
			url:'ajax.php?action=delete_user',
			method:'POST',
			data:{id:$id},
			success:function(resp){
				if(resp==1){
					alert_toast("User successfully deleted", 'success');
					setTimeout(function(){
						location.reload();
					}, 1500);
				}
			}
		});
	}
</script>