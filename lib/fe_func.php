<?php
session_start();

include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'admin/classes/DBclass.php');
require_once rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/SocialAuther/autoload.php';

$DB = new c_database();
$DB->iniSet();
$DB->connect();


// ф-ция вывода шаблона
function view($path, $data = null){
    if (is_array($data)) {
        extract($data);
    }

    $path = $path.'.tpl.php';
    
    include "tmpl/layout.php";
}
// показать страницу входа, если пользователь не авторизирован
function view_login($path, $data = null){
    if ($data){
        extract($data);
    }

    $path = $path.'.tpl.php';
    
    include "tmpl/layout.php";
}
// показать лендинг
function view_land(){

	include "tmpl/landing.php";

}
// показать страницу призов
function view_prizes(){

	include "tmpl/prizes.php";

}
// авторизация пользователя
function auth($l,$p){
    global $DB;

    $DB->query_exec("SELECT * FROM managers WHERE login = '{$l}'");
    $pass = $DB->fetch();
    $pass = $pass[0];

    if ($pass['password']==md5($p)) {
        return $res = array('id'=>$pass['id'],'p_level'=>$pass['p_level']);
    } else {
        return false;
    }

}
// авторизация пользователя
function auth_social($request){
    global $DB;

    include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/social_config.php');

	$adapters = array();
	foreach ($adapterConfigs as $adapter => $settings) {
	    $class = 'SocialAuther\Adapter\\' . ucfirst($adapter);
	    $adapters[$adapter] = new $class($settings);
	}


	if (isset($request['action']) && array_key_exists($request['action'], $adapters)) {
		$auther = new SocialAuther\SocialAuther($adapters[$request['action']]);
	}

	if ($auther->authenticate()) {

		### тут можно использовать ф-цию get_user_id($socialId)
		$DB->query_exec("SELECT *  FROM `users` WHERE `provider` = '{$auther->getProvider()}' AND `social_id` = '{$auther->getSocialId()}' LIMIT 1");
	    $record = $DB->fetch();

	    if (!$record) {
	        $values = array(
	            $auther->getProvider(),
	            $auther->getSocialId(),
	            $auther->getName(),
	            $auther->getEmail(),
	            $auther->getSocialPage(),
	            $auther->getSex(),
	            date('Y-m-d', strtotime($auther->getBirthday())),
	            $auther->getAvatar()
	        );

	        $query = "INSERT INTO `users` (`provider`, `social_id`, `name`, `email`, `social_page`, `sex`, `birthday`, `avatar`) VALUES ('";
	        $query .= implode("', '", $values) . "')";
	        $DB->query_exec($query);
	        $new_user_id = $DB->GetLastID();
	    }else {
	    	$record = $record[0];
		    $userFromDb = new stdClass();
		    $userFromDb->provider   = $record['provider'];
		    $userFromDb->socialId   = $record['social_id'];
		    $userFromDb->name       = $record['name'];
		    $userFromDb->email      = $record['email'];
		    $userFromDb->socialPage = $record['social_page'];
		    $userFromDb->sex        = $record['sex'];
		    $userFromDb->birthday   = date('m.d.Y', strtotime($record['birthday']));
		    $userFromDb->avatar     = $record['avatar'];
		}

		$user = new stdClass();
		$user->provider   = $auther->getProvider();
		$user->socialId   = $auther->getSocialId();
		$user->name       = $auther->getName();
		$user->email      = $auther->getEmail();
		$user->socialPage = $auther->getSocialPage();
		$user->sex        = $auther->getSex();
		$user->birthday   = $auther->getBirthday();
		$user->avatar     = $auther->getAvatar();

		if ( isset($new_user_id) ) {
			$user->id = $new_user_id;
		}elseif ( isset($record['id']) ) {
			$user->id = $record['id'];
		}

		$_SESSION['user'] = $user;
		
		// проверяем на равенство объект с данными из БД и объект с данными из социалки
		if (isset($userFromDb) && $userFromDb != $user) {
		    $idToUpdate = $record['id'];
		    $birthday = date('Y-m-d', strtotime($user->birthday));

		    $DB->query_exec("UPDATE `users` SET " .
		        "`social_id` = '{$user->socialId}', `name` = '{$user->name}', `email` = '{$user->email}', " .
		        "`social_page` = '{$user->socialPage}', `sex` = '{$user->sex}', " .
		        "`birthday` = '{$birthday}', `avatar` = '$user->avatar' " .
		        "WHERE `id`='{$idToUpdate}'");
		}
	}

	$res['page'] = "gameGreetingsPage";

	return $res;
}
// выход пользователя
function logout(){
    session_destroy();
    $_SESSION = array();
}

// show questions titles list to user:
function get_questions($qid=""){
	global $DB;

	// получим уровень пользователя
	$user_level = get_user_level($_SESSION['user']->id);
//	$_SESSION['user']‌‌->active_questions
	// проверим уровень пользователя
	if ( $user_level == 0 ) {
	// если уровень пользователя 0
	// показать самый первый вопрос
		$DB->query_exec("SELECT * FROM questions WHERE q_hard = 1 AND q_visibility = 1
			AND id NOT IN (SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1)
			AND ISNULL(parent_id) ORDER BY q_nomer ASC LIMIT 1");
		$questions = $DB->fetch();


	} elseif ( $user_level == 1 || $user_level == 2 ) {

		// если уровень 1 или 2
		// выводим по два вопроса
			// если есть еще не закрытые вопросы с жестким порядком
			// то вывести следующий не закрытый номер
			
			// получим все оставшиеся вопросы с номером
			$DB->query_exec("SELECT *
							FROM questions
							WHERE q_hard = 1 AND q_visibility = 1 AND
							id NOT IN (SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1) AND ISNULL(parent_id)
							ORDER BY q_nomer ASC");
			$hard_ord_questions = $DB->fetch();
			// до тех пор пока в массиве будет 2 или больше вопросов
			// будем выводить первые два
			if ( count($hard_ord_questions) >= 2 ) {
				$questions = first_n_arr_el($hard_ord_questions,2);
			}elseif (count($hard_ord_questions) == 1) {
				$questions = first_n_arr_el($hard_ord_questions,1);

				if ( empty($_SESSION['user']->active_questions) || count($_SESSION['user']->active_questions)>=2 ) {
					$_SESSION['user']->active_questions = array();
					$exl_categories_id = get_question_cats_ids($questions);

					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
		            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $random_questions_test = get_random_questions($random_categories,1);
					$questions[] = $random_questions_test[0];
					$_SESSION['user']->active_questions[0] = $random_questions_test[0];
				}else{
					if ( count($_SESSION['user']->active_questions)==1 ) {
						$questions[] = $_SESSION['user']->active_questions[0];
					}
				} // if ( empty($_SESSION['user']->active_questions) )

			}else{
				if ( empty($_SESSION['user']->active_questions) ) {
					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
					$avaibleSubCategories = get_avaible_Subcategories($closedSubCategories);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $questions = get_random_questions($random_categories,2);
                    $_SESSION['user']->active_questions = $questions;
		        }else{
		        	if ( count($_SESSION['user']->active_questions)==2 ) {
		        		$questions = $_SESSION['user']->active_questions;
		        	}elseif (count($_SESSION['user']->active_questions)==1) {
		        		$questions[] = $_SESSION['user']->active_questions[0];
		        		$exl_categories_id = get_question_cats_ids($questions);

						$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
			            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
			            $random_categories = get_random_categories($avaibleSubCategories);
			            $random_questions_test = get_random_questions($random_categories,1);
						$questions[] = $random_questions_test[0];
						$_SESSION['user']->active_questions = $questions;
		        	}
		        }
			} // if ( count($hard_ord_questions) >= 2 )

	} else { // if user level >= 3

		// если уровень 3 или выше
		// выводим по три вопроса
		// получим все оставшиеся вопросы с номером
		$DB->query_exec("SELECT *
						FROM questions
						WHERE q_hard = 1 AND q_visibility = 1 AND
						id NOT IN (SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1) AND ISNULL(parent_id)
						ORDER BY q_nomer ASC");
		// вопросы которые выводяться в жестком порядке
		$hard_ord_questions = $DB->fetch();
		// до тех пор пока в массиве будет 3 или больше вопросов
		// будем выводить первые два
		if ( count($hard_ord_questions) >= 3 ) { // если 3 жестких вопроса
		    // формируем массив из жестких вопросов
			$questions = first_n_arr_el($hard_ord_questions,3);
		}elseif (count($hard_ord_questions) == 2) {
            // формируем массив из жестких вопросов
			$questions = first_n_arr_el($hard_ord_questions,2);

			if ( empty($_SESSION['user']->active_questions) || count($_SESSION['user']->active_questions)>=2 ) {
				$_SESSION['user']->active_questions = array();
				$exl_categories_id = get_question_cats_ids($questions);

				$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
	            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
	            $random_categories = get_random_categories($avaibleSubCategories);
	            $random_questions_test = get_random_questions($random_categories,1);
				$questions[] = $random_questions_test[0];
				$_SESSION['user']->active_questions[] = $random_questions_test[0];
			}else{
				if ( count($_SESSION['user']->active_questions)==1 ) {
					$questions[] = $_SESSION['user']->active_questions[0];
				}
			} // if ( empty($_SESSION['user']->active_questions) )

		}elseif (count($hard_ord_questions) == 1) {
            
			$questions = first_n_arr_el($hard_ord_questions,1);
            
			if ( empty($_SESSION['user']->active_questions) || count($_SESSION['user']->active_questions)>2 ) {
				$_SESSION['user']->active_questions = array();
				$exl_categories_id = get_question_cats_ids($questions);

				$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
	            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
	            $random_categories = get_random_categories($avaibleSubCategories);
	            $random_questions_test = get_random_questions($random_categories,2);
	            foreach ($random_questions_test as $key => $r_question) {
					$questions[] = $r_question;
					$_SESSION['user']->active_questions[] = $r_question; // записали 2
				}
			}else{
				if ( count($_SESSION['user']->active_questions)==2 ) { // если уже 2 то все ок
		            foreach ($_SESSION['user']->active_questions as $key => $r_question) {
						$questions[] = $r_question;
					}					
				}elseif (count($_SESSION['user']->active_questions)==1) { // если 1, то еще один надо получить
                    $questions[] = $_SESSION['user']->active_questions[0]; // вот тут 2 елемента в questions

                    $exl_categories_id = get_question_cats_ids($questions);

					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
		            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $random_questions_test = get_random_questions($random_categories,1);

		            foreach ($random_questions_test as $key => $r_question) {
						$questions[] = $r_question;
						$_SESSION['user']->active_questions[] = $r_question; // записали второй
					}
				}
			} // if ( empty($_SESSION['user']->active_questions) )
		}else{ // если нет жестких вопросов $hard_ord_questions = 0
			if ( empty($_SESSION['user']->active_questions ) ) {
				$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
				$avaibleSubCategories = get_avaible_Subcategories($closedSubCategories);
				$random_categories = get_random_categories($avaibleSubCategories);

				$questions = get_random_questions($random_categories,3);
				$_SESSION['user']->active_questions = $questions;
			} else {
				if ( count($_SESSION['user']->active_questions)==3 ) {
					foreach ($_SESSION['user']->active_questions as $key => $r_question) {
						$questions[] = $r_question;
					}
				} elseif ( count($_SESSION['user']->active_questions)==2 ) {
					foreach ($_SESSION['user']->active_questions as $key => $r_question) {
						$questions[] = $r_question;
					}

					$exl_categories_id = get_question_cats_ids($questions);

					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
		            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $random_questions_test = get_random_questions($random_categories,1);
		            foreach ($random_questions_test as $key => $r_question) {
						$questions[] = $r_question;
					}
					$_SESSION['user']->active_questions = $questions;
				}elseif ( count($_SESSION['user']->active_questions)==1 ) {
					foreach ($_SESSION['user']->active_questions as $key => $r_question) {
						$questions[] = $r_question;
					}

					$exl_categories_id = get_question_cats_ids($questions);

					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
		            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $random_questions_test = get_random_questions($random_categories,2);
		            foreach ($random_questions_test as $key => $r_question) {
						$questions[] = $r_question;
					}
					$_SESSION['user']->active_questions = $questions;
				}else{
					$closedSubCategories = get_closed_subcategories($_SESSION['user']->id);
		            $avaibleSubCategories = get_avaible_Subcategories($closedSubCategories,$exl_categories_id);
		            $random_categories = get_random_categories($avaibleSubCategories);
		            $random_questions_test = get_random_questions($random_categories,3);
		            foreach ($random_questions_test as $key => $r_question) {
						$questions[] = $r_question;
					}
					$_SESSION['user']->active_questions = $questions;
				}
			}
		}
	}

	// если хотя бы один элемент массива $question = null
	// значит что-то пошло не так
	// значит нужно обнулить $_SESSION[user]->active_questions
	// и вернуться назад для генерации новых трех вопросов
	foreach ($questions as $key => $value) {
		if ( $questions[$key] == null ) {
			$_SESSION['user']->active_questions = array();
			header("Location:".$_SERVER['PHP_SELF']);
			die;
		}
	}

	$res = $questions;
    return $res;
}
// получим вопросы для серии
function get_seria_questions($qid){
    global $DB;
	// получаем все дочерние вопросы
	$DB->query_exec("SELECT * FROM questions WHERE parent_id = {$qid}");
	$questions = $DB->fetch();
	$res['questions'] = $questions;
	// получаем данные родительского вопроса
	$DB->query_exec("SELECT * FROM questions WHERE id = {$qid}");
	$main_question = $DB->fetch();
	$res['main_question'] = $main_question[0];
	return $res;
}

// show single question 
function show_question($qid){
	global $DB;

	$q_type = get_question_type($qid);

	if ( $q_type ==4 ) {
		
	} else {
		return render_question_type($qid,$q_type);
	}

}
function check_user_answer($data){
	global $DB;

	$q_type = get_question_type($data['qid']);

	if ( $q_type == 4 ) {
		// посчитать очки
		// $_SESSION['sess_score'] очки набранные за сессию, отвечая на вопросы
		// $_SESSION['max_posible_points'] очки набранные за сессию, отвечая на вопросы
		// макс к-во очков
		$max_score = get_question_score($data['qid']);
		// сколько очков зачислить пользователю

		$act_sessia = seria_session($_SESSION['user']->id,$data['qid']);
		$act_sessia = $act_sessia[0];

		$earned_points = round(($act_sessia['sess_score']/$act_sessia['max_posible_points'])*$max_score);

		// какой финал вывести
			// получить все финалы
		$all_finals = final_check($data['qid']);
		if ( $earned_points >= $all_finals[0]['final_points'] ) {
			$final = get_question_final("good",$data['qid']);
			$res['final_type'] = "good";
			$res["correct_answer"] = true;
		} elseif ( $earned_points <= $all_finals[1]['final_points'] ) {
			$final = get_question_final("bad",$data['qid']);
			$res['final_type'] = "bad";
		}else{
			$final = get_question_final("neut",$data['qid']);
			$res['final_type'] = "good";
			$res["correct_answer"] = true;
		}

		
		$finale_text = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $final['final']);
		$res['final_text'] = unserialize($finale_text);
		if ( is_seria_opros($data['qid']) ) {
			$_SESSION['user']->score = update_score($_SESSION['user']->id,$data['qid'],$max_score);
			$res['points'] = $max_score;
            $res["correct_answer"] = true;
		}else{
			$_SESSION['user']->score = update_score($_SESSION['user']->id,$data['qid'],$earned_points);
			$res['points'] = $earned_points;
		}

		close_session($_SESSION['user']->id,$data['qid']);

		$gp_values = array($_SESSION['user']->id, $data['qid'], "seria ended", $res['points'], 1 );
		update_user_gameplay($gp_values);

	}else{
	    ### ЕСЛИ ЕСТЬ $data['pid']
        ### ТО ЭТО СЕРИЯ ВОПРОСОВ
        ### ПОЛУЧИМ ЕЕ
        if ( isset($data['pid']) ){
            $active_sessia = seria_session($_SESSION['user']->id,$data['pid']);
            $active_sessia = $active_sessia[0];
        }
		// получим правильные ответы из базы
		$right_answers = get_right_answers($data['qid'],$q_type);
		$correct_qtity = 0;
		// если тип выбор из 
		if ( $q_type != 1 ) {
			### тип вопроса ВЫБОР ИЗ ВАРИАНТОВ
			
			if ( !empty($right_answers) ) {
				// получим id правильных ответов в массив
				$right_answers = explode("|", $right_answers['q_correct']);
				if ( is_array($right_answers) ) {
					foreach ($right_answers as $key => $r_a_nomer) {
						// если введенный ответ совпадает с одним их ответов из базы
						// тогда запишем что правильный ответ есть
						if ( $_POST['answer'] == $r_a_nomer) {
							$correct_qtity++;
						}
					}
				}
			}

			// если есть правильнй ответ
			// тогда:
			if ( $correct_qtity > 0 ) {
				// получим правильный финал
				$final = get_question_final("good",$data['qid']);
                $res["correct_answer"] = true;

				$active_sessia = seria_session($_SESSION['user']->id,$data['pid']);
                $active_sessia = $active_sessia[0];

				if ( isset($active_sessia['questions'])==false ) {
					$_SESSION['user']->score = update_score($_SESSION['user']->id,$data['qid']);
				}
				// вернем для вывода сколько баллов заработал пользователь за вопрос
				$res['points'] = get_question_score($data['qid']);
				$res['final_type'] = "good";

				// вот тут вместо сессий запишем в бд
				// новый счет/баллы пользователя
				// и потом из базы будем их получать
				// $values = array($u_id, $q_id, $answer, $score, $closed );
				$gp_values = array($_SESSION['user']->id, $data['qid'], $_POST['answer'], $res['points'], 1 );
				update_user_gameplay($gp_values);

			}else{
				$res['final_type'] = "bad";
				// если ответ не правильный
				// покажем плохой финал
				$final = get_question_final("bad",$data['qid']);

				$gp_values = array($_SESSION['user']->id, $data['qid'], $_POST['answer'], 0, 1 );
				update_user_gameplay($gp_values);
			}
			// вернем для вывода тексты финала
			// 
			$final['final'] = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $final['final']);
			$res['final_text'] = unserialize($final['final']);
		}else{
			### тип вопроса ВВОД ОТВЕТА
			
			if ( is_array($right_answers) ) {
				foreach ($right_answers as $key => $answer) {
					$answer = $answer['answer'];
					// уберем ненужные кавчки, если вдруг будут
					$answer = str_replace("\"", "", $answer);
					$answer = str_replace("“", "", $answer);
					$answer = str_replace("”", "", $answer);
					$answer = str_replace("«", "", $answer);
					$answer = str_replace("»", "", $answer);
					$answer = str_replace("", "", $answer);
					// в нижний регистр все буквы в ответе из админки
					$for_pattern = mb_strtolower($answer, 'UTF-8');
					// паттерн по который искать в ответе. в начале слова
					$pattern = '/^'.$for_pattern.'.*/';

					$user_answer = $_POST['answer'];
					// уберем ненужные кавчки из ответа введенного пользователем
					$user_answer = str_replace("\"", "", $user_answer);
					$user_answer = str_replace("“", "", $user_answer);
					$user_answer = str_replace("”", "", $user_answer);
					$user_answer = str_replace("«", "", $user_answer);
					$user_answer = str_replace("»", "", $user_answer);
					$user_answer = str_replace("", "", $user_answer);
					// в нижний регистр все буквы в ответе пользователя
					$answer_for_match = mb_strtolower($user_answer, 'UTF-8');

					preg_match($pattern, $answer_for_match, $matches);
					if ($matches[0]) {
						$correct_qtity++;					
					}				
				}
			}


			// если ответ правильный
			if ( $correct_qtity > 0 ) {
				$final = get_question_final("good",$data['qid']);
				$res["correct_answer"] = true;

                $active_sessia = seria_session($_SESSION['user']->id,$data['pid']);
                $active_sessia = $active_sessia[0];

                if ( isset($active_sessia['questions'])==false ) {
                	if ( !is_manual($data['qid']) ) {
	                    $_SESSION['user']->score = update_score($_SESSION['user']->id,$data['qid']);
                	}
                }
				
				$res['points'] = get_question_score($data['qid']);
                $res['final_type'] = "good";

				$gp_values = array($_SESSION['user']->id, $data['qid'], $_POST['answer'], $res['points'], 1 );
				update_user_gameplay($gp_values);

				// запишем данные в google sheet
				if ( is_manual($data['qid']) ) {
					$gsheet_data = array ('uid' => $_SESSION['user']->id, 'uname' => $_SESSION['user']->name, 'qid' => $data['qid'], 'uanswer' => $_POST['answer'], 'email' => $_SESSION['user']->email );
					$gsheet_data = http_build_query($gsheet_data);
					$g_url = "https://script.google.com/macros/s/AKfycbyhH0vB0WNxXW1hweSHPHT8Wd-NT7vuRv3c9gsdDxjtn36S4dM/exec?".$gsheet_data;
					$g_res = fopen($g_url,'r');
					fclose($g_res);
				}

			// если ответ не правильный
			}else{
                $res['final_type'] = "bad";
				$final = get_question_final("bad",$data['qid']);
				$gp_values = array($_SESSION['user']->id, $data['qid'], $_POST['answer'], 0, 0 );
				update_user_gameplay($gp_values);
			}

            $finale_text = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $final['final']);
			$res['final_text'] = unserialize($finale_text);
		}
	}

	// сравнить баллы, набранные пользователем и баллы нужные для сл уровня
	$current_user_points = get_user_score_for_level($_SESSION['user']->id);
	$next_level_points = get_next_level_points($_SESSION['user']->id);
	if ( $current_user_points >= $next_level_points ) {
		// повысить уровень пользователя
		// и вывести поздравления
		if ( isset($active_sessia['questions'])==false ) {
        	if ( !is_manual($data['qid']) ) {
				$res['user_level'] = update_user_level($_SESSION['user']->id);
			}
    	}	
	}

	$res['page'] = "checkAnswerPage";
	return $res;
}
function update_score($user_id,$qid,$points=""){
	global $DB;

	$q_type = get_question_type($qid);

	if ( $q_type == 4 ) {
		// получить баллы пользователя
		$u_total_score = get_user_total_score($user_id);
		$u_level_score = get_user_score_for_level($user_id);


		$new_total_score = $u_total_score + $points;
		$new_level_score = $u_level_score + $points;

		// записали в базу, обновили общее к-во баллов
		$DB->query_exec("UPDATE users SET total_score={$new_total_score}, score_for_level={$new_level_score} WHERE id={$user_id};");		
	} else {
		// получить баллы пользователя
		$u_total_score = get_user_total_score($user_id);
		$u_level_score = get_user_score_for_level($user_id);

		$new_total_score = $u_total_score + get_question_score($qid);
		$new_level_score = $u_level_score + get_question_score($qid);
		// записали в базу, обновили общее к-во баллов
		$DB->query_exec("UPDATE users SET total_score={$new_total_score}, score_for_level={$new_level_score} WHERE id={$user_id};");
	}
	
	return $new_total_score;
}
function update_user_gameplay($values){
	global $DB;

    $u_level = get_user_level($_SESSION['user']->id);

	$query = "INSERT INTO gameplay (`u_id`, `q_id`, `answer`, `score`, `closed`,`u_level`) VALUES ('";
	        $query .= implode("', '", $values) . "',{$u_level})";
    $DB->query_exec($query);

    if ( $values[4] == 1 ) {
    	foreach ($_SESSION['user']->active_questions as $key => $act_question) {
    		if ( $act_question['id'] == $values[1] ) {
    			unset($_SESSION['user']->active_questions[$key]);
    		}
    	}
    }
}
function check_if_question_closed($qid,$uid=''){
	global $DB;

	$DB->query_exec("SELECT q_id FROM gameplay WHERE q_id = {$qid} AND u_id = {$uid} AND closed = 1");
	$res = $DB->fetch();

	return empty($res)?false:true;

}
function random_question($exl_categories_id,$q_limit,$exl_q_ids=""){
	global $DB;

	if ( is_array($exl_categories_id) ) {
        $exl_categories = implode(",", $exl_categories_id);

        $DB->query_exec("SELECT *
							FROM questions
							WHERE q_hard = 0 AND q_visibility = 1 AND
							q_category NOT IN ({$exl_categories}) AND
							id NOT IN (SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1) AND ISNULL(parent_id)
							LIMIT {$q_limit}");
        $random_questions = $DB->fetch();
    } else {
    	if (!empty($exl_q_ids)) {
    		$exl_q_string = implode(",", $exl_q_ids);
    		$query_where = " AND id NOT IN (".$exl_q_string.")";
    	}

        $DB->query_exec("SELECT *
							FROM questions
							WHERE q_hard = 0 AND q_visibility = 1
							".$query_where."
							AND id NOT IN (SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1) AND ISNULL(parent_id)
							GROUP BY q_category
							LIMIT {$q_limit}");
        $random_questions = $DB->fetch();

        for ($i=0; $i < $q_limit; $i++) { 

	        foreach ($random_questions as $key => $value) {
	        	$exl_q_ids[] = $value['id'];
	        }
        	if (count($random_questions)>0){
                if ( count($random_questions) < $q_limit ) {
                    $q_to_add = random_question("", 1, $exl_q_ids);
                    if (!empty($q_to_add[0]))
                    $random_questions[] = $q_to_add[0];
                }
            }

        }
    }



	return $random_questions;
}
function get_question_cats_ids($questions){
	global $DB;

	foreach ($questions as $key => $value) {
		$q_cat_ids[] = $value['q_category'];
	}

	return $q_cat_ids;	
}
function get_closed_subcategories($uid){
	global $DB;
	$DB->query_exec("SELECT q_id FROM gameplay WHERE u_id = {$uid} AND closed = 1 GROUP BY q_id");
	$closedQuestions = $DB->fetch();
	foreach ($closedQuestions as $key => $qid) {
		$DB->query_exec("SELECT q_category FROM questions WHERE id = {$qid['q_id']}");
		$cat_id = $DB->fetch();
		$cat_id = $cat_id[0]['q_category'];
		// если это подкатегория, запишем в массив
		$DB->query_exec("SELECT p_id FROM categories WHERE id = {$cat_id}");
		$check_sub_cat = $DB->fetch();
		if ( $check_sub_cat[0]['p_id'] != 0 ) {
			$subCategories[] = $cat_id;
		}
	}

	return $subCategories;

}
function get_avaible_Subcategories($closedSubCategories,$exl_categories_id=0){
	global $DB;

	// отсекаем закрытые подкатегории
	if (!empty($closedSubCategories)) {
		$exl_q_string = implode(",", $closedSubCategories);
		$query_where = " AND id NOT IN (".$exl_q_string.")";
	}else{
		$query_where = "";
	}

	// отсекаем родительские категории существующих в стеке вопросов
	if (!empty($exl_categories_id)) {
		foreach ($exl_categories_id as $key => $excluded_category) {
			if ( get_category_parent($excluded_category)==0 ) {
				$exl_cats[] = $excluded_category;
			}else{
				$exl_parent_cats[] = get_category_parent($excluded_category);
			}
		}
		if (!empty($exl_cats)) {
			$exl_cats = implode(",", $exl_cats);
			$query_where_no_cats = " AND id NOT IN (".$exl_cats.")";
		}else{
			$query_where_no_cats = "";
		}
		if (!empty($exl_parent_cats)) {
			$exl_parent_cats = implode(",", $exl_parent_cats);
			$query_where_no_parents = " AND p_id NOT IN (".$exl_parent_cats.")";
		}else{
			$query_where_no_parents = "";
		}		

	}else{
		$query_where_no_parents = "";
		$query_where_no_cats = "";
	}	

	$DB->query_exec("SELECT id FROM categories WHERE p_id <> 0".$query_where.$query_where_no_parents.$query_where_no_cats);
	$avaible_categories = $DB->fetch();
	foreach ($avaible_categories as $key => $value) {
		$return_avaible_categories[] = $value['id'];
	}

	return $return_avaible_categories;
}
function get_random_categories($avaibleSubCategories){
	global $DB;

	// выборка только из доступных подкатегорий
	if (!empty($avaibleSubCategories)) {
		$avaible_q_string = implode(",", $avaibleSubCategories);
		$query_where = " AND id IN (".$avaible_q_string.")";
	}else{
		$query_where = "";
	}	

	$DB->query_exec("SELECT id FROM (SELECT * FROM categories ORDER BY RAND()) AS subquery WHERE 1=1".$query_where."GROUP BY p_id");
	$avaible_categories = $DB->fetch();
	foreach ($avaible_categories as $key => $value) {
		$return_ids[] = $value['id'];
	}

	return $return_ids;
}
function get_random_questions($random_categories,$limit){
	global $DB;

	if ( is_array($random_categories) ) {
		$vozmozhnie_categories = implode(",", $random_categories);
	}

	$userGender_query = "";
	if ( $_SESSION['user']->sex == 'male' ) {
		$userGender_query = " AND gender <> 'female'";
	}else{
		$userGender_query = " AND gender <> 'male'";
	}

	$DB->query_exec("SELECT DISTINCT q_id FROM gameplay WHERE u_id = {$_SESSION['user']->id} AND closed = 1");
	$user_closed_questions_dump = $DB->fetch();
	foreach ($user_closed_questions_dump as $key => $u_c_q) {
		$user_closed_questions[] = $u_c_q['q_id'];
	}
	$user_closed_questions = implode(",", $user_closed_questions);

	$DB->query_exec("SELECT *
		FROM questions
		WHERE q_hard = 0 AND q_visibility = 1 AND
		q_category IN ({$vozmozhnie_categories}) AND
		id NOT IN ({$user_closed_questions}) AND ISNULL(parent_id)".$userGender_query."
		GROUP BY q_category
		LIMIT {$limit}");

    $random_questions = $DB->fetch();

    if ( count($random_questions) < $limit ) {
    	$DB->query_exec("SELECT * FROM questions WHERE q_hard = 0 AND q_visibility = 1 AND id NOT IN ({$user_closed_questions}) AND ISNULL(parent_id) ORDER BY RAND()");
    	$addition_random_questions = $DB->fetch();
    	$i_add = 0;
    	while ( count($random_questions) < $limit ) {
    		$random_questions[] = $addition_random_questions[$i_add];
    		$i_add++;
    	}
    }

    return $random_questions;
}




// обработка серии вопросов
	// проверяет запущена ли сессия или нет true - false 
	function seria_session($u_id,$qid){
		global $DB;
		
		$DB->query_exec("SELECT * FROM seria_session WHERE u_id = {$u_id} AND parent_q = {$qid} AND closed = 0");
		$res = $DB->fetch();
		return $res;
	}
	// запускает новую сессию серии вопросов
	function start_seria_session($ser_sessia){
		global $DB;

		$ser_sessia['questions'] = implode("|", $ser_sessia['questions']);

		$query = "INSERT INTO seria_session (`u_id`, `parent_q`, `current`, `questions`, `max_posible_points`)
				VALUES ({$ser_sessia['u_id']},{$ser_sessia['parent_q']},{$ser_sessia['current']},'{$ser_sessia['questions']}',{$ser_sessia['max_posible_points']})";

	    $DB->query_exec($query);
	}
	function seria_update_current($u_id,$pid,$qid){
		global $DB;

		$this_seria = seria_session($u_id,$pid);
		$this_seria = $this_seria[0];

		$this_seria['questions'] = explode("|", $this_seria['questions']);
		foreach ($this_seria['questions'] as $key => $current_q) {
			if ($current_q==$qid) {
				$current_id = $key;
			}
		}

		$DB->query_exec("UPDATE seria_session SET current = {$current_id} + 1 WHERE u_id = {$u_id} AND parent_q = {$pid}");
	}
	function reset_seria_current($u_id,$qid){
		global $DB;
		$DB->query_exec("UPDATE seria_session SET current = 0 WHERE u_id = {$u_id} AND parent_q = {$qid}");
	}
	function close_session($u_id,$qid){
		global $DB;
		$DB->query_exec("UPDATE seria_session SET closed = 1 WHERE u_id = {$u_id} AND parent_q = {$qid}");
	}
	function seria_update_score($u_id,$qid,$points){
		global $DB;
		$DB->query_exec("UPDATE seria_session SET sess_score = sess_score + {$points} WHERE u_id = {$u_id} AND parent_q = {$qid}");
	}
	function is_seria_opros($pid){
		global $DB;
		$DB->query_exec("SELECT opros FROM questions WHERE id = {$pid}");
		$opros_res = $DB->fetch();
		$opros_res = $opros_res[0]['opros'];
		return ($opros_res==1)?true:false;
	}
	function show_seria_current($uid,$pid){
		global $DB;
		$DB->query_exec("SELECT * FROM seria_session WHERE u_id = {$uid} AND parent_q = $pid");
		$current_session = $DB->fetch();
		$current_session = $current_session[0];
		// найдем к-во вопросов в серии
		if ( !empty($current_session) ) {
			$child_questions = explode("|", $current_session['questions']);
			$questions_quantity = count($child_questions);
			$current_question = $current_session['current'] + 1;
		} else {
			$current_question = 0;
			$DB->query_exec("SELECT COUNT(id) AS q FROM questions WHERE parent_id = $pid");
			$questions_quantity = $DB->fetch();
			$questions_quantity = $questions_quantity[0]['q'];
		}

		if ($questions_quantity!=0) {
			return $current_question."/".$questions_quantity;
		} else{
			return "";
		}
	}
	function seria_count_questions($uid,$pid){
		global $DB;
		$DB->query_exec("SELECT * FROM seria_session WHERE u_id = {$uid} AND parent_q = $pid");
		$current_session = $DB->fetch();
		$current_session = $current_session[0];
		// найдем к-во вопросов в серии
		if ( !empty($current_session) ) {
			$child_questions = explode("|", $current_session['questions']);
			$questions_quantity = count($child_questions);
		}
		return $questions_quantity;
	}




// question related functions
	// get question type by id
	function get_question_type($qid){
		global $DB;
		$DB->query_exec("SELECT q_type FROM questions WHERE id = {$qid}");
		$res = $DB->fetch();
		return $res[0]['q_type'];
	}
    function get_question_title($qid){
        global $DB;
        $DB->query_exec("SELECT q_title FROM questions WHERE id = {$qid}");
        $res = $DB->fetch();
        return $res[0]['q_title'];
    }
    function get_question_share($qid){
        global $DB;
        $DB->query_exec("SELECT q_share FROM questions WHERE id = {$qid}");
        $res = $DB->fetch();

        $share = $res[0]['q_share'];

        if ( empty($share) ) {
        	$share = "Я участвую в квесте от Familia, выполняю задания и зарабатываю баллы на подарки! Охота за сокровищами началась! #familia_offprice";
        }

        return $share;
    }
    // get answers
    function get_answers($q_id){
        global $DB;
        $DB->query_exec("SELECT * FROM answers WHERE q_id = {$q_id}");
        $res = $DB->fetch();

        return $res;
    }
    // get right answers
    function get_right_answers($qid,$qtype){
    	global $DB;
    	
    	if ( $qtype != 1 ) {
			$DB->query_exec("SELECT q_correct FROM questions WHERE id = {$qid}");
			$q_data = $DB->fetch();
			$q_data = $q_data[0];
			return $q_data;
    	} else {
    		return get_answers($qid);
    	}
    }
    // get final of the question
    function get_question_final($final_type,$q_id){
    	global $DB;
    	
		$DB->query_exec("SELECT final FROM finals WHERE final_type = '{$final_type}' AND q_id = {$q_id}");
		$q_data = $DB->fetch();
		$q_data = $q_data[0];
		return $q_data;
    }
    function final_check($q_id){
            global $DB;
            $DB->query_exec("SELECT * FROM finals WHERE q_id = {$q_id}");
            $res = $DB->fetch();

            return $res;
        }
    // get final of the question
    function get_question_score($q_id){
    	global $DB;
    	
		$DB->query_exec("SELECT q_score FROM questions WHERE id = {$q_id}");
		$q_data = $DB->fetch();
		$q_data = $q_data[0]['q_score'];
		return $q_data;
    }
    // get final of the question
    function get_user_total_score($user_id){
    	global $DB;
		$DB->query_exec("SELECT total_score FROM users WHERE id = '{$user_id}'");
		$total_score = $DB->fetch();
		$total_score = $total_score[0]['total_score'];
		return $total_score;
    }
    // get score for level
    function get_user_score_for_level($user_id){
    	global $DB;
		$DB->query_exec("SELECT score_for_level FROM users WHERE id = '{$user_id}'");
		$total_score = $DB->fetch();
		$total_score = $total_score[0]['score_for_level'];
		return $total_score;
    }
    // update score for share
    function points_for_share($data){
		global $DB;

		$DB->query_exec("SELECT shared FROM phpsession WHERE u_id = {$data['u_id']} ");
		$db_shared_empty = $DB->fetch();

		if ( empty( $db_shared_empty ) ) {
			$DB->query_exec("INSERT INTO phpsession (u_id) VALUES ( {$data['u_id']} ) ");
		}

		$db_shared_empty = $db_shared_empty[0]['shared'];

		if ( isset($_SESSION['user']->shared) ) { 
			if ( $_SESSION['user']->shared != $db_shared_empty ) {
				$DB->query_exec("UPDATE phpsession SET shared = {$_SESSION['user']->shared} WHERE u_id={$data['u_id']};");
			}
		}

		$DB->query_exec("SELECT shared FROM phpsession WHERE u_id = {$data['u_id']} ");
		$db_shared = $DB->fetch();
		$db_shared = $db_shared[0]['shared'];

		
		$_SESSION['user']->shared = $db_shared;



        if ($_SESSION['user']->shared > 0) {
            $points_to_skip = 0;
        } else {
            $points_to_skip = 15;
        }
        


		$u_total_score = get_user_total_score($data['u_id']);
		$u_level_score = get_user_score_for_level($data['u_id']);


		$new_total_score = $u_total_score + $points_to_skip;
		$new_level_score = $u_level_score + $points_to_skip;

		// записали в базу, обновили общее к-во баллов
		$DB->query_exec("UPDATE users SET total_score={$new_total_score}, score_for_level={$new_level_score} WHERE id={$data['u_id']};");

		if ( !empty($data['q_id']) ) {
			$DB->query_exec("INSERT INTO shared (u_id,q_id,provider,score) VALUES ( {$data['u_id']}, {$data['q_id']}, '{$data['provider']}', '{$points_to_skip}')");
		}
		if ( !empty($data['level']) ) {
			$DB->query_exec("INSERT INTO shared (u_id,level,provider,score) VALUES ( {$data['u_id']}, {$data['level']}, '{$data['provider']}', '{$points_to_skip}')");
		}

		update_user_level($data['u_id']);


    	$_SESSION['user']->shared++;
    	$DB->query_exec("UPDATE phpsession SET shared = {$_SESSION['user']->shared} WHERE u_id={$data['u_id']};");


    	$res['text'] = "баллы успешно начислены";

    	return json_decode($res);
    }


    // get user level
    function get_user_level($user_id){
    	global $DB;
		$DB->query_exec("SELECT user_level FROM users WHERE id = '{$user_id}'");
		$user_level = $DB->fetch();
		$user_level = $user_level[0]['user_level'];
		return $user_level;    	
    }
    // get game level
    function get_level($level_id){
    	global $DB;
		$DB->query_exec("SELECT * FROM game_levels WHERE game_levels.level = '{$level_id}'");
		$game_level = $DB->fetch();
		$game_level = $game_level[0];
		return $game_level;
    }
    // get user id
    function get_user_id($socialId){
    	global $DB;
		$DB->query_exec("SELECT id FROM users WHERE social_id = '{$socialId}'");
		$user_id = $DB->fetch();
		$user_id = $user_id[0]['id'];
		return $user_id;    	
    }    
    // update user level
    function update_user_level($user_id){
    	global $DB;
    	$user_points = get_user_score_for_level($user_id);
        $DB->query_exec("SELECT * FROM game_levels WHERE points <= {$user_points} ORDER BY LEVEL DESC LIMIT 1");
        $avaible_level = $DB->fetch();
        $avaible_level = $avaible_level[0]['level'];

		$DB->query_exec("UPDATE users SET user_level = {$avaible_level} WHERE id = {$user_id}");
		$user_level = get_user_level($user_id);
		$DB->query_exec("SELECT * FROM game_levels WHERE level = {$user_level}");
		$game_level = $DB->fetch();
		return $game_level[0];
    }
    // get user level
    function get_next_level_points($user_id){
    	global $DB;
    	$user_level = get_user_level($user_id);
    	$next_level = $user_level + 1;
		$DB->query_exec("SELECT points FROM game_levels WHERE level = '{$next_level}'");
		$next_level_points = $DB->fetch();
		$next_level_points = $next_level_points[0]['points'];
		return $next_level_points;    	
    }
    // get_last_closed_questions
    function get_last_closed_questions($u_id){
    	global $DB;
		$DB->query_exec("SELECT * FROM gameplay WHERE u_id = '{$u_id}' AND closed = 1 AND q_id IN (SELECT id FROM questions WHERE ISNULL(parent_id)) ORDER BY created DESC LIMIT 3");
		$last_closed_questions = $DB->fetch();
		return $last_closed_questions;
    }
    // check if question with manual check
    function is_manual($q_id){
    	global $DB;
		$DB->query_exec("SELECT * FROM questions WHERE id = {$q_id} AND manual = 1");
		$q_manual = $DB->fetch();
		if ( empty($q_manual) ) {
			return false;
		}else{
			return true;
		}

    }
    /**
	 * @param $q_id - question id
	 * @return bool
	 */
    function is_child_question($q_id){
    	global $DB;
		$DB->query_exec("SELECT * FROM questions WHERE id = {$q_id}");
		$q_manual = $DB->fetch();
		$q_manual = $q_manual[0];
		if ( $q_manual['parent_id'] === null ) {
			return false;
		}else{
			return $q_manual['parent_id'];
		}

    }



    function skip_answer($data){
    	global $DB;

    	$db_skipped = get_skipped_session($_SESSION['user']->id);

    	if ( isset($_SESSION['user']->skipped) ) {
    		if ($_SESSION['user']->skipped < 3) {
	    		$points_to_skip = 5;
    		}else{
	    		$points_to_skip = 10;
    		}

    		if( get_user_total_score($_SESSION['user']->id) >= $points_to_skip ){
    			$res['allow'] = true;
    			$res['text'] = "Вы действительно хотите пропустить задание за ".$points_to_skip." баллов?";
    		} else {
    			$res['allow'] = false;
    			$res['text'] = "У вас недостаточно баллов";
    		}
    	} else {
    		$points_to_skip = 5;
			if ( $db_skipped != null ) {
				if ( $db_skipped < 3 ) {
					$points_to_skip = 5;
				}else{
					$points_to_skip = 10;
				}
			}    		
    		if( get_user_total_score($_SESSION['user']->id) >= $points_to_skip ){
    			$res['allow'] = true;
    			$res['text'] = "Вы действительно хотите пропустить задание за ".$points_to_skip." баллов?";
    		} else {
    			$res['allow'] = false;
    			$res['text'] = "У вас недостаточно баллов" ;   			
    		}    		
    	}
    	
		return $res;
    }
    function skip_question($qid){
		global $DB;

		$DB->query_exec("SELECT skipped FROM phpsession WHERE u_id = {$_SESSION['user']->id} ");
		$db_skipped_empty = $DB->fetch();

		if ( empty( $db_skipped_empty ) ) {
			$DB->query_exec("INSERT INTO phpsession (u_id) VALUES ( {$_SESSION['user']->id} ) ");
		}

		$db_skipped_empty = $db_skipped_empty[0]['skipped'];

		if ( isset($_SESSION['user']->skipped) ) { 
			if ( $_SESSION['user']->skipped != $db_skipped_empty ) {
				$DB->query_exec("UPDATE phpsession SET skipped = {$_SESSION['user']->skipped} WHERE u_id={$_SESSION['user']->id};");
			}
		}

		$DB->query_exec("SELECT skipped FROM phpsession WHERE u_id = {$_SESSION['user']->id} ");
		$db_skipped = $DB->fetch();
		$db_skipped = $db_skipped[0]['skipped'];

		
		$_SESSION['user']->skipped = $db_skipped;
    	

		if ($_SESSION['user']->skipped < 3) {
    		$points_to_skip = 5;
		}else{
    		$points_to_skip = 10;
		}


    	$new_score = get_user_total_score($_SESSION['user']->id)-$points_to_skip;
    	$DB->query_exec("UPDATE users SET total_score={$new_score} WHERE id={$_SESSION['user']->id}");

    	$active_sessia = seria_session($_SESSION['user']->id,$qid['qid']);
    	
    	if ( !empty($active_sessia) ) {// если вопрос серия - закрыть надо все вопросы и сессию
    		$active_sessia = $active_sessia[0];
    		$inner_questions = explode('|',$active_sessia['questions']);
    		// закроем все внутренние вопросы
    		foreach ($inner_questions as $key => $iq_id) {
    			$gp_values = array($_SESSION['user']->id, $iq_id, "skipped", 0, 1 );
				update_user_gameplay($gp_values);
    		}
    		// закроем родительский вопрос
			$gp_values = array($_SESSION['user']->id, $qid['qid'], "skipped", 0, 1 );
			update_user_gameplay($gp_values);   
			// закроем сессию
			close_session($_SESSION['user']->id,$qid['qid']);

    	}else{// иначе, закрыть только один вопрос
	    	$gp_values = array($_SESSION['user']->id, $qid['qid'], "skipped", 0, 1 );
			update_user_gameplay($gp_values);    		
    	}

    	$_SESSION['user']->skipped++;
    	$DB->query_exec("UPDATE phpsession SET skipped = {$_SESSION['user']->skipped} WHERE u_id={$_SESSION['user']->id};");
    }

    function get_skipped_session($uid){
    	global $DB;
		$DB->query_exec("SELECT skipped FROM phpsession WHERE u_id = {$uid} ");
		$db_skipped = $DB->fetch();
		$db_skipped = $db_skipped[0]['skipped'];
		return $db_skipped;
    }

// category related functions
    function get_category_parent($catid){
    	global $DB;
		$DB->query_exec("SELECT p_id FROM categories WHERE id = '{$catid}'");
		$parent_cat_id = $DB->fetch();
		$parent_cat_id = $parent_cat_id[0]['p_id'];
		return $parent_cat_id;
    }

// render functions
	// render question function
	function render_question_type($qid,$q_type){
		global $DB;

		if ( $q_type == 4 ) {
			
		} else {
			$DB->query_exec("SELECT * FROM questions WHERE id = {$qid}");
			$q_data = $DB->fetch();
			$q_data = $q_data[0];

			ob_start();
			require 'tmpl/qtypes/type'.$q_type.'.php';
			$res = ob_get_contents();
			ob_end_clean();			
		}



		return $res;
	}

    // render seria answer for ajax
    function render_seria_popup($data){
	    global $DB;

	    ob_start();
		require 'tmpl/qtypes/final_'.$data['final_type'].'.php';
		$res = ob_get_contents();
		ob_end_clean();	

		return $res;
    }
    function render_skip_answer($data){
	    global $DB;

	    ob_start();
		require 'tmpl/qtypes/skip_answer.php';
		$res = ob_get_contents();
		ob_end_clean();	

		return $res;
    }


    function help_page($data){
    	return;
    }
    function send_ask_us($data){
    	require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/phpmailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		$mail->CharSet  = 'UTF-8';
		$mail->isSMTP();                             	// Set mailer to use SMTP
		$mail->Host     = 'smtp.timeweb.ru';         	// Specify main and backup SMTP servers
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;                      	// Enable SMTP authentication
		$mail->Port = 465;                      	// TCP port to connect to

		$mail->Username = 'info@adventurefamil.ru';         	// SMTP username
		$mail->Password = 'i2h9XAbk';                	// SMTP password

		$mail->setFrom('info@adventurefamil.ru', 'adventurefamil');	// от кого в заголовке письма
		$mail->isHTML(true);                         	// Set email format to HTML

		$mail->addAddress('adventurefamil@gmail.com');	// КТО ПОЛУЧИТ ПИСЬМО
		$mail->Subject = 'Вопрос с игровой системы';

		require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'tmpl/mail/admin.php';
		### если письмо НЕ ОТПРАВЛЕНО:
		if(!$mail->send()) {
		    $data["mail_error"] = 'Message could not be sent.';
		    $data["mail_error"] .= 'Mailer Error: ' . $mail->ErrorInfo;
		### если письмо ОТПРАВЛЕНО
		} else {
		    $data["mail_error"] = 'Message has been sent';
		}

		$data = ' <h3 class="text-center">Спасибо, ваше сообщение отправлено</h3> ';

		return $data;
    }
    function prize_request($data){
    	require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/phpmailer/PHPMailerAutoload.php';

		$mail = new PHPMailer;

		$mail->CharSet  = 'UTF-8';
		$mail->isSMTP();                             	// Set mailer to use SMTP
		$mail->Host     = 'smtp.timeweb.ru';         	// Specify main and backup SMTP servers
		$mail->SMTPSecure = 'ssl';
		$mail->SMTPAuth = true;                      	// Enable SMTP authentication
		$mail->Port = 465;                      	// TCP port to connect to

		$mail->Username = 'info@adventurefamil.ru';         	// SMTP username
		$mail->Password = 'i2h9XAbk';                	// SMTP password

		$mail->setFrom('info@adventurefamil.ru', 'adventurefamil');	// от кого в заголовке письма
		$mail->isHTML(true);                         	// Set email format to HTML

		$mail->addAddress('adventurefamil@gmail.com');	// КТО ПОЛУЧИТ ПИСЬМО
		$mail->Subject = 'Запрос на получение сертификата';

		require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'tmpl/mail/prize.php';
		### если письмо НЕ ОТПРАВЛЕНО:
		if(!$mail->send()) {
		    $data["mail_error"] = 'Message could not be sent.';
		    $data["mail_error"] .= 'Mailer Error: ' . $mail->ErrorInfo;
		### если письмо ОТПРАВЛЕНО
		} else {
		    $data["mail_error"] = 'Message has been sent';
		}

		$_SESSION['user']->cert_no = null;
		unset($_SESSION['user']->cert_no);

		$data = ' <h3 class="text-center">Спасибо, ваше сообщение отправлено</h3><br><p class="text-center">Сертификат будет выслан после модерации. Модерация может занять до суток.</p>';

		return $data;
    }

// PRIZES RELATED FUNCTIONS
    function check_prize($data){
    	global $DB;
    	extract($data);
    	// $prize_type
    	// $u_id

    	// get user tottal score
    	$user_score = get_user_total_score($u_id);
    	// get prize price
    	$prize_type = substr($prize_type, -1);
        $prize_price = get_prize_type_data($prize_type);
        /**
         * $prize_price['cert_label']
         * $prize_price['cert_text']
         * $prize_price['cert_price']
         */
        if ( $user_score >= $prize_price['cert_price'] ) {
        	$tpl_data['id'] = $prize_price['id'];
        	$tpl_data['title'] = $prize_price['cert_label'];
        	$tpl_data['text'] = $prize_price['cert_text'];
        	$tpl_data['price'] = $prize_price['cert_price'];
        	$tpl_data['allow'] = true;
        } else {
        	$tpl_data['text'] = "У вас недостаточно баллов для получения этого приза. Играйте дальше)";
        	$tpl_data['price'] = $prize_price['cert_price'];
        	$tpl_data['allow'] = false;
        }

	    ob_start();
		require 'tmpl/qtypes/check_prize.php';
		$res = ob_get_contents();
		ob_end_clean();	

		return $res;
    }
    function get_prize($request){
		global $DB;
		$res['page'] = "getPrizePage";
		$u_id = $_SESSION['user']->id;

		$p_type = $request['pt'];
		$user_score = get_user_total_score($u_id);
		$prize_price = get_prize_type_data($p_type);

		### если у пользователя достаточно балов
		### выдадим ему сертификат
        if ( $user_score >= $prize_price['cert_price'] ) {
        	/**
        	 * получаем из базы по типу приза номер сертификата
        	 * и запизываем id пользователя, которому уходит сертификат
        	 * списываем баллы с пользователя
        	 * генерируем картинку
        	 * выводим картинку
        	 */
        	if ( $p_type != 1 ) {
				$DB->query_exec("UPDATE certs SET u_id={$u_id} WHERE cert_type = {$p_type} AND u_id = 0 LIMIT 1");
	        	$cert = get_cert($p_type, $u_id);
	        	$cert_no = $cert['code'];
        	}else{
        		$cert_no = "9914887660015";
        		$res['img']='/images/sertificate/new/9914887660015.jpg';
        	}

        	if ( check_ean13($cert_no) === true ) {
        		### списываем баллы с пользователя и ставим его id напротив сертификата
        		$new_score = $user_score - $prize_price['cert_price'];
        		
		    	$DB->query_exec("UPDATE users SET total_score={$new_score} WHERE id={$u_id}");

		    	if ( $p_type != 1 ) {
	        		
		    	} else {
		    		$DB->query_exec("INSERT INTO certs (code,cert_type,u_id) VALUES ('{$cert_no}',1,'{$u_id}')");
		    	}

        		### генерируем штрих-код
        		// generate_barcode($cert_no);
        		### получаем картинку
        		// $cert_img = generate_cert_img($cert_no);
        		// $res['img']=$cert_img;
        		$res['text']="предьявите этот сертификат в магазине Familia";
                $_SESSION['user']->cert_no = $cert_no;
        	}
        	

        } else {
        	$res['text']="";
        }

        return $res;

    }
    function get_prize_type_data($prize_type){
    	global $DB;

    	$DB->query_exec("SELECT * FROM certs_types WHERE id = {$prize_type}");
		$cert_price = $DB->fetch();
		$cert_price = $cert_price[0];

		return $cert_price;
    }
    function get_cert($type,$uid=0){
    	global $DB;

    	// $DB->query_exec("SELECT * FROM certs WHERE cert_type = {$type} AND u_id = {$uid}");
    	$DB->query_exec("SELECT * FROM certs WHERE cert_type = {$type} AND u_id = {$uid} ORDER BY id DESC LIMIT 1");
		$cert = $DB->fetch();
		$cert = $cert[0];

		return $cert;
    }
    function get_cert_qtity($cert_type){
    	global $DB;

    	$DB->query_exec("SELECT * FROM certs WHERE cert_type = {$cert_type} AND u_id = 0");
		$cert = $DB->fetch();
		$cert = count($cert);


		return $cert;
    }


    function generate_barcode($code){
		require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/php-barcode.php');

		$code_ean13=$code;                                                                        // 13-символьный код для штрих-кода

		$im_ean13=imagecreatetruecolor(320, 150);                                                         // Генерируем картинку 320*150
		$black  = ImageColorAllocate($im_ean13,0x00,0x00,0x00);                                           // Указываем цвет черный
		$white  = ImageColorAllocate($im_ean13,0xff,0xff,0xff);                                           // Указываем цвет белый
		imagefilledrectangle($im_ean13, 0, 0, 320, 150, $white);                                          // Заливаем картинку белым цветом
		$data = Barcode::gd($im_ean13, $black, 160, 60, 0, "ean13", $code_ean13, 3, 100);                 // Генерируем и накладываем на картинку штрих-код с указанием координат и размера шрифта
		$font=rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'fonts/roboto.ttf';                           // Загружаем шрифт для подписи числа цифрами под штрих-кодом
		$xt=90;	$yt=130; $fontSize=15; $angle=0;                                                          // координаты, размер шрифта, поворот надписи под штрих-кодом
		imagettftext($im_ean13, $fontSize, $angle, $xt, $yt, $black, $font, $data['hri']);                // Рисуем надпись на картинке
		$file_ean13 = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/barcodes/'.$code_ean13.'.png';// Указываем имя нового файла
		imagepng ($im_ean13, $file_ean13);                                                                // Создаем сгенерированную картинку в виде файла
		imagedestroy($im_ean13);                                                                          // очищаемся
    }
    function generate_cert_img($code){
    	global $DB;

    	$DB->query_exec("SELECT cert_type FROM certs WHERE code = {$code}");
		$pt = $DB->fetch();
		$pt = $pt[0]['cert_type'];
		$pt_data = get_prize_type_data($pt);

		require_once(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/pic.php');

		switch ($pt) {
			case '1':
				$ttfImg = new ttfTextOnImage(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/design/Sertificate_7.jpg');        // Создаем объект картинки на базе шаблона сертификата
				break;
			case '2':
				$ttfImg = new ttfTextOnImage(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/design/Sertificate_500.jpg');        // Создаем объект картинки на базе шаблона сертификата
				break;
			case '3':
				$ttfImg = new ttfTextOnImage(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/design/Sertificate_1000.jpg');        // Создаем объект картинки на базе шаблона сертификата
				break;
			case '4':
				$ttfImg = new ttfTextOnImage(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/design/Sertificate_1500.jpg');        // Создаем объект картинки на базе шаблона сертификата
				break;
			
			default:
				# code...
				break;
		}

		

		$font=rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'fonts/roboto.ttf';
		// Если нужна какая-то надпись
		// $text1 = $pt_data['cert_label'];
		#	$ttfImg->setFont('pic/roboto.ttf', 28, "#6b1d73", 1);       // Устанавливаем параметры шрифта надписи (шрифт, размер, цвет, прозрачность)
		#	$ttfImg->writeTextCenter (126, 840, $text1, 1240, 0);       // Пишем надпись $text1 (по центру) (X0, Y0, надпись, X1, Y1) (для того, чтобы центровать)

		// Пример
			$d1 = '';
			$ttfImg->setFont($font, 32, "#6b1d73", 1);
			$ttfImg->writeText(570, 975, $d1);                         // Пишем надпись $d1 (базовый вариант, выравнивание по левой стороне)

		$sert_name = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/sertificate/new/JglEMbgwtp/'.$code.'.jpg';           // имя файла, где будет сохранена окончательная картинка
		$ttfImg->output($sert_name);                                    											   // Сохраняем картинку в файл (готовый сертификат в виде отдельного файла

		$file = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/sertificate/new/JglEMbgwtp/'.$code.'.jpg';           // имя файла, где будет сохранена окончательная картинка
		// Далее идет мутный код не с моими комментариями
		$img_path=$sert_name;
        $file_ean13 = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'images/barcodes/'.$code.'.png';
		$wi = $file_ean13;                                              //тут хранится путь к накладываемому изображению, в формате .png
		$img = imagecreatefromjpeg($img_path);                          //создаем исходное изображение
		$arwater_img = getimagesize($img_path);                         //узнаем размер переданного изображения, чтобы правильно рассчитать координаты наложения
		$water_width = $arwater_img[0];                                 //ширина исходного изображения
		$water_height = $arwater_img[1];                                //высота исходного изображения
		$water_img_type = $arwater_img[2];
		$water_img_type = $arwater_img[$water_img_type-1];
		$water_img_size = $arwater_img[3];
		$water_img = imagecreatefrompng($wi);                           //создаем водный знак
		$water_size = getimagesize($wi);                                //узнаем размеры водного знака, чтобы правильно выполнить наложение
		$logo_h = $water_size[1];                                       //высота водного знака
		$logo_w = $water_size[0];                                       //ширинаа водного знака
		imagecopy ($img, $water_img, 436, 1330, 0, 0, $logo_w, $logo_h);//накладываем водный знак на изображение по заданным координатам.
		imagejpeg($img, $file, '100');  
		imagedestroy($img);


		return '/images/sertificate/new/JglEMbgwtp/'.$code.'.jpg';
    }
// end PRIZES RELATED FUNCTIONS

function first_n_arr_el($arr,$n){
	for ($i=0; $i < $n; $i++) { 
		$res_arr[] = $arr[$i];
	}
	return $res_arr;
}
function plural_form($number=0, $after) {
    if ( empty($number) ) {
    	echo "баллов";
    }else{
	    $cases = array (2, 0, 1, 1, 1, 2);
	    echo $after[ ($number%100>4 && $number%100<20)? 2: $cases[min($number%10, 5)] ];
    }
}





function check_ean13($code){

	$error = '';
	if (strlen($code) != 13)
		$error.="Код не из 13 символов<br>";
	else
	{
		$c1=($code[1]+$code[3]+$code[5]+$code[7]+$code[9]+$code[11])*3;
		$c2=$code[0]+$code[2]+$code[4]+$code[6]+$code[8]+$code[10];
		$c3=($c1+$c2)%10;
		if (((10-$c3)%10) != $code[12]) $error.='Проверьте номер дисконтной карты, какая-то цифра неправильная.<br>';
	}
	if (empty($error)){
		return true;
	} else {
		return false;
	}
}
function generate_ean13(){

	$nine_digit = sprintf("%09d",rand(1,999999999));

	$code = "9911".$nine_digit;

	if ( !check_ean13($code) ){
		generate_ean13();
	} else {
		return $code;
	}


}


function warning($u_id){
	global $DB;

/*	$DB->query_exec("SELECT C.code, C.u_id, C.cert_type, U.name, U.email, U.social_page, U.birthday
					FROM certs C
					LEFT JOIN users U ON C.u_id = U.id
					WHERE u_id NOT IN (
					SELECT id
					FROM users
					WHERE id > 4500 AND user_level >= 8) AND u_id != 2666 AND u_id != 0 AND u_id = {$u_id}");*/

	$DB->query_exec("SELECT created FROM gameplay WHERE u_id = {$u_id} ORDER BY created ASC LIMIT 1");
	$bedniy_user = $DB->fetch();
	$bedniy_user = $bedniy_user[0]['created'];

	if ( !empty($bedniy_user) ){
		$warn = "В связи с действиями злоумышленников все сертификаты, выданные с 16.10 по 18:00 19.10 были заблокированы. Приносим Вам свои извинения! Пожалуйста, обратитесь в форму обратной связи в разделе \"Помощь\" для получения нового сертификата.";
	}

	return $warn;

}



?>