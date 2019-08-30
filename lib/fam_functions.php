<?php
include(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'admin/classes/DBclass.php');

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
// вывод формы авторизации
function view_login($path, $data = null){
	if ($data){
		extract($data);
	}

	$path = $path.'.tpl.php';
	
	include "tmpl/login.php";
}
// авторизация
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
// получить данные пользователя для меню менеджера (выход)
function get_user_data($user_id){
	global $DB;

	$DB->query_exec("SELECT * FROM managers WHERE id = {$user_id}");
	$res = $DB->fetch();
	$data = $res[0];
	return $data;
}

// вывести все вопросы в таблице
function get_questions($where){
	global $DB;
	$res['page'] = "Вопросы";
	if (!empty($where)) {

		$sql_cond = "WHERE 1=1";

		if ( !empty( $where['id'] ) ) {
			$sql_cond .= " AND id = {$where['id']}";
		}
		if ( !empty($where['q_title']) && $where['q_title'] != "" ) {
			$sql_cond .= " AND q_title LIKE '%{$where['q_title']}%'";
		}
		if ( !empty($where['sys_name']) && $where['sys_name'] != "" ) {
			$sql_cond .= " AND sys_name LIKE '%{$where['sys_name']}%'";
		}
		if ( !empty($where['q_visibility']) && $where['q_visibility'] != "" ) {
			$sql_cond .= " AND q_visibility = {$where['q_visibility']}";
		}
		if ( !empty($where['q_hard']) && $where['q_hard'] != "" ) {
			$sql_cond .= " AND q_hard = {$where['q_hard']}";
		}
		if ( !empty($where['q_nomer']) && $where['q_nomer'] != "" ) {
			$sql_cond .= " AND q_nomer LIKE '%{$where['q_nomer']}%'";
		}
		if ( !empty($where['q_category']) && $where['q_category'] != "" ) {
			$sql_cond .= " AND q_category LIKE '%{$where['q_category']}%'";
		}
		if ( !empty($where['q_type']) && $where['q_type'] != "" ) {
			$sql_cond .= " AND q_type = '{$where['q_type']}'";
		}
		
		$DB->query_exec("SELECT * FROM questions ".$sql_cond);
	} else {
		$DB->query_exec("SELECT * FROM questions");
	}    

	// нужно получить все вопросы. Тут возможно через ajax надо будет
	
	$res['questions'] = $DB->fetch();

	return $res;
}
	// добавить новый вопрос
	function add_question($where){
		global $DB;
		$res['page'] = "Новый вопрос";

		$DB->query_exec("INSERT INTO questions VALUES ()");
		$res['q_id'] = $DB->GetLastID();

		return json_encode($res);

	}
	// редактировать новый вопрос
	function edit_question($request){
		global $DB;
		$res['page'] = "Редактировать вопрос";

		$DB->query_exec("SELECT * FROM questions WHERE id = {$request['qid']}");
		$res = $DB->fetch();

		return $res;

	}
	// удалить вопрос
	function del_question($qid){
		global $DB;

		$DB->query_exec("DELETE FROM questions WHERE id = {$qid}");

		$DB->query_exec("UPDATE questions SET parent_id=NULL WHERE  parent_id={$qid}");

		header("Location:/admin/index.php?action=questions");
	}
	// сохранить вопрос
	function save_question($data){
		global $DB;
		extract($data);

		$q_title = quotes_ecran($question['name']);
		$q_sys_name = $question['sys_name'];
		$q_visibility = isset($question['q_visibility']) ? 1 : 0;
		$q_hard = isset($question['q_hard']) ? 1 : 0;
		$opros = isset($question['opros']) ? 1 : 0;
		$manual = isset($question['manual']) ? 1 : 0;
		$q_level = isset($question['q_level']) ? 1 : 2;
		$q_nomer = empty($question['q_nomer']) ? 0 : $question['q_nomer'];
		$q_category = empty($question['q_category']) ? 0 : $question['q_category'];
		$q_type = empty($question['q_type']) ? 0 : $question['q_type'];
		$q_correct = empty($question['correct']) ? "" : $question['correct'];
		$q_text = quotes_ecran($question['q_text']);
		$q_score = $question['q_score'];
		$gender = $question['gender'];
		$q_share = quotes_ecran($question['q_share']);
		$seria_button = isset($question['seria_button']) ? $question['seria_button'] : "";

		if (!isset($question['session'])) {
			### ОТВЕТЫ
			$existed_answers = answers_check($q_id);
			if (empty($existed_answers)) {
				if (isset($question['answer']) && is_array($question['answer'])) {
					foreach ($question['answer'] as $key => $answer) {
						$answer = quotes_ecran($answer);
						$DB->query_exec("INSERT INTO answers (q_id,answer,q_type) VALUES ({$q_id},'{$answer}',{$q_type})");
					}
				} elseif (isset($question['image']) && is_array($question['image'])) {
					foreach ($question['image'] as $key => $answer) {
						$DB->query_exec("INSERT INTO answers (q_id,answer,q_type) VALUES ({$q_id},'{$answer["name"]}',{$q_type})");
					}
				}
			} else {
				$DB->query_exec("DELETE FROM `answers` WHERE  `q_id`={$q_id};");
				if (isset($question['answer']) && is_array($question['answer'])) {
					foreach ($question['answer'] as $key => $answer) {
						$answer = quotes_ecran($answer);
						$DB->query_exec("INSERT INTO answers (q_id,answer,q_type) VALUES ({$q_id},'{$answer}',{$q_type})");
					}
				} elseif (isset($question['image']) && is_array($question['image'])) {
					foreach ($question['image'] as $key => $answer) {
						$DB->query_exec("INSERT INTO answers (q_id,answer,q_type) VALUES ({$q_id},'{$answer["name"]}',{$q_type})");
					}
				}
			}

			// ПРАВИЛЬНЫЕ ОТВЕТЫ
			$answers_write_to_question = answers_check($q_id);
			$q_answer = '';
			foreach ($answers_write_to_question as $key => $answer) {
				$q_answer .= $answer['id'] . "|";
			}
			$q_answer = substr($q_answer, 0, -1);


			### ФИНАЛЫ
			// проверим есть ли уже в базе финалы?
			$existed_finals = final_check($q_id);
			// если НЕТУ то вставим новую запись
			if (empty($existed_finals)) {
				if (isset($question['final']) && is_array($question['final'])) {
					foreach ($question['final'] as $final_type => $final) {
						foreach ($final as $key => $value) {
							$final[$key] = htmlspecialchars($final[$key]);
							$final[$key] = quotes_ecran($final[$key]);
						}
						$w_final = serialize($final);
						$DB->query_exec("INSERT INTO finals (q_id,final,final_type) VALUES ({$q_id},'{$w_final}','{$final_type}')");
					}
				}
			} else {
				// если уже есть, удалим существующие, и вставим заново
				$DB->query_exec("DELETE FROM `finals` WHERE  `q_id`={$q_id};");
				if (isset($question['final']) && is_array($question['final'])) {
					foreach ($question['final'] as $final_type => $final) {
						foreach ($final as $key => $value) {
							$final[$key] = htmlspecialchars($final[$key]);
							$final[$key] = quotes_ecran($final[$key]);
						}
						$w_final = serialize($final);

						if (isset($question['f_score'][$final_type])&&!empty($question['f_score'][$final_type])) {
							$DB->query_exec("INSERT INTO finals (q_id,final,final_type,final_points) VALUES ({$q_id},'{$w_final}','{$final_type}',{$question['f_score'][$final_type]})");
						} else {
							$DB->query_exec("INSERT INTO finals (q_id,final,final_type) VALUES ({$q_id},'{$w_final}','{$final_type}')");
						}
					}
				}
			}

			$finals_write_to_question = final_check($q_id);
			$q_final = '';
			foreach ($finals_write_to_question as $key => $answer) {
				$q_final .= $answer['id'] . "|";
			}
			$q_final = substr($q_final, 0, -1);


			$DB->query_exec("UPDATE `questions` SET 
								q_title = '{$q_title}',
								sys_name = '{$q_sys_name}',
								q_visibility = {$q_visibility},
								q_hard = {$q_hard},
								q_nomer = {$q_nomer},
								q_category = {$q_category},
								q_type = {$q_type},
								q_level = {$q_level},
								q_text = '{$q_text}',
								q_answer = '{$q_answer}',
								q_final = '{$q_final}',
								seria_button = '{$seria_button}',
								q_score = '{$q_score}',
								gender = '{$gender}',
								q_share = '{$q_share}',
								opros = '{$opros}',
								manual = '{$manual}',
								q_correct = '{$q_correct}'
								WHERE id = '{$q_id}'");
		} else {

			if (is_array($question['session'])) {
				foreach ($question['session'] as $key => $child_id) {
					$DB->query_exec("UPDATE `questions` SET parent_id = {$q_id} WHERE id = {$child_id}");
				}
			}
		}

		$res['res'] = $q_id;

		return json_encode($res);
	}
		// проверить существуют ли ответы на этапе редактирования
		function answers_check($q_id){
			global $DB;
			$DB->query_exec("SELECT * FROM answers WHERE q_id = {$q_id}");
			$res = $DB->fetch();

			return $res;
		}
		// удалить ответ
		function del_answer($a_id){
			global $DB;
			$DB->query_exec("DELETE FROM answers WHERE id = {$a_id}");

			$res['res'] = $a_id;

			return json_encode($res);
		}
		// проверить существуют ли финалы на этапе редактирования
		function final_check($q_id){
			global $DB;
			$DB->query_exec("SELECT * FROM finals WHERE q_id = {$q_id}");
			$res = $DB->fetch();

			return $res;
		}
		function get_question_childs($qid){
			global $DB;
			$DB->query_exec("SELECT * FROM questions WHERE parent_id = {$qid}");
			$res = $DB->fetch();

			return $res;
		}
	

// вывести уровни в таблице
function get_game_levels(){
	global $DB;
	$res['page'] = "Уровни";

	// нужно получить все вопросы. Тут возможно через ajax надо будет
	$DB->query_exec("SELECT * FROM game_levels");
	$res['game_levels'] = $DB->fetch();

	return $res;
}
	// добавить новый уровень
	function add_game_level($where){
		global $DB;
		$res['page'] = "Новый уровень";

		$DB->query_exec("INSERT INTO game_levels VALUES ()");
		$res['q_id'] = $DB->GetLastID();

		return json_encode($res);

	}
	// редактировать новый уровень
	function edit_game_level($request){
		global $DB;
		$res['page'] = "Редактировать уровень";

		$DB->query_exec("SELECT * FROM game_levels WHERE id = {$request['qid']}");
		$res = $DB->fetch();

		return $res;

	}
	// сохранить вопрос
	function save_game_level($data){
		global $DB;
		extract($data);

		$g_level = $game_level['level'];
		$g_points = $game_level['points'];
		$g_name = $game_level['name'];
		$g_greetings = $game_level['greetings'];
		$g_text = $game_level['l_text'];
		$image = $game_level['image'];


		$DB->query_exec("UPDATE `game_levels` SET 
							level = {$g_level},
							points = {$g_points},
							name = '{$g_name}',
							greetings = '{$g_greetings}',
							l_text = '{$g_text}',
							image = '{$image}'
							WHERE id = '{$q_id}'");


		$res['res'] = $q_id;

		return json_encode($res);
	}
	// удалить ответ
	function del_game_level($l_id){
		global $DB;
		$DB->query_exec("DELETE FROM game_levels WHERE id = {$l_id}");

		header("Location:/admin/index.php?action=game_levels");
	}

/*=============================================
=            КАТЕГОРИИ            =
=============================================*/

// вывести категории в таблице
function get_q_categories(){
	global $DB;
	$res['page'] = "Категории";

	// нужно получить все вопросы. Тут возможно через ajax надо будет
	$DB->query_exec("SELECT * FROM categories");
	$res['categories'] = $DB->fetch();

	return $res;
}
	// добавить категорию
	function add_q_category($where){
		global $DB;
		$res['page'] = "Новая категория";

		$DB->query_exec("INSERT INTO categories VALUES ()");
		$res['q_id'] = $DB->GetLastID();

		return json_encode($res);

	}
	// редактировать категорию
	function edit_q_category($request){
		global $DB;
		$res['page'] = "Редактировать категорию";

		$DB->query_exec("SELECT * FROM categories WHERE id = {$request['qid']}");
		$res = $DB->fetch();

		return $res;

	}
	// сохранить вопрос
	function save_q_category($data){
		global $DB;
		extract($data);

		$label = $category['label'];
		$p_id = !empty($category['p_id'])?$category['p_id']:0;

		$DB->query_exec("UPDATE `categories` SET 
							label = '{$label}',
							p_id = {$p_id}
							WHERE id = '{$q_id}'");


		$res['res'] = $q_id;

		return json_encode($res);
	}
	// удалить ответ
	function del_q_category($l_id){
		global $DB;
		$DB->query_exec("DELETE FROM categories WHERE id = {$l_id}");

		header("Location:/admin/index.php?action=q_categories");
	}

/*=====  End of КАТЕГОРИИ  ======*/

/*====================================
=            ПОЛЬЗОВАТЕЛИ            =
====================================*/
function get_users($where='',$order=""){
	global $DB;
	$res['page'] = "Пользователи";
    if (!empty($where)) {

        $sql_cond = "WHERE 1=1";

        if ( !empty( $where['id'] ) ) {
            $sql_cond .= " AND id = {$where['id']}";
        }
        if ( !empty($where['name']) && $where['name'] != "" ) {
            $sql_cond .= " AND name LIKE '%{$where['name']}%'";
        }
        if ( !empty($where['email']) && $where['email'] != "" ) {
            $sql_cond .= " AND email LIKE '%{$where['email']}%'";
        }
        if ( !empty($where['sex']) && $where['sex'] != "" ) {
            $sql_cond .= " AND sex LIKE '%{$where['sex']}%'";
        }
        if ( !empty($where['social_page']) && $where['social_page'] != "" ) {
            $sql_cond .= " AND social_page LIKE '%{$where['social_page']}%'";
        }
        if ( !empty($where['user_level']) && $where['user_level'] != "" ) {
            $sql_cond .= " AND user_level = {$where['user_level']}";
        }
        if ( !empty($where['score_for_level']) && $where['score_for_level'] != "" ) {
            $sql_cond .= " AND score_for_level = {$where['score_for_level']}";
        }
        if ( !empty($where['total_score']) && $where['total_score'] != "" ) {
            $sql_cond .= " AND total_score = '{$where['total_score']}'";
        }

        if (empty($order)) {
            $order = "";
        }

        $DB->query_exec("SELECT * FROM users ".$sql_cond.' '.$order);
    } else {

        if (empty($order)) {
            $order = "";
        }

        $DB->query_exec("SELECT * FROM users ".$order);
    }



	// нужно получить все вопросы. Тут возможно через ajax надо будет
	$res['users'] = $DB->fetch();

	return $res;
}
	// редактировать пользователя
	function edit_user($request){
		global $DB;
		$res['page'] = "Редактировать пользователя";

		$DB->query_exec("SELECT * FROM users WHERE id = {$request['uid']}");
		$res = $DB->fetch();

		return $res;

	}
	// сохранить пользователя
	function save_user($data){
		global $DB;
		extract($data);

		$name = $user['name'];
		$email = !empty($user['email'])?$user['email']:'';
		$total_score = $user['total_score'];

		$DB->query_exec("UPDATE `users` SET 
							name = '{$name}',
							email = '{$email}',
							total_score = {$total_score}
							WHERE id = '{$u_id}'");


		$res['res'] = "Пользователь ".$name;

		return json_encode($res);
	}
    function get_user_total_score($user_id){
    	global $DB;
		$DB->query_exec("SELECT total_score FROM users WHERE id = '{$user_id}'");
		$total_score = $DB->fetch();
		$total_score = $total_score[0]['total_score'];
		return $total_score;
    }
    function get_question_score($q_id){
    	global $DB;
    	
		$DB->query_exec("SELECT q_score FROM questions WHERE id = {$q_id}");
		$q_data = $DB->fetch();
		$q_data = $q_data[0]['q_score'];
		return $q_data;
    }
    function get_user_certs($request){
    	global $DB;
    	
		$DB->query_exec("SELECT * FROM certs WHERE u_id = {$request['uid']}");
		$u_serts = $DB->fetch();

		return $u_serts;
    }
    function show_user_gameplay($request){
    	global $DB;
    	
		$DB->query_exec("SELECT G.*,Q.q_title FROM gameplay G left join questions Q on G.q_id = Q.id WHERE u_id = {$request['uid']}");
		$u_serts = $DB->fetch();

		return $u_serts;
    }
    function show_user_share($request){
    	global $DB;
    	
		$DB->query_exec("SELECT * FROM shared WHERE u_id = {$request['uid']}");
		$u_shared = $DB->fetch();

		return $u_shared;
    }

/*=====  End of ПОЛЬЗОВАТЕЛИ  ======*/

/*==================================================
=            ВОПРОСЫ С РУЧНОЙ ПРОВЕРКОЙ            =
==================================================*/

function get_manual_check($data){
	global $DB;
	$res['page'] = "Вопросы с ручной проверкой";

	// нужно получить все вопросы. Тут возможно через ajax надо будет
	$DB -> query_exec("SELECT GP.*,U.name,U.total_score FROM gameplay GP LEFT JOIN users U ON U.id = GP.u_id WHERE GP.closed = 1 AND GP.answer != 'skipped' AND GP.q_id IN (SELECT id FROM questions WHERE manual = 1)");
	$res['questions'] = $DB -> fetch();

	return $res;	
}
function admin_add_points($data){
	global $DB;
	extract($data);
	
	$u_total_score = get_user_total_score($uid);
	$new_total_score = $u_total_score + get_question_score($qid);
	$DB->query_exec("UPDATE users SET total_score={$new_total_score} WHERE id={$uid};");
	$DB->query_exec("UPDATE gameplay SET m_approved=1 WHERE id={$qid};");

	return $res;	
}

/*=====  End of ВОПРОСЫ С РУЧНОЙ ПРОВЕРКОЙ  ======*/

/*=============================
=            CERTS            =
=============================*/

function get_certs($request){
	global $DB;
	$res['page'] = "Сертификаты";
	$DB -> query_exec("SELECT CT.*,COUNT(C.code) as quantity FROM certs_types CT left join certs C on CT.id = C.cert_type WHERE u_id != 0 group by CT.id");
	$res['certs'] = $DB -> fetch();

	return $res;
}

/*=====  End of CERTS  ======*/





// получить данные вопроса по id
	// 
	function get_question_label($table,$id){
		global $DB;

		$DB->query_exec("SELECT label FROM {$table} WHERE id = {$id}");
		$label = $DB->fetch();

		return $label[0]['label'];
	}


// генерация html
	// selects
	function gen_select($table,$post_name,$form_filter=""){
		global $DB;

		$DB->query_exec("SELECT * FROM {$table}");
		$records = $DB->fetch();


		$select = '<select class="table-group-action-input form-control input-medium '.$form_filter.'" name="'.$post_name.'"><option value=""></option>';
		foreach ($records as $value) {
			$select .= '<option value="'.$value['id'].'">'.$value['label'].'</option>';
		}
		$select .= '</select>';

		return $select;

	}
	function gen_select_id($table,$post_name,$id){
		global $DB;

		$DB->query_exec("SELECT * FROM {$table}");
		$records = $DB->fetch();

		$select = '<select class="table-group-action-input form-control input-medium" name="'.$post_name.'"><option value=""></option>';

		foreach ($records as $value) {
			$sel = ($value['id']==$id)?"selected":"";
			$select .= '<option value="'.$value['id'].'" '.$sel.'>'.$value['label'].'</option>';
		}

		$select .= '</select>';

		return $select;
	}
	function gen_questions_select($id){
		global $DB;

		$DB->query_exec("SELECT sys_name FROM questions WHERE id = {$id}");
		$parent_sys_name = $DB->fetch();
		$parent_sys_name = $parent_sys_name[0]['sys_name'];

		// получим все вопросы, которые еще не в серии
		$DB->query_exec("SELECT * FROM questions WHERE q_level = 2 AND ISNULL(parent_id) AND sys_name LIKE '%{$parent_sys_name}%' AND id <> {$id} ORDER BY sys_name ASC");
		$questions = $DB->fetch();

		$select = '<select class="form-control" multiple="multiple" name="question[session][]" style="height:200px">';
		foreach ($questions as $key => $value) {
				$select .= '<option value="'.$value['id'].'">'.$value['sys_name'].' - '.$value['q_title'].'</option>';
		}
		$select .= '</select>';

		return $select;
	}
	function gen_parent_cats_select($pid){
		global $DB;

		// получим все вопросы, которые еще не в серии
		if ( empty($pid) ) {
			$DB->query_exec("SELECT * FROM categories WHERE p_id = 0");
			$cats = $DB->fetch();
		} else {
			$DB->query_exec("SELECT * FROM categories");
			$cats = $DB->fetch();			
		}

		$select = '<select class="form-control input-medium" name="category[p_id]"><option value=""></option>';
		foreach ($cats as $key => $value) {
			$sel = ($value['id']==$pid)?"selected":"";
			$select .= '<option value="'.$value['id'].'" '.$sel.'>'.$value['label'].'</option>';
		}
		$select .= '</select>';

		return $select;
	}
	// generate select for gender in questions
	function gen_gender_select($gender){
		global $DB;

		$genders = array(''=>'','male'=>'М','female'=>'Ж');

		$select = '<select class="table-group-action-input form-control input-medium" name="question[gender]">';
		foreach ($genders as $key => $value) {
			$sel = ($key==$gender)?"selected":"";
			$select .= '<option value="'.$key.'" '.$sel.'>'.$value.'</option>';
		}
		$select .= '</select>';

		return $select;
	}


	function quotes_ecran($str){
		$str = str_replace("\\", "", $str);
		$str = str_replace("'", "\'", $str);
		$str = str_replace("\"", "\"", $str);
		$str = str_replace("?", "&#63;", $str);
		return $str;
	}






function check_ean13($code){

	if (strlen($code)<>13)
		$error.="Код не из 13 символов<br>";
	else
	{
		$c1=($code[1]+$code[3]+$code[5]+$code[7]+$code[9]+$code[11])*3;
		$c2=$code[0]+$code[2]+$code[4]+$code[6]+$code[8]+$code[10];
		$c3=($c1+$c2)%10;
		if (((10-$c3)%10)<>$code[12]) $error.='Проверьте номер дисконтной карты, какая-то цифра неправильная.<br>';
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

	if ( check_ean13($code) === false ){
		generate_ean13();
	} else {
		return $code;
	}


}



?>