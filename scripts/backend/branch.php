<?php
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$perpage = 20;

$order = $session->order;
if(!isset($order['branch'])) {
	$order['branch']['order'] = 'b.name';
	$order['branch']['sort'] = 'ASC';
}

if(isset($_GET['nofilter'])) {
	$filter = $session->filter;
	$filter['branch'] = array();
	$session->filter = $filter;
	Common::redirect();
} elseif(isset($_GET['order']) && in_array($_GET['order'], array('b.name', 'b.status', 'b.inserted'))) {
	if($order['branch']['order'] == $_GET['order']) {
		$order['branch']['sort'] = $order['branch']['sort'] == 'ASC' ? 'DESC' : 'ASC';
	} else {
		$order['branch']['order'] = $_GET['order'];
		$order['branch']['sort'] = 'ASC';
	}
	$session->order = $order;
	Common::redirect();
} elseif(isset($_POST['filter'])) {
	$filter = $session->filter;
	$filter['branch'] = $_POST;
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
$where = array("1");
if(isset($filter['branch']) && $filter['branch']) {
	$filter = $filter['branch'];
	if(isset($filter['name']) && $filter['name']) {
		$where[] = "b.name LIKE '%".$filter['name']."%'";
	}
	if(isset($filter['status']) && $filter['status']) {
		$where[] = "b.status LIKE '".$filter['status']."'";
	}
	if(isset($filter['inserted_from']) && $filter['inserted_from']) {
		$where[] = "b.inserted >= '".$filter['inserted_from']."'";
	}
	if(isset($filter['inserted_to']) && $filter['inserted_to']) {
		$where[] = "b.inserted - INTERVAL 1 DAY <= '".$filter['inserted_to']."'";
	}

	$smarty->assign(array(
		'filter' => $filter,
	));
}

$query = "SELECT COUNT(*) AS cnt
		  FROM ".DBPREF."branch b
		  WHERE ".implode(' AND ', $where);
$cnt = dibi::query($query)->fetchSingle();

$pagerObj = new Pager($cnt, $perpage, $page);
$pagerObj->process();
$pager = $pagerObj->getPager();

$query = "SELECT b.*
		  FROM ".DBPREF."branch b
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY ".$order['branch']['order']." ".$order['branch']['sort']."
		  LIMIT ".$pager['from'].", ".$perpage;
$branches = dibi::query($query)->fetchAssoc('branch_id');

$smarty->assign(array(
	'branches' => $branches,
	'pager' => $pager,
	'count' => $cnt,
	'ORDER' => $order['branch'],
));
