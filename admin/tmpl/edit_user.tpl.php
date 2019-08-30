<?php
extract($data);
?>


<div class="row">
    <div class="col-md-12">

		<form class="form-horizontal form-row-seperated" data-qid="<?=$data['id']?>">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="actions btn-set mr-l-0">
                        <button class="btn btn-success" id="save_button">
                            <i class="fa fa-check-circle"></i> Save & Continue Edit
                        </button>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-2 control-label">Имя:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-medium" name="user[name]" value="<?=empty($name)?'':$name?>" placeholder="<?=empty($name)?'':$name?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">E-mail:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-medium" name="user[email]" value="<?=empty($email)?'':$email?>" placeholder="<?=empty($email)?'':$email?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Баллы:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control input-medium" name="user[total_score]" value="<?=empty($total_score)?'0':$total_score?>" placeholder="<?=empty($total_score)?'0':$total_score?>">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<script>
	


jQuery(document).ready(function($) {

    $('#save_button').click(function(event) {
    	event.preventDefault();
    	/* Act on the event */
    	var form_data = $('form').serialize()+'&u_id='+$('form').data('qid');
    	$.ajax({
    		url: '/admin/index.php?action=save_user',
    		type: 'post',
    		dataType: 'json',
    		data: form_data
    	})
    	.done(function(data) {
    		console.log("success");
            $('<div class="alert alert-success"> <strong>'+data.res+'</strong>'+
            '    был обновлен.'+
            '</div>').appendTo('.portlet-title').delay( 1300 ).fadeOut( 400 ).queue(function() { $(this).remove(); });
    	})
    	.fail(function(data) {
    		console.log("error");
    	})
    	.always(function(data) {
    		// console.log("complete");
    	});
    	
    });

});



</script>