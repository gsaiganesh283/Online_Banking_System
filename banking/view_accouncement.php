<?php 
require_once('config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `announcements` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
}
}
?>
<style>
#uni_modal .modal-content>.modal-footer{
    display:none;
}
#uni_modal .modal-body{
    padding:0 !important;
}
</style>
<div class="container-fluid p-2">
    <div><?php echo stripslashes(html_entity_decode($announcement)) ?></div>
    <div class="d-flex w-100 justify-content-end">
        <small class="mb-3"><?php echo date("M d,Y h:i A",strtotime($date_created)) ?></small>
    </div>
</div>
<div class="modal-footer p-0">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>

