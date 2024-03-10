<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `announcements` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
}
}
?>
<div class="card card-outline card-primary">
    <div class="card-header">
    <h3 class="card-title"><?php echo isset($_GET['id']) ? 'Update Announcement' : "Create New Announcement"; ?></h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form id="announcements-form">
                <input type="hidden" name="id" value='<?php echo isset($id)? $id : '' ?>'>
                <div class="form-group">
                    <label class="control-label">Title</label>
                    <input type="text" class="form-control col-sm-8" name="title" value="<?php echo isset($title)? $title : '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="control-label">Announcement</label>
                    <textarea type="text" class="form-control summernote" name="announcement" required><?php echo isset($announcement)? stripslashes(html_entity_decode($announcement)) : '' ?></textarea>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex w-100">
            <button form="announcements-form" class="btn btn-primary mr-2">Save</button>
            <a href="./?page=announcements" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('.summernote').summernote({
		        height: 200,
		        toolbar: [
		            [ 'style', [ 'style' ] ],
		            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
		            [ 'fontname', [ 'fontname' ] ],
		            [ 'fontsize', [ 'fontsize' ] ],
		            [ 'color', [ 'color' ] ],
		            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
		            [ 'table', [ 'table' ] ],
		            [ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
		        ]
		    })
        
        $('#announcements-form').submit(function(e){
            e.preventDefault()
            start_loader()
            if($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url:_base_url_+'classes/Master.php?f=save_announcement',
                method:'POST',
                data:$(this).serialize(),
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured","error")
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        location.href="./?page=announcements"
                    }else if(!!resp.msg){
                         var msg = $('<div class="err_msg"><div class="alert alert-danger">'+resp.msg+'</div></div>')
                         $('#announcements-form').prepend(msg) 
                         msg.show('slow')
                    }else{
                        alert_toast('An error occured',"error")
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
    })
</script>