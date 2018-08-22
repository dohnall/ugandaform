<?php
if(isset($_POST['login'])) {
	if($_POST['username']) {
		$user_id = User::login($_POST['username'], $_POST['passwd']);
		if($user_id) {
			$session->user_id = $user_id;

			$values = [
				'user_id' => $user_id,
				'username' => $_POST['username'],
				'success' => 'Y',
				'ip' => $_SERVER['REMOTE_ADDR'],
				'inserted%sql' => 'NOW()',
			];
			User::logLogin($values);

			if(isset($_POST['remember']) && $_POST['remember'] == 1) {
				$user = new User($user_id);
				$userData = $user->getData();
				setcookie('USERTOKEN', md5($user_id."_".$userData['username']), time() + 3600 * 24 * 365);
			}
			Common::redirect(ROOT.'admin/dashboard');
		}
		$values = [
			'user_id' => 0,
			'username' => $_POST['username'],
			'success' => 'N',
			'ip' => $_SERVER['REMOTE_ADDR'],
			'inserted%sql' => 'NOW()',
		];
		User::logLogin($values);
		$_SESSION['alert'] = 'Login failed, wrong username or password.';
		$_SESSION['alert_type'] = 'danger';
		Common::redirect();
	}
	$_SESSION['alert'] = 'Login failed, fill in username.';
	$_SESSION['alert_type'] = 'danger';
	Common::redirect();
}

$MAIN = 'main-login';
