<?
$answers = get_answers($q_data['id']);
// это условия надо только для дизайна,
// в зависимости от к-ва вариантов выбора
// высота текста разная
if ( count($answers) == 2 ) {
	$answersCountCSSClass = "answersCount2";
}else{
	$answersCountCSSClass = "answersCount4";
}

$is_child_question = is_child_question($q_data['id']);
if ( $is_child_question ) {
	if ( $is_child_question != $_GET['pid'] ) {
		header("Location:".$_SERVER['PHP_SELF']);
	}
}

$skip_link = 'index.php?action=skip_answer&qid='.$q_data['id'];
if ( isset($_GET['pid']) ) {
    $active_sessia = seria_session($_SESSION['user']->id,$_GET['pid']);
    $active_sessia = $active_sessia[0];
    $active_sessia['questions'] = explode("|", $active_sessia['questions']);
    $skip_link = 'index.php?action=skip_answer&qid='.$_GET['pid'];
}
$db_skipped = get_skipped_session($_SESSION['user']->id);
if ( isset($_SESSION['user']->skipped) ) {
	if ($_SESSION['user']->skipped < 3) {
		$points_to_skip = 5;
	}else{
		$points_to_skip = 10;
	}
}else{
	$points_to_skip = 5;
	if ( $db_skipped != null ) {
		if ( $db_skipped < 3 ) {
			$points_to_skip = 5;
		}else{
			$points_to_skip = 10;
		}
	}	
}
?>

<div class="pd-wrap h40per questionText questionSelectText <?=$answersCountCSSClass?>">
	<p><?=$q_data['q_text']?></p>
</div>

<div class="h50per questionSelectText <?=$answersCountCSSClass?>">
<form action="index.php?action=check_user_answer&qid=<?=$q_data['id']?><?=isset($_GET['pid'])?"&pid={$_GET['pid']}":""?>" method="post" class="mr0 rel">
	<div class="cleared hAuto">
		<? $i = 1;
		foreach ($answers as $key => $answer) : ?>
			<div class="w50per floatLeft mrb-10">
				<div class="choice flex flex-h-center">
					<input type="radio" id="answer<?=$i?>" name="answer" value="<?=$i?>">
					<label for="answer<?=$i?>" class="">
						<span class="box"></span>
						<span class="check"></span>
						<?=$answer['answer']?>
					</label>
				</div>
			</div>	
		<? $i++;
		endforeach; ?>
	</div>
	<div class="questionButtons text-right">
		<a href="<?=$skip_link?>" class="btn rounded skip_answer w20per"><img src="/images/design/gold-coin.png" alt=""><span><?=$points_to_skip?></span> Пропустить</a>
		<input type="submit" name="check_answer" value="Проверить" class="btn rounded check_answer w20per">
	</div>


	
</form>
</div>
<script>
$(document).ready(function() {
<? if ( (isset($active_sessia['questions'][($active_sessia['current']+1)]) || ($active_sessia['current']+1)==seria_count_questions($_SESSION['user']->id,$_GET['pid'])) && !is_seria_opros($_GET['pid']) ) { ?>
	$("form").on('submit', function(event) {
		event.preventDefault(event);
		/* Act on the event */
		var data_url = $(this).attr('action'),
			form_data = $(this).serialize();

		$.ajax({
			url: data_url,
			type: 'post',
			dataType: 'html',
			data: form_data
		})
		.done(function(data) {
			$.magnificPopup.open({
			  items: {
			    src: data,
				type: 'inline'
			  },
			  showCloseBtn: false,
			  closeOnBgClick: false
			});
		})
		.fail(function() {
			// console.log("error");
		})
		.always(function() {
			// console.log("complete");
		});
	});
<? } ?>

	$(".skip_answer").on('click', function(event) {
		event.preventDefault(event);
		/* Act on the event */
		var data_url = $(this).attr('href');

		$.ajax({
			url: data_url,
			type: 'post',
			dataType: 'html'
		})
		.done(function(data) {
			$.magnificPopup.open({
			  items: {
			    src: data,
				type: 'inline'
			  },
			  showCloseBtn: false,
			  closeOnBgClick: false
			});
			$(".closeSkipPopup").on('click', function(event) {
				event.preventDefault();
				$.magnificPopup.close();
			});
		})
		.fail(function() {
			// console.log("error");
		})
		.always(function() {
			// console.log("complete");
		});
	});


});	
</script>
