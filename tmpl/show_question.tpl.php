
<div class="violetHeader">
	<div class="col-md-1 col-xs-2">
		<div class="middle">
			<a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
		</div>
	</div>
	<div class="col-md-10 col-xs-8 text-center">
		<div class="middle wFull">
			<div>
				<h2 class="questionTitle"><?=get_question_title($_GET['qid'])?></h2>
			</div>
		</div>
	</div>
</div>

<?=$data;?>



<script>
var ispopup = '<div class="ispopup"></div>';
$('.questionText p img').closest('p').css({position:'relative'}).append(ispopup);
</script>