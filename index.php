<?php
ini_set("display_errors",0);
error_reporting(E_ALL & ~E_NOTICE);

define('DR',rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/');
require DR . 'lib/fe_func.php';

if ( isset($_SESSION['user']) ) {
	if (!isset($_SESSION['user']->id)) $_SESSION['user']->id=get_user_id($_SESSION['user']->socialId);
}

if ( $_GET['action'] == 'pravo' ) {
	view('pravo', $_REQUEST);
	die;
}
view_land();
die;

switch ($_GET['action']) {
	// АВТОРИЗАЦИЯ ЧЕРЕЗ СОЦ СЕТИ
	// 
	case 'vk':
        $data = auth_social($_REQUEST);
        view('game_preview', $data);
		break;
	case 'facebook':
		$data = auth_social($_REQUEST);
		view('game_preview', $data);
		break;
	case 'odnoklassniki':
		$data = auth_social($_REQUEST);
		view('game_preview', $data);
		break;

	case 'start_game':
		/*$data = get_questions();
		view('questions', $data);*/
		if (!isset($_SESSION['user'])) {
			view_login('login', $data);
		}else{
			if ( empty($_SESSION['user']) ) {
				view_login('login', $data);
			} else {
				$data = get_questions();
				view('questions', $data);
			}
		}
		break;

	// покажем вопрос на экране
	case 'show_question':
	    // если на вопрос уже ответили и пробуют перезагрузить страницу, перенаправить на главную страницу
		if (check_if_question_closed($_REQUEST['qid'],$_SESSION['user']->id)) {
			header("Location:".$_SERVER['PHP_SELF']);
			die;
		}
		### если вопросы закрыты по времени, то тоже нельзя смотреть просто вбив id
        $user_level = get_user_level($_SESSION['user']->id); // 
        $last_closed_questions = get_last_closed_questions($_SESSION['user']->id);
        if ( $user_level >= 6 ) {
	        if ( $last_closed_questions[2]['u_level'] >= 6 ) {
	        	$dt = new DateTime($last_closed_questions[2]['created']);
	        	$dt->add(new DateInterval('PT12H'));
	        	$dt_now = new DateTime();
	        	$interval = $dt_now->diff($dt);
	        	
	        	if ( $dt_now < $dt ) {		
					header("Location:".$_SERVER['PHP_SELF']);
					die;
				}
			}
		}


		// получим тип вопроса по его id
		$gloabal_q_type = get_question_type($_REQUEST['qid']);
		// если это серия вопросов, то показываем вопросы по-другому
		if ( $gloabal_q_type == 4 ) {
			$data = get_seria_questions($_REQUEST['qid']);
			view('seria_questions', $data);
		} else {
			$data = show_question($_REQUEST['qid']);
			view('show_question', $data);
		}
		
		break;

	// проверим ответ пользователя
	case 'check_user_answer':

		// если id вопроса уже закрыт
		// а страницу пробуют перезагрузить
		// редиректим на главную
		if (check_if_question_closed($_REQUEST['qid'],$_SESSION['user']->id)) {
			header("Location:".$_SERVER['PHP_SELF']);
			die;
		}
		### если вопросы закрыты по времени, то тоже нельзя смотреть просто вбив id
        $user_level = get_user_level($_SESSION['user']->id); // 
        $last_closed_questions = get_last_closed_questions($_SESSION['user']->id);
        if ( $user_level >= 6 ) {
	        if ( $last_closed_questions[2]['u_level'] >= 6 ) {
	        	$dt = new DateTime($last_closed_questions[2]['created']);
	        	$dt->add(new DateInterval('PT12H'));
	        	$dt_now = new DateTime();
	        	$interval = $dt_now->diff($dt);
	        	
	        	if ( $dt_now < $dt ) {		
					header("Location:".$_SERVER['PHP_SELF']);
					die;
				}
			}
		}		

		$data = check_user_answer($_REQUEST);

		// при выводе результата, проверяем серия это или просто вопрос
        if ( isset($_GET['pid']) ) {
            $user = $_SESSION['user'];
            if ( get_question_type($_GET['qid'])==1 ) {
            	if ( $data['final_type']=='good' ) {
            		seria_update_current($user->id,$_GET['pid'],$_GET['qid']);
            	}
            }else{
            	seria_update_current($user->id,$_GET['pid'],$_GET['qid']);
            }
            seria_update_score($user->id,$_GET['pid'],$data['points']);

            $active_sessia = seria_session($user->id,$_GET['pid']);
            $active_sessia = $active_sessia[0];
            $active_sessia['questions'] = explode("|", $active_sessia['questions']);

            // если идет серия, то
	        if( isset($active_sessia['questions'][$active_sessia['current']]) ) {
                // если родительский вопрос, который в $_REQUEST['pid']
                // это серия-опрос - тогда редиректим на следующий вопрос
		        if ( is_seria_opros($_GET['pid']) ) {
		            header("Location:".$_SERVER['PHP_SELF']."?action=show_question&qid=".$active_sessia['questions'][$active_sessia['current']]."&pid=".$_GET['pid']);
		            die;
		        }
            // если серия закончилась, $active_sessia['current'] будет на один больше чем посл елемент в
            // $active_sessia['questions'], следовательно существовать такой переменной не будет
		    }else{
		    	if( isset($active_sessia['questions'][($active_sessia['current']-1)]) ) { // попап для последнего вопроса в серии
		    		if ( !is_seria_opros($_GET['pid']) ) {
						echo render_seria_popup($data);
						die;
		    		}
				}
	            // редиректим на проверку родительского вопроса
		    	header("Location:".$_SERVER['PHP_SELF']."?action=check_user_answer&qid=".$active_sessia['parent_q']);
		    	die;
		    }
		    // тут все еще серия
            // выводим результаты попапами, ф-ция render_seria_popup($_REQUEST)
		    echo render_seria_popup($data);
	        die;
        }

		view('answer_result', $data);
		
		break;

	case 'show_new_level':
		$data = get_level(get_user_level($_SESSION['user']->id));
		$data['page'] = "showLevelPage";
		view('show_new_level', $data);
		break;

	case 'show_prizes':
		view_prizes();
		break;

	case 'check_prize':
		$data = check_prize($_REQUEST);		// returns html for popup
		echo $data;
		break;

	case 'get_prize':
		$data = get_prize($_REQUEST);
		view('get_prize', $data);
		break;
	case 'help':
		$data = help_page($_REQUEST);
		view('help_page', $data);
		break;
	case 'ask_us':
		view('ask_us', $data);
		break;
	case 'send_ask_us':
		$data = send_ask_us($_REQUEST);
		echo $data;
		break;
	case 'prize_request':
		$data = prize_request($_REQUEST);
		echo $data;
		break;
	case 'pravo':
		view('pravo', $_REQUEST);
		break;

	case 'save_img':
		if(isset($_POST['file'])) // в $_GET передаем номер кода
		{
		   $img = trim(strip_tags($_POST['file']));
		   header('Content-Type: image/jpg;');
		   header("Content-Disposition: attachment; filename=".$img.";");
		   readfile($img);
		   exit();
		}
		break;

	case 'skip_answer':
		$data = skip_answer($_REQUEST);
		echo render_skip_answer($data);
		break;

	case 'skip_question':
		$data = skip_question($_REQUEST);
		header("Location:".$_SERVER['PHP_SELF']);
		die;
		break;

    case 'logout':
        logout();
        view_login('login', $data);
        break;

    // удалить временно
    case 'reset_sessia':
    	$_SESSION['user']->active_questions = array();
    	unset($_SESSION['user']->skipped);
	    break;

	case 'points_for_share':
		echo points_for_share($_REQUEST);
		break;

	case 'register':
		
		view('game_preview', $data);
		break;

	default:
		if (!isset($_SESSION['user'])) {
			view_land();
			// view_login('login', $data);
		}else{
			if ( empty($_SESSION['user']) ) {
				view_login('login', $data);
			} else {
				$data = get_questions();
				view('questions', $data);
			}
		}
		break;
}




?>