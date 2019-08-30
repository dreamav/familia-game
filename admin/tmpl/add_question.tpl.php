<div class="row">
    <div class="col-md-12">

		<form class="form-horizontal form-row-seperated" data-qid="<?=$q_id?>">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="actions btn-set mr-l-0">
                        <button class="btn btn-success" id="save_button">
                            <i class="fa fa-check-circle"></i> Save & Continue Edit
                        </button>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tabbable-bordered">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab_general" data-toggle="tab"> Основные </a>
                            </li>
                            <li>
                                <a href="#tab_text_input" data-toggle="tab"> Текст </a>
                            </li>
                            <li>
                                <a href="#tab_text_choice" data-toggle="tab"> Выбор из текста </a>
                            </li>
                            <li>
                                <a href="#tab_image_choice" data-toggle="tab"> Выбор из картинок </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab_general">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Заголовок вопроса:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="question[name]" placeholder="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Видимость:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[q_visibility][]" value="1">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Жёсткость:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[q_hard][]" value="1">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Родитель?:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[q_level][]" value="1">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Номер по порядку:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="question[q_nomer]" placeholder="1">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Категория:
                                        </label>
                                        <div class="col-md-10">
                                        	<?=gen_select("categories","question[q_category]")?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Тип:
                                        </label>
                                        <div class="col-md-10">
                                        	<?=gen_select("types","question[q_type]")?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_text_input">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control" name="question[q_text]"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
	                                	<h3 class="form-section">Варианты ответов:</h3>
                                	</div>
                                </div>
                                <div id="all_answers"></div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
                                	<button type="submit" class="btn green add_field" data-pane_id="tab_text_input">
                                		<i class="fa fa-plus"></i>
                                		Добавить
                                	</button>
                                	</div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_text_choice">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control" name="question[q_text]"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
	                                	<h3 class="form-section">Варианты ответов:</h3>
	                                	<p>2 или 4</p>
                                	</div>
                                </div>
                                <div id="all_answers"></div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
                                	<button type="submit" class="btn green add_field" data-pane_id="tab_text_choice">
                                		<i class="fa fa-plus"></i>
                                		Добавить
                                	</button>
                                	</div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab_image_choice">
                                 <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control" name="question[q_text]"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
	                                	<h3 class="form-section">Варианты ответов:</h3>
	                                	<p>2 или 4</p>
                                	</div>
                                </div>
                                <div id="all_answers">
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
                                	<button type="submit" class="btn green add_image_field" data-pane_id="tab_image_choice">
                                		<i class="fa fa-plus"></i>
                                		Добавить
                                	</button>
                                	</div>
                                </div>                           	
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
	$('select[name*=q_type]').on('change', function(event) {
		event.preventDefault();
		/* Act on the event */
		var th = $(this),
			typeVal = parseInt(th.val());

		switch (typeVal) {
			  case 1:
			    $('a[href*=tab_text_input]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
			    break;
			  case 2:
			    $('a[href*=tab_text_choice]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
			    break;
			  case 3:
			    $('a[href*=tab_image_choice]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
			    break;
			  default:
				break;
			}
	});




function add_answer(pane_id){
	var el = $(
        '<div class="form-group">'+
        '    <label class="col-md-2 control-label">Ответ:'+
        '    </label>'+
        '    <div class="col-md-10">'+
        '        <input type="text" class="form-control" name="question[answer][]">'+
        '    </div>'+
        '</div>'
        ).appendTo($('#'+pane_id+' #all_answers'));



	// order();
	// el.click(trClick);
	// el.dblclick(trDblClick);
	// $(el).find('.q_moveup').click(moveup);
	// $(el).find('.q_movedown').click(movedown);
	// el.find('button.q_delete').click(q_delete);
	// el.find('button.q_edit').click(q_edit);
	// checkAlerts();
	// toBeSaved(el);		
}
function add_answer_image(pane_id,img_counter){
	var el = $(
		'<div class="row">'+
		'	<div class="col-md-2"><div id="img'+img_counter+'" class="col-md-2"></div></div>'+
		'	<div class="col-md-4"><input type="text" class="form-control" name="question[image]['+img_counter+'][name]"></div>'+
		'	<div class="col-md-4"><input type="text" class="form-control" name="question[image]['+img_counter+'][order]"></div>'+
		'</div>'
        ).appendTo($('#'+pane_id+' #all_answers'));
	var myDropzone = new Dropzone("div#img"+img_counter, { url: "/admin/api/img_upload.php"});
	myDropzone.on("addedfile", function(file) {
	/* Maybe display some more file information on your page */
		$('input[name*="question[image]['+img_counter+'][name]"]').val(file.name);
		$('input[name*="question[image]['+img_counter+'][order]"]').val(img_counter);
	});	
}

$('.add_field').on('click', function(event) {
	event.preventDefault();
	/* Act on the event */
	add_answer($(this).data('pane_id'));
});

var img_counter = 1;
$('.add_image_field').on('click', function(event) {
	event.preventDefault();
	/* Act on the event */
	add_answer_image($(this).data('pane_id'),img_counter);
	img_counter++;
});

$('#save_button').click(function(event) {
	event.preventDefault();
	/* Act on the event */
	var form_data = $('form').serialize()+'&q_id='+$('form').data('qid');
	$.ajax({
		url: '/admin/index.php?action=save_question',
		type: 'post',
		dataType: 'json',
		data: form_data
	})
	.done(function() {
		console.log("success");
	})
	.fail(function() {
		console.log("error");
	})
	.always(function() {
		console.log("complete");
	});
	
});





});



</script>