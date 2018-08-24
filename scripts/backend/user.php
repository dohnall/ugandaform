<?php
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$perpage = 20;

$order = $session->order;
if(!isset($order['user'])) {
	$order['user']['order'] = 'u1.username';
	$order['user']['sort'] = 'ASC';
}

if(isset($_GET['nofilter'])) {
	$filter = $session->filter;
	$filter['user'] = array();
	$session->filter = $filter;
	Common::redirect();
} elseif(isset($_GET['dashboard'])) {
	$dashboard = $session->dashboard;
	if(!isset($dashboard['from_date'])) {
		$dashboard['from_date'] = date('Y-m-d');
	}
	if(!isset($dashboard['to_date'])) {
		$dashboard['to_date'] = date('Y-m-d');
	}

	$filter = $session->filter;
	$filter['user'] = array(
		'inserted_from' => $dashboard['from_date'],
		'inserted_to' => $dashboard['to_date'],
	);
	$session->filter = $filter;
	Common::redirect(ROOT.'admin/user');
} elseif(isset($_GET['order']) && in_array($_GET['order'], array('u1.username', 'u1.fname', 'u1.lname', 'u1.points', 'u1.status', 'u1.inserted'))) {
	if($order['user']['order'] == $_GET['order']) {
		$order['user']['sort'] = $order['user']['sort'] == 'ASC' ? 'DESC' : 'ASC';
	} else {
		$order['user']['order'] = $_GET['order'];
		$order['user']['sort'] = 'ASC';
	}
	$session->order = $order;
	Common::redirect();
} elseif(isset($_POST['filter'])) {
	$filter = $session->filter;
	$filter['user'] = $_POST;
	$session->filter = $filter;
	Common::redirect();
/*
} elseif(isset($_GET['delete']) && is_numeric($_GET['delete']) && $user['role'] == 1) {
	$query = "DELETE FROM ".DBPREF."user WHERE user_id = ".$_GET['delete'];
	if($db->delete($query)) {
		$session->alert = 'Uživatel byl smazán!';
	} else {
		$session->alert = 'Neznámý uživatel!';
	}
	Common::redirect();
*/
}

$filter = $session->filter;
$where = array("u1.admin = 'N'");
if(isset($filter['user']) && $filter['user']) {
	$filter = $filter['user'];
	if(isset($filter['username']) && $filter['username']) {
		$where[] = "u1.username LIKE '%".$filter['username']."%'";
	}
	if(isset($filter['fname']) && $filter['fname']) {
		$where[] = "u1.fname LIKE '%".$filter['fname']."%'";
	}
	if(isset($filter['lname']) && $filter['lname']) {
		$where[] = "u1.lname LIKE '%".$filter['lname']."%'";
	}
	if(isset($filter['points']) && $filter['points']) {
		$where[] = "u1.points >= '".$filter['points']."'";
	}
	if(isset($filter['status']) && $filter['status']) {
		$where[] = "u1.status LIKE '".$filter['status']."'";
	}
	if(isset($filter['inserted_from']) && $filter['inserted_from']) {
		$where[] = "u1.inserted >= '".$filter['inserted_from']."'";
	}
	if(isset($filter['inserted_to']) && $filter['inserted_to']) {
		$where[] = "u1.inserted - INTERVAL 1 DAY <= '".$filter['inserted_to']."'";
	}

	$smarty->assign(array(
		'filter' => $filter,
	));
}

$query = "SELECT COUNT(*) AS cnt
		  FROM ".DBPREF."user u1
		  WHERE ".implode(' AND ', $where);
$cnt = dibi::query($query)->fetchSingle();

$pagerObj = new Pager($cnt, $perpage, $page);
$pagerObj->process();
$pager = $pagerObj->getPager();

$query = "SELECT u1.*
		  FROM ".DBPREF."user u1
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY ".$order['user']['order']." ".$order['user']['sort']."
		  LIMIT ".$pager['from'].", ".$perpage;
$users = dibi::query($query)->fetchAssoc('user_id');

$smarty->assign(array(
	'users' => $users,
	'pager' => $pager,
	'count' => $cnt,
	'ORDER' => $order['user'],
));
