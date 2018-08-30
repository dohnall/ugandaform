<?php
$form_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
$data = array();

if($form_id) {
	$query = "SELECT * FROM ".DBPREF."daily_data WHERE id = ".$form_id;
	$data = dibi::query($query)->fetch();
	if(!$data) {
		Common::redirect(ROOT.'admin/form3-detail');
	}
	$new = false;
} else {
	$new = true;
}

if(isset($_POST['save']) || isset($_POST['savenew'])) {
	if(!$_POST['game'] || !$_POST['shop'] || !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $_POST['date'])) {
		$session->alert = 'Please, fill all mandatory fields!';
		$session->alert_type = 'danger';
		$session->data = $_POST;
		Common::redirect();
	}
	//d($_FILES);
	if($new == true) {
		//insert
		$values = [
		    'user_id' => $session->user_id,
			'date' => $_POST['date'],
			'game' => is_numeric($_POST['game']) ? $_POST['game'] : 0,
			'shop' => is_numeric($_POST['shop']) ? $_POST['shop'] : 0,
            'tickets' => is_numeric($_POST['tickets']) ? $_POST['tickets'] : 0,
            'payin' => is_numeric($_POST['payin']) ? $_POST['payin'] : 0,
			'payout' => is_numeric($_POST['payout']) ? $_POST['payout'] : 0,
			'inserted%sql' => 'NOW()',
		];
		//d($values);
		dibi::insert(DBPREF."daily_data", $values)->execute();
		$form_id = dibi::getInsertId();
	} else {
		//update
        $oldvalues = [
            'date' => (String)$data->date,
            'game' => $data->game,
            'shop' => $data->shop,
            'tickets' => $data->tickets,
            'payin' => $data->payin,
            'payout' => $data->payout,
        ];

		$values = [
            'date' => $_POST['date'],
            'game' => is_numeric($_POST['game']) ? $_POST['game'] : 0,
            'shop' => is_numeric($_POST['shop']) ? $_POST['shop'] : 0,
            'tickets' => is_numeric($_POST['tickets']) ? $_POST['tickets'] : 0,
            'payin' => is_numeric($_POST['payin']) ? $_POST['payin'] : 0,
            'payout' => is_numeric($_POST['payout']) ? $_POST['payout'] : 0,
            'updated%sql' => 'NOW()',
		];

		dibi::update(DBPREF."daily_data", $values)
		->where('id = %i', $form_id)
		->execute();

		unset($values['updated%sql']);
		$logdata = [
            'agenda' => 3,
            'form_id' => $form_id,
            'old_value' => serialize($oldvalues),
            'new_value' => serialize($values),
            'user_id' => $session->user_id,
            'inserted%sql' => 'NOW()',
        ];
        dibi::insert(DBPREF."form_log", $logdata)->execute();
	}

	$session->alert = 'Record was saved!';

	if(isset($_POST['savenew'])) {
		Common::redirect(ROOT.'admin/form3-detail');
	} else {
		Common::redirect(ROOT.'admin/form3');
	}
}

$data = isset($session->data) ? $session->data : $data;

$smarty->assign(array(
	'form_id' => $form_id,
	'data' => $data,
));
