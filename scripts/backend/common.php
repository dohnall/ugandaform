<?php
if($session->user_id == 1) {
	//$session->user_id = 2;
}

if(!$session->user_id && isset($_COOKIE['USERTOKEN']) && preg_match('/^[a-f0-9]{32}$/', $_COOKIE['USERTOKEN'])) {
	$user_id = User::getUserByHash($_COOKIE['USERTOKEN']);

	if($user_id) {
		$session->user_id = $user_id;

		$user = new User($user_id);
		$userData = $user->getData();

		if($PAGE == 'homepage') {
			Common::redirect(ROOT.'admin/dashboard');
		}
	}
} elseif($PAGE == 'homepage' && $session->user_id) {
	Common::redirect(ROOT.'admin/dashboard');
}

if($PAGE != 'homepage' && $session->user_id == null) {
	Common::redirect(ROOT.'admin');
}

if($session->user_id) {
	$user = new User($session->user_id);
	$USER = $user->getData();

	$smarty->assign(array(
		'USER_ID' => $session->user_id,
		'USER' => $USER,
		'timestamp' => time(),
	));
}
