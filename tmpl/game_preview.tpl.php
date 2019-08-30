<div class="mr0 pd-wrap hAuto">
    <div class="hAuto">
        <h2 class="text-center mrt-10 mrb-10">Привет, <?=$_SESSION['user']->name;?>!</h2>
        <div class="text-center hAuto">
            <p class="mr0">Благодарим за регистрацию!</p>
            <p class="mrb-20">Надеемся, что тебе понравится игра ЗАМЕЧАТЕЛЬНЫЕ ПРИЗЫ ОТ FAMILIA</p>
        </div>
    </div>

    <div class="gameGreetings">
        <div class="gameEtaps cleared">
            <div class="gameEtap text-center">
                <div class="img flex flex-vh-center">
                    <div><img src="/images/design/greetings01.jpg" alt=""></div>
                </div>
                <h3>Выполняй задания и получай баллы</h3>
                <p>Тебе предстоит отвечать на вопросы, за правильные ответы на которые ты будешь полчучать баллы</p>
            </div>
            <div class="gameEtap text-center">
                <div class="img">
                    <img src="/images/design/greetings02.jpg" alt="">
                </div>
                <h3>Выполняй задания и получай баллы</h3>
                <p>Тебе предстоит отвечать на вопросы, за правильные ответы на которые ты будешь полчучать баллы</p>
            </div>
            <div class="gameEtap text-center">
                <div class="img">
                    <img src="/images/design/greetings03.jpg" alt="">
                </div>
                <h3>Обменивай баллы на ценные призы</h3>
                <p class="mrt-20"> <a href="index.php?action=show_prizes" class="btn btn-lg rounded prizes">смотреть призы</a> </p>
            </div>
        </div>
    </div>

    <div class="hAuto text-center mrt-20">
        <a href="index.php?action=start_game" class="btn btn-lg rounded startGame" onclick="fbq('track', 'CompleteRegistration'); yaCounter46338240.reachGoal('NachatIgru'); return true;">Начать игру</a>
    </div>
</div>

<div class="gameGreetingsCarousel">
    <div class="violetHeader">
        <div class="col-xs-12 text-center">
            <div class="middle wFull">
                <div>
                    <img src="images/design/familia-logo-dark-violet.jpg" alt="">
                </div>
            </div>
        </div>
    </div>
    <div class="h80per">
        <div class="gameEtaps carousel-widget"
            data-items="1"
            data-nav="false"
            data-pager="true"
            data-itemrange="0,1|420,1|600,1|768,1|992,1|1200,1"
            data-margin="50"
            data-autoplay="false"
            data-hauto="false"
            data-in="false"
            data-out="false"
            data-center="false">
            <div class="owl-carousel carousel">
            <div class="item gameEtap text-center">
                <div class="img flex flex-vh-center">
                    <div><img src="/images/design/greetings01-mob.jpg" alt=""></div>
                </div>
                <h3>Привет, <?=$_SESSION['user']->name;?>!</h3>
                <p>Благодарим за регистрацию!<br>Надеемся, что тебе понравится игра ЗАМЕЧАТЕЛЬНЫЕ ПРИЗЫ ОТ FAMILIA</p>
            </div>
            <div class="item gameEtap text-center">
                <div class="img">
                    <img src="/images/design/greetings02-mob.jpg" alt="">
                </div>
                <h3>Выполняй задания и получай баллы</h3>
                <p>Тебе предстоит отвечать на вопросы, за правильные ответы на которые ты будешь полчучать баллы</p>
            </div>
            <div class="item gameEtap text-center">
                <div class="img">
                    <img src="/images/design/greetings03-mob.jpg" alt="">
                </div>
                <h3>Переходи на новый уровень и открывай новые задания!</h3>
                <p>С получением нового уровня открывается доступ к новым заданиям и возможности получить еще больше баллов</p>
            </div>
            <div class="item gameEtap text-center">
                <div class="img">
                    <img src="/images/design/greetings04-mob.jpg" alt="">
                </div>
                <h3>Обменивай баллы на ценные призы</h3>
                <p class="mrt-20"> <a href="index.php?action=show_prizes" class="btn btn-lg rounded prizes">смотреть призы</a> </p>
            </div>
            </div>
        </div>
    </div>
    <div class="h10per">
        <div class="col-xs-12 text-center">
            <div class="middle wFull">
                <div>
                    <a href="index.php?action=start_game" class="btn btn-lg rounded startGame" onclick="fbq('track', 'CompleteRegistration'); yaCounter46338240.reachGoal('NachatIgru'); return true;">Начать игру</a>
                </div>
            </div>
        </div>
    </div>
</div>