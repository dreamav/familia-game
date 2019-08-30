<div class="violetHeader">
	<div class="col-md-1">
		<div class="middle">
			<a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
		</div>
	</div>
</div>
<div class="h80per questionFinal">

<? if ( $_GET['pt'] == 1 ) { ?>

	<div class="col-md-6">
		<div class="questionFinalTitle">
			<div class="flex flex-vh-center flex-column">
				<p>Поздравляем!<br>
					Вот электронный сертификат на ваш приз.
				</p>
				<img src="<?=$data['img']?>" alt="">
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="getPrizeText h50per text-center">
			<div class="questionButtons">
				<a href="<?=$data['img']?>" download class="btn rounded save_btn">Сохранить</a>
				<!-- <a href="" class="btn rounded print_btn">Распечатать</a> -->
			</div>
				<p>
					Воспользоваться сертификатом можно в любом магазине сети Familia. Распечатайте ваш сертификат и предъявите его в магазине. Сертификат нужно использовать до 1.01.2018
				</p>
		</div>
	</div>

<? } else { ?>

	<div class="col-md-6 col-md-offset-3">
		<div class="questionFinalTitle">
			<div class="flex flex-vh-center flex-column">
				<p>Поздравляем!<br>
					Введите ваш e-mail, мы пришлем вам ваш сертификат.
				</p>
		        <form>
		            <div class="form-group">
		                <input name="prize_request" class="form-control" id="pe" placeholder="Ваш e-mail">
		            </div>
		        </form>
				<div class="questionButtons">
					<a href="#" download class="btn rounded send_btn">Отправить</a>
					<!-- <a href="" class="btn rounded print_btn">Распечатать</a> -->
				</div>
			</div>
		</div>
	</div>

<?	} ?>

</div>

<script>

<? if ( $_GET['pt'] != 1 ) { ?>


	$('.send_btn').on('click', function(event) {
		event.preventDefault();
		var url = 'index.php?action=prize_request',
			form = $('form'),
			data = form.serialize();

		$.ajax({
			url: url,
			type: 'post',
			dataType: 'html',
			data: data
        })
		.done(function(data) {
			console.log("success");
			$('.col-md-6 .questionFinalTitle').html(data);

		})
		.fail(function() {
			console.log("error");
		})
		.always(function() {
			console.log("complete");
		});
	});



<? } ?>

</script>