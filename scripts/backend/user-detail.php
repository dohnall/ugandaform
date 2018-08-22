<?php
$user_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
$data = array();

if($user_id) {
	$query = "SELECT * FROM ".DBPREF."user WHERE admin = 'N' AND user_id = ".$user_id;
	$data = dibi::query($query)->fetch();
	if(!$data) {
		Common::redirect(ROOT.'admin/user-detail');
	}
	$new = false;
} else {
	$new = true;
}

if(isset($_POST['save'])) {
	//d($_FILES);
	if($new == true) {
		if(User::isUsername($_POST['username'])) {
			$session->alert = 'Zadané uživatelské jméno již existuje!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();
		}
		if(strlen($_POST['passwd']) < 8 || $_POST['passwd'] != $_POST['repasswd']) {
			$session->alert = 'Hesla se musí shodovat a musí mít alespoň 8 znaků!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();
		}
/*
		if(User::isEmail($_POST['email'])) {
			$session->alert = 'Zadaný e-mail již existuje!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();
		}
*/
		//insert
		$values = [
			'username' => $_POST['username'],
			'fname' => $_POST['fname'],
			'lname' => $_POST['lname'],
			'passwd' => hash('sha512', md5($_POST['passwd'])),
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'admin' => 'N',
			'status' => $_POST['status'],
			'inserted%sql' => 'NOW()',
		];
		dibi::insert(DBPREF."user", $values)->execute();
		$user_id = dibi::getInsertId();
	} else {
		if(User::isUsername($_POST['username'], $user_id)) {
			$session->alert = 'Zadané uživatelské jméno již existuje!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();
		}
		if(($_POST['passwd'] || $_POST['repasswd']) && (strlen($_POST['passwd']) < 8 || $_POST['passwd'] != $_POST['repasswd'])) {
			$session->alert = 'Hesla se musí shodovat a musí mít alespoň 8 znaků!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();	
		}
/*
		if(User::isEmail($_POST['email'], $user_id)) {
			$session->alert = 'Zadaný e-mail již existuje!';
			$session->alert_type = 'danger';
			$session->data = $_POST;
			Common::redirect();
		}
*/
		//update
		$values = [
			'username' => $_POST['username'],
			'fname' => $_POST['fname'],
			'lname' => $_POST['lname'],
			'email' => $_POST['email'],
			'phone' => $_POST['phone'],
			'status' => $_POST['status'],
		];
		if($_POST['passwd']) {
			$values['passwd'] = hash('sha512', md5($_POST['passwd']));
		}

		dibi::update(DBPREF."user", $values)
		->where('user_id = %i', $user_id)
		->execute();
	}

	$session->alert = 'Záznam byl uložen!';
	Common::redirect(ROOT.'admin/user');
}

$data = isset($session->data) ? $session->data : $data;

$smarty->assign(array(
	'user_id' => $user_id,
	'data' => $data,
));
