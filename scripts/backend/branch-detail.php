<?php
$branch_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
$data = array();

if($branch_id) {
	$query = "SELECT * FROM ".DBPREF."branch WHERE branch_id = ".$branch_id;
	$data = dibi::query($query)->fetch();
	if(!$data) {
		Common::redirect(ROOT.'admin/branch-detail');
	}
	$new = false;
} else {
	$new = true;
}

if(isset($_POST['save'])) {
    if (!$_POST['name'] || !$_POST['division']) {
        $session->alert = 'Please, fill all mandatory fields!';
        $session->alert_type = 'danger';
        $session->data = $_POST;
        Common::redirect();
    }
    //d($_FILES);
    if ($new == true) {
        //insert
        $values = [
            'name' => $_POST['name'],
            'division' => $_POST['division'],
            'machines' => is_numeric($_POST['machines']) && $_POST['machines'] > 0 ? $_POST['machines'] : 0,
            'status' => $_POST['status'],
            'inserted%sql' => 'NOW()',
        ];
        dibi::insert(DBPREF . "branch", $values)->execute();
        $branch_id = dibi::getInsertId();
    } else {
        //update
        $values = [
            'name' => $_POST['name'],
            'division' => $_POST['division'],
            'machines' => is_numeric($_POST['machines']) && $_POST['machines'] > 0 ? $_POST['machines'] : 0,
            'status' => $_POST['status'],
        ];

        dibi::update(DBPREF . "branch", $values)
            ->where('branch_id = %i', $branch_id)
            ->execute();
    }

    $session->alert = 'Branch saved!';
    Common::redirect(ROOT . 'admin/branch');
} elseif(isset($_POST['addnewhost'])) {
    if (!$_POST['name'] || !$_POST['type']) {
        $session->alert = 'Please, fill all mandatory fields!';
        $session->alert_type = 'danger';
        $session->data = $_POST;
        Common::redirect();
    }
    //insert
    $values = [
        'branch_id' => $branch_id,
        'type' => $_POST['type'],
        'name' => $_POST['name'],
        'status' => $_POST['status'],
        'inserted%sql' => 'NOW()',
    ];
    dibi::insert(DBPREF . "host", $values)->execute();
    $host_id = dibi::getInsertId();
    $session->alert = 'New host saved!';
    Common::redirect();
}

$data = isset($session->data) ? $session->data : $data;

$query = "SELECT *
		  FROM ".DBPREF."host
		  WHERE branch_id = %i
		  ORDER BY name ASC";
$hosts = dibi::query($query, $branch_id)->fetchAssoc('host_id');

$smarty->assign(array(
	'branch_id' => $branch_id,
	'hosts' => $hosts,
	'data' => $data,
));
