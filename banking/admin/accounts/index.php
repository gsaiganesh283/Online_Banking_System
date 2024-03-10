<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Accounts</h3>
		<div class="card-tools">
			<a href="?page=accounts/manage_account" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-bordered table-stripped" id="indi-list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="20%">
					<col width="20%">
					<col width="15%">
					<col width="15%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th class="text-center">#</th>
						<th>Account #</th>
						<th>Name</th>
						<th>Current Balance</th>
						<th>Date Added</th>
						<th>Date Last Update</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT *,concat(lastname,', ',firstname, middlename) as `name` from `accounts` order by concat(lastname,', ',firstname, middlename) desc ");
						while($row = $qry->fetch_assoc()):
					?>
					
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['account_number'] ?></td>
							<td><?php echo $row['name'] ?></td>
							<td class='text-right'><?php echo number_format($row['balance'],2) ?></td>
							<td><?php echo $row['date_created'] ?></td>
							<td><?php echo $row['date_updated'] ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item edit_data" href="./?page=accounts/manage_account&id=<?php echo $row['id'] ?>" data-id="<?php echo $row['id'] ?>"> Edit</a>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	var indiList;
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Account? All Transactions made will be also deleted.","delete_account",[$(this).attr('data-id')])
		})
	})
	function delete_account($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_account",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
	$(function(){
		indiList = $('#indi-list').dataTable({
			columnDefs:[{
				targets:[6],
				orderable:false
			}],
		});
	})
</script>