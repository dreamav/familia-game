<?php
// ini_set("display_errors",1);
// error_reporting(E_ALL & ~E_NOTICE);
define('DR',rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/');

// require DR . 'lib/functions.php';
require DR . 'lib/fam_functions.php';

// если отправлена форма авторизации, то:
if (!empty($_POST['username'])) {


	if($user_id = auth($_POST['username'],$_POST['password'])){

		// если проверка пройдена, установить для менеджера куки
		setcookie('user_id', $user_id['id']);
		setcookie('user_level', $user_id['p_level']);
		// перенаправить на основную таблицу
		header("Location: /admin/index.php?action=questions");

	}
}

// возможности для менеджера с уровнем доступа 0
if(isset($_COOKIE['user_level']) && $_COOKIE['user_level'] == 0){
	// роутинг
	switch ($_GET['action']) {
		// основная страница
		case 'questions':
			$data = get_questions(0);
			view('questions', $data);
			break;
		// добавить новый вопрос
		case 'add_question':
			$data = add_question(0);
			echo $data;
			break;
		// редактировать вопрос
		case 'edit_question':
			$data = edit_question($_REQUEST);
			view('edit_question', $data[0]);
			break;
		// сохранить вопрос
		case 'save_question':
			$data = save_question($_REQUEST);
			echo $data;
			break;
		// удалить вопрос
		case 'del_question':
			$data = del_question($_REQUEST['qid']);
			echo $data;
			break;
		// удалить ответ AJAX
		case 'remove_answer':
			del_answer($_REQUEST['a_id']);
			break;



		// уровни игры
		case 'game_levels':
			$data = get_game_levels(0);
			view('game_levels', $data);
			break;
		// добавить новый уровень
		case 'add_game_level':
			$data = add_game_level(0);
			echo $data;
			break;
		// редактировать уровень
		case 'edit_game_level':
			$data = edit_game_level($_REQUEST);
			view('edit_game_level', $data[0]);
			break;
		// сохранить уровень
		case 'save_game_level':
			$data = save_game_level($_REQUEST);
			echo $data;
			break;
		// удалить уровень
		case 'del_game_level':
			$data = del_game_level($_REQUEST['qid']);
			echo $data;
			break;


		// показать категории
		case 'q_categories':
			$data = get_q_categories(0);
			view('categories', $data);
			break;
		// добавить новую категорию
		case 'add_category':
			$data = add_q_category(0);
			echo $data;
			break;
		// редактировать категорию
		case 'edit_category':
			$data = edit_q_category($_REQUEST);
			view('edit_category', $data[0]);
			break;
		// сохранить уровень
		case 'save_category':
			$data = save_q_category($_REQUEST);
			echo $data;
			break;
		// удалить уровень
		case 'del_category':
			$data = del_q_category($_REQUEST['qid']);
			echo $data;
			break;


		// показать пользователей
		case 'users':
			$data = get_users();
			view('users', $data);
			break;
		// редактировать пользователя
		case 'edit_user':
			$data = edit_user($_REQUEST);
			view('edit_user', $data[0]);
			break;
		// сохранить пользователя
		case 'save_user':
			$data = save_user($_REQUEST);
			echo $data;
			break;
		// получить сертификаты пользователя
		case 'get_user_certs':
			$data = get_user_certs($_REQUEST);
			view('user_certs', $data);
			break;
		// показатьигровой процесс пользователя
		case 'show_user_gameplay':
			$data = show_user_gameplay($_REQUEST);
			view('user_gameplay', $data);
			break;
		// показать историю шаринга пользователя
		case 'show_user_share':
			$data = show_user_share($_REQUEST);
			view('user_share', $data);
			break;


		case 'manual_check':
			$data = get_manual_check($_REQUEST);
			view('manual_check', $data);
			break;
		case 'admin_add_points':
			$data = admin_add_points($_REQUEST);
			header("Location: /admin/index.php?action=manual_check");
			break;



		case 'certs':
			$data = get_certs($_REQUEST);
			view('certs', $data);
			break;

			

		// АВТОРИЗАЦИЯ, ВЫХОД
		case 'logout':

			unset($_COOKIE['user_id']);
			unset($_COOKIE['user_level']);
			setcookie('user_id', null, -1);
			setcookie('user_level', null, -1);
			header("Location: /admin/index.php");

			break;
		// . АВТОРИЗАЦИЯ, ВЫХОД
		
		default:
			if (!isset($_COOKIE['user_id'])) {
				# code...
				view_login('login', $data);
			}else{
				$data = get_questions(0);
				view('questions', $data);
			}
			break;
	}
} else {
	view_login('login', $data);
}