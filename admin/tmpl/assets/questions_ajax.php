<?php
/*
 * Paging
 */
require rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . 'lib/fam_functions.php';

$where = array();

if (!empty($_POST['id'])) {
    $where['id'] = $_POST['id'];
}
if (!empty($_POST['q_title']) && $_POST['q_title'] != "") {
    $where['q_title'] = $_POST['q_title'];
}
if (!empty($_POST['sys_name'])) {
    $where['sys_name'] = $_POST['sys_name'];
}
if (!empty($_POST['q_visibility'])) {
    $where['q_visibility'] = $_POST['q_visibility'];
}
if (!empty($_POST['q_hard'])) {
    $where['q_hard'] = $_POST['q_hard'];
}
if (!empty($_POST['q_nomer'])) {
    $where['q_nomer'] = $_POST['q_nomer'];
}
if (!empty($_POST['q_category'])) {
    $where['q_category'] = $_POST['q_category'];
}
if (!empty($_POST['q_type'])) {
    $where['q_type'] = $_POST['q_type'];
}

$questions = get_questions($where);
$questions = $questions['questions'];

$iTotalRecords = count($questions);
$iDisplayLength = intval($_REQUEST['length']);
$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
$iDisplayStart = intval($_REQUEST['start']);
$sEcho = intval($_REQUEST['draw']);

$records = array();
$records["data"] = array();

$end = $iDisplayStart + $iDisplayLength;
$end = $end > $iTotalRecords ? $iTotalRecords : $end;

$status_list = array(
    array("success" => "Pending"),
    array("info" => "Closed"),
    array("danger" => "On Hold"),
    array("warning" => "Fraud")
);


for ($i = $iDisplayStart; $i < $end; $i++) {


    $records["data"][] = array(
        "DT_RowId" => 'q_id_'.$questions[$i]['id'],
        $questions[$i]['id'],
        $questions[$i]['q_title'],
        $questions[$i]['sys_name'],
        $questions[$i]['q_visibility'],
        $questions[$i]['q_hard'],
        $questions[$i]['q_nomer'],
        get_question_label('categories',$questions[$i]['q_category']),
        get_question_label('types',$questions[$i]['q_type']),
        '<div class="btn-group"> <button class="btn btn-xs green dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false"> Действия <i class="fa fa-angle-down"></i> </button> <ul class="dropdown-menu" role="menu"> <li> <a href="index.php?action=edit_question&qid='.$questions[$i]["id"].'"> <i class="icon-docs"></i> Редактировать вопрос </a> </li> <li> <a href="index.php?action=del_question&qid='.$questions[$i]["id"].'"> <i class="icon-docs"></i> Удалить вопрос </a> </li> </ul> </div>'
    );
}

if (isset($_REQUEST["customActionType"]) && $_REQUEST["customActionType"] == "group_action") {
    $records["customActionStatus"] = "OK"; // pass custom message(useful for getting status of group actions)
    $records["customActionMessage"] = "Group action successfully has been completed. Well done!"; // pass custom message(useful for getting status of group actions)
}

$records["draw"] = $sEcho;
$records["recordsTotal"] = $iTotalRecords;
$records["recordsFiltered"] = $iTotalRecords;

echo json_encode($records);
?>