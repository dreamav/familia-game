<div class="popupMainContainer">
	<div class="skipAnswer">
		<div class="finalContent text-center">
			<?=$data['text']?>
		</div>
		<div class="finalButton <?=$data['allow']?'allowSkip':''?>">
			<?
				if ( $data['allow'] ) {
					echo '<a href="#" class="btn closeSkipPopup">отмена</a>';
					echo '<a href="index.php?action=skip_question&qid='.$_GET['qid'].'" class="btn">пропустить</a>';
				}else{
					echo '<a href="index.php" class="btn closeSkipPopup">отмена</a>';
				}
			?>
		</div>
	</div>
</div>