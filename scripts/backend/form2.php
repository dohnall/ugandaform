<?php
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$perpage = 200;

if(isset($_GET['nofilter'])) {
    $filter = $session->filter;
    $filter['form2'] = array();
    $session->filter = $filter;
    Common::redirect();
} elseif(isset($_POST['filter'])) {
    $filter = $session->filter;
    $filter['form2'] = $_POST;
    $session->filter = $filter;
    Common::redirect();
}

$filter = $session->filter;
$where = array("cb1.currency = 'USD'");
if(isset($filter['form2']) && $filter['form2']) {
    $filter = $filter['form2'];
    if(isset($filter['date_from']) && $filter['date_from']) {
        $where[] = "cb1.date >= '".$filter['date_from']."'";
    }
    if(isset($filter['date_to']) && $filter['date_to']) {
        $where[] = "cb1.date - INTERVAL 1 DAY <= '".$filter['date_to']."'";
    }
    if(isset($filter['particulars']) && $filter['particulars']) {
        $where[] = "cb1.particulars = ".$filter['particulars'];
    }
    if(isset($filter['shop']) && $filter['shop']) {
        $where[] = "cb1.shop = ".$filter['shop'];
    }
    if(isset($filter['payin']) && $filter['payin']) {
        $where[] = "cb1.payin = '".$filter['payin']."'";
    }
    if(isset($filter['payout']) && $filter['payout']) {
        $where[] = "cb1.payout = '".$filter['payout']."'";
    }
    if(isset($filter['note']) && $filter['note']) {
        $where[] = "cb1.note LIKE '%".$filter['note']."%'";
    }

    $smarty->assign(array(
        'filter' => $filter,
    ));
}

$query = "SELECT COUNT(*) AS cnt
		  FROM ".DBPREF."currency_balance cb1
		  WHERE ".implode(' AND ', $where);
$cnt = dibi::query($query)->fetchSingle();

$pagerObj = new Pager($cnt, $perpage, $page);
$pagerObj->process();
$pager = $pagerObj->getPager();

$query = "SELECT cb1.*, (SELECT SUM(cb2.payin) - SUM(cb2.payout) FROM ".DBPREF."currency_balance cb2 WHERE (cb2.date < cb1.date) OR (cb2.date = cb1.date AND cb2.id <= cb1.id)) AS balance
		  FROM ".DBPREF."currency_balance cb1
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY cb1.date ASC, id ASC
		  LIMIT ".$pager['from'].", ".$perpage;
$items = dibi::query($query)->fetchAssoc('id');

$smarty->assign(array(
    'items' => $items,
    'pager' => $pager,
    'count' => $cnt,
));
