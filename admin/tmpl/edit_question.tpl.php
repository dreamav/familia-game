<?php
extract($data);

$q_answers = answers_check($id);
$q_finals = final_check($id);
$fin_good = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $q_finals[0]['final']);
$fin_good = unserialize($fin_good);
$fin_bad = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $q_finals[1]['final']);
$fin_bad = unserialize($fin_bad);
$fin_neut = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $q_finals[2]['final']);
$fin_neut = unserialize($fin_neut);

$q_childs = get_question_childs($id);

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
                    <div class="tabbable-bordered">
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#general" data-toggle="tab"> Основные </a>
                            </li>
                            <li>
                                <a href="#tab_text_input" data-toggle="tab" <?=$q_type==1?"style='display:block'":""?>> Текст </a>
                            </li>
                            <li>
                                <a href="#tab_text_choice" data-toggle="tab"<?=$q_type==2?"style='display:block'":""?>> Выбор из текста </a>
                            </li>
                            <li>
                                <a href="#tab_image_choice" data-toggle="tab"<?=$q_type==3?"style='display:block'":""?>> Выбор из картинок </a>
                            </li>
                            <li>
                                <a href="#tab_seria_choice" data-toggle="tab"<?=$q_type==4?"style='display:block'":""?>> Выбор вопросов для серии </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="general">
                                <div class="form-body">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Заголовок вопроса:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="question[name]" value="<?=empty($q_title)?'':$q_title?>" placeholder="<?=empty($q_title)?'':$q_title?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Системное название вопроса:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="question[sys_name]" value="<?=empty($sys_name)?'':$sys_name?>" placeholder="<?=empty($sys_name)?'':$sys_name?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Видимость:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[q_visibility]" value="<?=empty($q_visibility)?'':'on'?>" <?=empty($q_visibility)?'':'checked'?>>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Жёсткость:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[q_hard]" value="<?=empty($q_hard)?'':'on'?>" <?=empty($q_hard)?'':'checked'?>>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Родитель?:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="checkbox" name="question[q_level]" value="<?=($q_level==2)?'':'on'?>" <?=($q_level==2)?'':'checked'?>>
                                        </div>
                                    </div>
                                    <?if($q_type==4):?>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Опрос?:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="checkbox" name="question[opros]" value="<?=($opros==0)?'':'on'?>" <?=($opros==0)?'':'checked'?>>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Текст на кнопке:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control input-medium" name="question[seria_button]" value="<?=empty($seria_button)?'':$seria_button?>" placeholder="<?=empty($seria_button)?'':$seria_button?>">
                                        </div>
                                    </div>
                                    <?endif;?>
                                    <?if($q_type==1): // вывод Вопрос с ручной проверкой? ?>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Вопрос с ручной проверкой?:
                                        </label>
                                        <div class="col-md-10">
	                                        <input type="checkbox" name="question[manual]" value="<?=($manual==0)?'':'on'?>" <?=($manual==0)?'':'checked'?>>
                                        </div>
                                    </div>
                                    <?endif;?>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Пол:
                                        </label>
                                        <div class="col-md-10">
                                            <?=gen_gender_select($gender);?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Номер по порядку:
                                        </label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" name="question[q_nomer]" value="<?=empty($q_nomer)?'':$q_nomer?>" placeholder="<?=empty($q_nomer)?'':$q_nomer?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Категория:
                                        </label>
                                        <div class="col-md-10">
                                        	<?=gen_select_id("categories","question[q_category]",$q_category)?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Тип:
                                        </label>
                                        <div class="col-md-10">
                                        	<?=gen_select_id("types","question[q_type]",$q_type)?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Текст для шаринга:
                                        </label>
                                        <div class="col-md-6">
                                            <textarea class="form-control" name="question[q_share]"><?=empty($q_share)?"":$q_share?></textarea>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                            <?if($q_type==1||$q_type==null||$q_type==0){?>
                            <div class="tab-pane" id="tab_text_input">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-6">
                                        <textarea id="text_input_question_text" class="form-control richtext" name="question[q_text]"><?=empty($q_text)?"":$q_text?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
	                                	<h3 class="form-section">Варианты ответов:</h3>
                                	</div>
                                </div>
                                <div id="all_answers">
                                    <?if($q_type==1){
                                        foreach ($q_answers as $key => $q_answer) {?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Ответ:
                                                </label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" name="question[answer][]" value="<?=$q_answer['answer']?>">
                                                </div>
                                            </div>
                                    <?}
                                    }?>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
                                	<button type="submit" class="btn green add_field" data-pane_id="tab_text_input">
                                		<i class="fa fa-plus"></i>
                                		Добавить
                                	</button>
                                	</div>
                                </div>
                                                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Финалы:</h3>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Хороший финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][good][title]" value="<?=empty($fin_good)?"":$fin_good['title']?>">
                                        <textarea class="form-control richtext" name="question[final][good][text]"><?=empty($fin_good)?"":$fin_good['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Плохой финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][bad][title]" value="<?=empty($fin_bad)?"":$fin_bad['title']?>">
                                        <textarea class="form-control richtext" name="question[final][bad][text]"><?=empty($fin_bad)?"":$fin_bad['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Нейтральный финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][neut][title]" value="<?=empty($fin_neut)?"":$fin_neut['title']?>">
                                        <textarea class="form-control richtext" name="question[final][neut][text]"><?=empty($fin_neut)?"":$fin_neut['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Баллы:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[q_score]" value="<?=empty($q_score)?"5":$q_score?>">
                                    </div>
                                </div>
                            </div>
                            <?}?>
                            <?if($q_type==2||$q_type==null||$q_type==0){?>
                            <div class="tab-pane" id="tab_text_choice">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control richtext" name="question[q_text]"><?=empty($q_text)?"":$q_text?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Правильные ответы:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[correct]" value="<?=empty($q_correct)?"":$q_correct?>">
                                    </div>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
	                                	<h3 class="form-section">Варианты ответов:</h3>
	                                	<p>2 или 4</p>
                                	</div>
                                </div>
                                <div id="all_answers">
                                    <?if($q_type==2){
                                        foreach ($q_answers as $key => $q_answer) {?>
                                            <div class="form-group">
                                                <label class="col-md-2 control-label">Ответ:
                                                </label>
                                                <div class="col-md-10">
                                                    <input type="text" class="form-control" name="question[answer][]" value="<?=$q_answer['answer']?>">
                                                </div>
                                            </div>
                                    <?}
                                    }?>
                                </div>
                                <div class="row">
                                	<div class="col-md-10 col-md-offset-2">
                                	<button type="submit" class="btn green add_field" data-pane_id="tab_text_choice">
                                		<i class="fa fa-plus"></i>
                                		Добавить
                                	</button>
                                	</div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Финалы:</h3>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Хороший финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][good][title]" value="<?=empty($fin_good)?"":$fin_good['title']?>">
                                        <textarea class="form-control richtext" name="question[final][good][text]"><?=empty($fin_good)?"":$fin_good['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Плохой финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][bad][title]" value="<?=empty($fin_bad)?"":$fin_bad['title']?>">
                                        <textarea class="form-control richtext" name="question[final][bad][text]"><?=empty($fin_bad)?"":$fin_bad['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Нейтральный финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][neut][title]" value="<?=empty($fin_neut)?"":$fin_neut['title']?>">
                                        <textarea class="form-control richtext" name="question[final][neut][text]"><?=empty($fin_neut)?"":$fin_neut['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Баллы:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[q_score]" value="<?=empty($q_score)?"5":$q_score?>">
                                    </div>
                                </div>                                
                            </div>
                            <?}?>
                            <?if($q_type==3||$q_type==null||$q_type==0){?>
                            <div class="tab-pane" id="tab_image_choice">
                                 <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control richtext" name="question[q_text]"><?=empty($q_text)?"":$q_text?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Правильные ответы:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[correct]" value="<?=empty($q_correct)?"":$q_correct?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Варианты ответов:</h3>
                                        <p>2 или 4</p>
                                    </div>
                                </div>
                                <div id="all_answers">
                                    <?if($q_type==3){
                                        foreach ($q_answers as $key => $q_answer) {?>
                                            <div class="form-group" id="div_answer_<?=$q_answer['id']?>">
                                                <div class="col-md-1">
                                                    <img src="/images/<?=$q_answer['answer']?>" alt="" style="max-width: 100%">
                                                </div>
                                                <label class="col-md-1 control-label">Ответ:
                                                </label>
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" name="question[answer][]" value="<?=$q_answer['answer']?>">
                                                </div>
                                                <div class="col-md-1">
                                                    <a href="#" id="remove_answer_<?=$q_answer['id']?>" class="btn red" data-answerid="<?=$q_answer['id']?>">
                                                        <i class="fa fa-minus"></i>
                                                        Удалить
                                                    </a>
                                                </div>
                                            </div>
                                    <?}
                                    }?>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                    <button type="submit" class="btn green add_image_field" data-pane_id="tab_image_choice">
                                        <i class="fa fa-plus"></i>
                                        Добавить
                                    </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Финалы:</h3>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Хороший финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][good][title]" value="<?=empty($fin_good)?"":$fin_good['title']?>">
                                        <textarea class="form-control richtext" name="question[final][good][text]"><?=empty($fin_good)?"":$fin_good['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Плохой финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][bad][title]" value="<?=empty($fin_bad)?"":$fin_bad['title']?>">
                                        <textarea class="form-control richtext" name="question[final][bad][text]"><?=empty($fin_bad)?"":$fin_bad['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Нейтральный финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][neut][title]" value="<?=empty($fin_neut)?"":$fin_neut['title']?>">
                                        <textarea class="form-control richtext" name="question[final][neut][text]"><?=empty($fin_neut)?"":$fin_neut['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Баллы:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[q_score]" value="<?=empty($q_score)?"5":$q_score?>">
                                    </div>
                                </div>
                            </div>
                            <?}?>
                            <?if($q_type==4||$q_type==null||$q_type==0){?>
                            <div class="tab-pane" id="tab_seria_choice">
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Текст вопроса:
                                    </label>
                                    <div class="col-md-10">
                                        <textarea class="form-control richtext" name="question[q_text]"><?=empty($q_text)?"":$q_text?></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Дочерние вопросы:</h3>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <?
                                        if (empty($q_childs)) {
                                            echo gen_questions_select($id);
                                        } else {
                                            if (is_array($q_childs)) {
                                                echo "<ul>";
                                                foreach ($q_childs as $key => $q_child) {
                                                    echo "<li>" . $q_child['sys_name'] . ' - ' . $q_child['q_title'] . "</li>";
                                                }
                                                echo "</ul>";
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <h3 class="form-section">Финалы:</h3>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Хороший финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][good][title]" value="<?=empty($fin_good)?"":$fin_good['title']?>">
                                        <textarea class="form-control richtext" name="question[final][good][text]"><?=empty($fin_good)?"":$fin_good['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Баллы хорошего результата:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[f_score][good]" value="<?=empty($q_finals[0]['final_points'])?"":$q_finals[0]['final_points']?>">
                                        <span class="help-block"> хорошим результатом считается от этого числа и больше баллов </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Плохой финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][bad][title]" value="<?=empty($fin_bad)?"":$fin_bad['title']?>">
                                        <textarea class="form-control richtext" name="question[final][bad][text]"><?=empty($fin_bad)?"":$fin_bad['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Баллы плохого результата:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[f_score][bad]" value="<?=empty($q_finals[1]['final_points'])?"":$q_finals[1]['final_points']?>">
                                        <span class="help-block"> плохим результатом считается к-во баллов ниже этого числа (включая это число) </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Нейтральный финал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[final][neut][title]" value="<?=empty($fin_neut)?"":$fin_neut['title']?>">
                                        <textarea class="form-control richtext" name="question[final][neut][text]"><?=empty($fin_neut)?"":$fin_neut['text']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label">Максимальный бал:
                                    </label>
                                    <div class="col-md-10">
                                        <input type="text" class="form-control" name="question[q_score]" value="<?=empty($q_score)?"10":$q_score?>">
                                    </div>
                                </div>
                            </div>
                            <?}?>
                        </div>
                    </div>
                </div>
            </div>
        </form>

    </div>
</div>
<script>
	


jQuery(document).ready(function($) {
    /* change tab on select type */
	$('select[name*=q_type]').on('change', function(event) {
		event.preventDefault();
		/* Act on the event */
		var th = $(this),
			typeVal = parseInt(th.val());

		switch (typeVal) {
			  case 1:
			    $('a[href*=tab_text_input]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
                $('#tab_text_input').siblings('div[id^=tab_]').remove();
			    break;
			  case 2:
			    $('a[href*=tab_text_choice]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
                $('#tab_text_choice').siblings('div[id^=tab_]').remove();
			    break;
              case 3:
                $('a[href*=tab_image_choice]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
                $('#tab_image_choice').siblings('div[id^=tab_]').remove();
                break;
			  case 4:
			    $('a[href*=tab_seria_choice]').css('display', 'block').parent('li').siblings().find('a').removeAttr('style');
                $('#tab_seria_choice').siblings('div[id^=tab_]').remove();
			    break;
			  default:
				break;
			}
	});



/* add answer */
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
}
/* add answer images type */
function add_answer_image(pane_id,img_counter){
	var el = $(
		'<div class="row">'+
		'	<div class="col-md-2"><div id="img'+img_counter+'" class="col-md-2"></div></div>'+
		'	<div class="col-md-4"><input type="text" class="form-control" name="question[image]['+img_counter+'][name]"></div>'+
		'	<div class="col-md-4"><input type="text" class="form-control" name="question[image]['+img_counter+'][order]"></div>'+
		'</div>'
        ).appendTo($('#'+pane_id+' #all_answers'));
	var myDropzone = new Dropzone("div#img"+img_counter, { url: "/admin/api/img_upload.php"});
    myDropzone.on("sending", function(file,xhr,form) {
        form.append("img_nomer", img_counter);
        form.append("q_nomer", <?=$id?>);
    }); 
	myDropzone.on("success", function(file,responce) {
	/* Maybe display some more file information on your page */
		$('input[name*="question[image]['+img_counter+'][name]"]').val(responce);
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
        tinyMCE.triggerSave();

        // validate
        var q_correct_input = $("input[name*='question[correct]']");
        if ( q_correct_input.val() == "" ) {

            q_correct_input.closest('.form-group').addClass('has-error');
            $('html, body').stop().animate({
                scrollTop: (q_correct_input.closest('.form-group').offset().top-60)
            }, 1500);
            return;
        }

        /* Act on the event */
    	var form_data = $('form').serialize()+'&q_id='+$('form').data('qid');
    	$.ajax({
    		url: '/admin/index.php?action=save_question',
    		type: 'post',
    		dataType: 'json',
    		data: form_data
    	})
    	.done(function(data) {
    		console.log("success");
            q_correct_input.closest('.form-group').removeClass('has-error');
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

$('a[id^=remove_answer_]').on('click', function(event) {
    event.preventDefault();

    var a_id = $(this).data('answerid');

    $.ajax({
        url: '/admin/index.php?action=remove_answer&a_id='+a_id,
        type: 'post',
        dataType: 'json'
    })
    .done(function(data) {
        console.log("success");
        $('<div class="alert alert-success"> <strong>Вопрос #'+data.res+'!</strong>'+
        '    был обновлен.'+
        '</div>').appendTo('.portlet-title').delay( 1300 ).fadeOut( 400 ).queue(function() { $(this).remove(); });
    })
    .fail(function(data) {
        console.log("error");
    });    

});



// summernote code

tinyMCE.init({
    language : "ru",
    mode : "specific_textareas",
    editor_selector : "richtext",
    theme : "advanced",
    skin : "o2k7",
    skin_variant : "silver",
    plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,-externalplugin,imagemanager,filemanager",

    // Theme options
    theme_advanced_buttons1 : "pastetext,pasteword,|,undo,redo,|,cleanup,removeformat,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,forecolor,backcolor,|,sub,sup,search,replace,|,outdent,indent,blockquote,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
    theme_advanced_buttons2 : ",link,unlink,anchor,image,code,|,insertdate,inserttime,preview,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,templatetablecontrols,|,visualaid,hr,|styleselect,formatselect,fontselect,fontsizeselect,|",
    theme_advanced_buttons3:"",
    relative_urls : true,
    remove_script_host : true,
    plugin_insertdate_dateFormat : '%d.%m.%Y',
    plugin_insertdate_timeFormat : '%H:%M:%S',
    document_base_url : "/plugins/",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    template_external_list_url : "lists/template_list.js",
    external_link_list_url : "lists/link_list.js",
    external_image_list_url : "lists/image_list.js",
    media_external_list_url : "lists/media_list.js",
    extended_valid_elements : 'noindex,script[type|src],iframe[src|style|width|height|scrolling|marginwidth|marginheight|frameborder],div[*],p[*],object[width|height|classid|codebase|embed|param],param[name|value],embed[param|src|type|width|height|flashvars|wmode]',
    media_strict: false,
    width : "800",
    height : "450"
 });


});


</script>