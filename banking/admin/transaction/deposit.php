<?php 
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `accounts` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
}
}
?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
    <div class="card-header">
    <h3 class="card-title">Deposit</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <form id="account-form">
                <input type="hidden" name="id" value='<?php echo isset($id)? $id : '' ?>'>
                <div class="form-group">
                    <label class="control-label">Account Number</label>
                    <input type="text" class="form-control col-sm-6" name="account_number" value="<?php echo isset($account_number)? $account_number : '' ?>" required autocomplete="off">
                </div>
                <hr>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <input type="hidden" name="account_id" value="">
                        <label class="control-label">Name</label>
                        <input type="text" class="form-control" id="name"  name="name" readonly>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="control-label">Balance</label>
                        <input type="text" class="form-control" id="balance" name="current" readonly>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <label class="control-label">Deposit Amount</label>
                    <input type="number" step='any' min = "0" class="form-control col-sm-6 text-right" name="balance" value="0" required>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-flex w-100">
            <button form="account-form" class="btn btn-primary mr-2">Submit</button>
            <a href="./?page=transaction" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
<script>
    $(function(){
        $('#generate_pass').click(function(){
            var randomstring = Math.random().toString(36).slice(-8);
            $('[name="generated_password"]').val(randomstring)
        })
        $('[name="account_number"]').on('input',function(){
            if($('._checks').length > 0)
                $('._checks').remove()
            $('[name="account_id"]').val('')
            $('#name').val('')
            $('#balance').val('')
            $(this).removeClass('border-danger')
            $(this).removeClass('border-success')
            if($(this).val() == '')
            return false;
            $('button[form="account-form"]').attr('disabled',true)
            var checks = $('<small class="_checks">')
            checks.text("Checking availablity") 
            $('[name="account_number"]').after(checks)
            $.ajax({
                url:_base_url_+'classes/Master.php?f=get_account',
                method:'POST',
                data:{account_number: $(this).val()},
                dataType:'json',
                error:err=>{
                    console.log(err)
                    alert_toast("An error occured","error")
                    end_loader()
                },
                success:function(resp){
                    if(resp.status == 'success'){
                        checks.hide('slow').remove()
                        $('[name="account_number"]').addClass('border-success')
                        $('button[form="account-form"]').attr('disabled',false)
                        $('[name="account_id"]').val(resp.data.id)
                        $('#name').val(resp.data.name)
                        $('#balance').val(resp.data.balance)
                    }else if(resp.status == 'not_exist'){
                        checks.addClass('text-danger')
                        checks.text('Account doesn\'t exist')
                        $('[name="account_number"]').addClass('border-danger')
                        $('button[form="account-form"]').attr('disabled',true)
                    }else{
                        alert_toast('An error occured',"error")
                        $('[name="account_number"]').addClass('border-danger')
                        console.log(resp)
                    }
                    end_loader()
                }
            })
        })
        $('#account-form').submit(function(e){
            e.preventDefault()
            start_loader()
            if($('.err_msg').length > 0)
                $('.err_msg').remove()
            $.ajax({
                url:_base_url_+'classes/Master.php?f=deposit',
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
                        location.reload();
                    }else if(!!resp.msg){
                         var msg = $('<div class="err_msg"><div class="alert alert-danger">'+resp.msg+'</div></div>')
                         $('#account-form').prepend(msg) 
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