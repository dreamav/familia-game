<div class="violetHeader">
    <div class="col-md-1 col-xs-2">
        <div class="middle">
            <a href="/index.php"><img src="images/design/home-link.jpg" alt=""></a>
        </div>
    </div>
    <div class="col-md-10 col-xs-8 text-center">
        <div class="middle wFull">
            <div>
                <h2 class="questionTitle">ФОРМА ОБРАТНОЙ СВЯЗИ</h2>
            </div>
        </div>
    </div>
</div>
<div class="h80per mr0 pd-wrap helpPage">
    <div class="col-md-6 col-md-offset-3">
        <form>
            <div class="form-group">
                <label for="pe">свяжитесь со мной через</label>
                <input name="contact" class="form-control" id="pe" placeholder="Ваш e-mail">
            </div>
            <div class="form-group">
                <label for="message">ваше сообщение:</label>
                <textarea name="message" id="message" class="form-control" placeholder="Текст сообщения"></textarea>
            </div>
            <input type="hidden" name="u_id" value="<?=$_SESSION['user']->id?>">
        </form>
        <p>Техническая поддержка: +7 (495) 162-77-52</p>
        <a href="index.php?action=pravo&p=politika">Политика конфиденциальности</a>
    <div class="text-center">
        <a href="#" class="btn btn-lg rounded send_ask_us">Отправить</a>
    </div>
    </div>
</div>
<script>
jQuery(document).ready(function($) {
    $('.send_ask_us').on('click', function(event) {
        event.preventDefault();
        var url = 'index.php?action=send_ask_us',
            form = $('form'),
            data = form.serialize();

        $.ajax({
            url: url,
            type: 'post',
            dataType: 'html',
            data: data,
        })
        .done(function(data) {
            console.log("success");
            $('.helpPage .col-md-6').html(data);

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