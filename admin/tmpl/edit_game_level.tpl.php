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
                            <label class="col-md-2 control-label">№ уровня:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="game_level[level]" value="<?=empty($level)?'':$level?>" placeholder="<?=empty($level)?'':$level?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Очки/баллы для достжения уровня:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="game_level[points]" value="<?=empty($points)?'':$points?>" placeholder="<?=empty($points)?'':$points?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Название уровня:
                            </label>
                            <div class="col-md-10">
                                <input type="text" class="form-control" name="game_level[name]" value="<?=empty($name)?'':$name?>" placeholder="<?=empty($name)?'':$name?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Поздравления за получение уровня:
                            </label>
                            <div class="col-md-10">
                                <textarea class="form-control" name="game_level[greetings]"><?=empty($greetings)?"":$greetings?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-2 control-label">Текст:
                            </label>
                            <div class="col-md-10">
                                <textarea class="form-control" name="game_level[l_text]"><?=empty($l_text)?"":$l_text?></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <? if ( !empty($image) ) { ?>
                                <div class="col-md-1">
                                    <img src="/images/<?=$image?>" alt="" style="max-width: 100%">
                                </div>
                                <div class="col-md-4"><input type="text" class="form-control" name="game_level[image]" value="<?=$image?>"></div>
                            <?} else { ?>
                               <div class="col-md-2"><div id="image" class="col-md-2"></div></div>
                               <div class="col-md-4"><input type="text" class="form-control" name="game_level[image]"></div>
                           <? } ?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<script>
	


jQuery(document).ready(function($) {

    if ( $("#image").length > 0 ) {
        var myDropzone = new Dropzone("div#image", { url: "/admin/api/img_upload.php"});
        myDropzone.on("sending", function(file,xhr,form) {
            form.append("level_id", <?=$id?>);
        }); 
        myDropzone.on("success", function(file,responce) {
        /* Maybe display some more file information on your page */
            $('input[name*="game_level[image]"').val(responce);
        }); 
    }

    $('#save_button').click(function(event) {
    	event.preventDefault();
    	/* Act on the event */
    	var form_data = $('form').serialize()+'&q_id='+$('form').data('qid');
    	$.ajax({
    		url: '/admin/index.php?action=save_game_level',
    		type: 'post',
    		dataType: 'json',
    		data: form_data
    	})
    	.done(function(data) {
    		console.log("success");
            $('<div class="alert alert-success"> <strong>Вопрос #'+data.res+'!</strong>'+
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