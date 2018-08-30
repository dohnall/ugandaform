<?php
$form_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
$data = array();

if($form_id) {
	$query = "SELECT * FROM ".DBPREF."currency_balance WHERE currency = 'UGX' AND id = ".$form_id;
	$data = dibi::query($query)->fetch();
	if(!$data) {
		Common::redirect(ROOT.'admin/form1-detail');
	}
	$new = false;
} else {
	$new = true;
}

if(isset($_POST['save']) || isset($_POST['savenew'])) {
	if(!$_POST['particulars'] || !$_POST['shop'] || !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $_POST['date'])) {
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
			'particulars' => is_numeric($_POST['particulars']) ? $_POST['particulars'] : 0,
			'shop' => is_numeric($_POST['shop']) ? $_POST['shop'] : 0,
			'payin' => is_numeric($_POST['payin']) ? $_POST['payin'] : 0,
			'payout' => is_numeric($_POST['payout']) ? $_POST['payout'] : 0,
			'note' => $_POST['note'],
            'currency' => 'UGX',
			'inserted%sql' => 'NOW()',
		];
		//d($values);
		dibi::insert(DBPREF."currency_balance", $values)->execute();
		$form_id = dibi::getInsertId();
	} else {
		//update
        $oldvalues = [
            'date' => (String)$data->date,
            'particulars' => $data->particulars,
            'shop' => $data->shop,
            'payin' => $data->payin,
            'payout' => $data->payout,
            'note' => $data->note,
        ];

		$values = [
            'date' => $_POST['date'],
            'particulars' => is_numeric($_POST['particulars']) ? $_POST['particulars'] : 0,
            'shop' => is_numeric($_POST['shop']) ? $_POST['shop'] : 0,
            'payin' => is_numeric($_POST['payin']) ? $_POST['payin'] : 0,
            'payout' => is_numeric($_POST['payout']) ? $_POST['payout'] : 0,
            'note' => $_POST['note'],
            'updated%sql' => 'NOW()',
		];

		dibi::update(DBPREF."currency_balance", $values)
		->where('id = %i', $form_id)
		->execute();

		unset($values['updated%sql']);
		$logdata = [
            'agenda' => 1,
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
		Common::redirect(ROOT.'admin/form1-detail');
	} else {
		Common::redirect(ROOT.'admin/form1');
	}
}

$data = isset($session->data) ? $session->data : $data;

$smarty->assign(array(
	'form_id' => $form_id,
	'data' => $data,
));
