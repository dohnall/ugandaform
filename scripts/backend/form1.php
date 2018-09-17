<?php
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$perpage = 200;

if(isset($_GET['nofilter'])) {
	$filter = $session->filter;
	$filter['form1'] = array();
	$session->filter = $filter;
	Common::redirect();
} elseif(isset($_POST['filter'])) {
	$filter = $session->filter;
	$filter['form1'] = $_POST;
	$session->filter = $filter;
	Common::redirect();
}

$filter = $session->filter;
$where = array("cb1.currency = 'UGX'");
if(isset($filter['form1']) && $filter['form1']) {
	$filter = $filter['form1'];
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
    if(isset($filter['inserted_from']) && $filter['inserted_from']) {
        $where[] = "cb1.inserted >= '".$filter['inserted_from']."'";
    }
    if(isset($filter['inserted_to']) && $filter['inserted_to']) {
        $where[] = "cb1.inserted - INTERVAL 1 DAY <= '".$filter['inserted_to']."'";
    }
	$smarty->assign(array(
		'filter' => $filter,
	));
}

if(isset($_GET['export'])) {
    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
    $cacheSettings = array('memoryCacheSize' => '8MB');
    PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

    //Load $inputFileName to a PHPExcel Object
    $objPHPExcel = PHPExcel_IOFactory::load(dirname(__FILE__).DS.'export-ugx.xlsx');

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("VictoriaTech, s.r.o.")
        ->setLastModifiedBy("VictoriaTech, s.r.o.")
        ->setTitle("Office 2007 XLSX UGX")
        ->setSubject("Office 2007 XLSX UGX")
        ->setDescription("UGX export.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("UGX");

    $sheet = $objPHPExcel->setActiveSheetIndex(0);

    $query = "SELECT cb1.*, b.name AS branch, b.division, b.machines, (SELECT SUM(cb2.payin) - SUM(cb2.payout) FROM ".DBPREF."currency_balance cb2 WHERE cb2.currency = 'UGX' AND ((cb2.date < cb1.date) OR (cb2.date = cb1.date AND cb2.id <= cb1.id))) AS balance
		  FROM ".DBPREF."currency_balance cb1
		  LEFT JOIN ".DBPREF."branch b ON (cb1.shop = b.branch_id)
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY cb1.date ASC, cb1.id ASC";
    $items = dibi::query($query)->fetchAssoc('id');
//d($actions);

    $i = 2;
    foreach($items as $row) {

        $sheet->setCellValue('B'.$i, date('n', strtotime($row['date'])));
        $sheet->setCellValue('C'.$i, date('j', strtotime($row['date'])));
        $sheet->setCellValue('D'.$i, $TEXT[LANG]['particulars'][$row['particulars']]);
        $sheet->setCellValue('E'.$i, $TEXT[LANG]['cf_name'][$row['particulars']]);
        $sheet->setCellValue('F'.$i, $row['branch']);
        $sheet->setCellValue('G'.$i, $TEXT[LANG]['division'][$row['division']]);
        $sheet->setCellValue('H'.$i, $row['payin']);
        $sheet->setCellValue('I'.$i, $row['payout']);
        $sheet->setCellValue('J'.$i, $row['note']);
        $sheet->setCellValue('K'.$i, $row['balance']);

        $i++;
    }

// Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="ugx-'.date('Y-m-d-H-i-s').'.xlsx"');
    header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;
}

$query = "SELECT COUNT(*) AS cnt
		  FROM ".DBPREF."currency_balance cb1
		  WHERE ".implode(' AND ', $where);
$cnt = dibi::query($query)->fetchSingle();

$pagerObj = new Pager($cnt, $perpage, $page);
$pagerObj->process();
$pager = $pagerObj->getPager();
/*
$query = "SELECT cb1.*, (SELECT SUM(cb2.payin) - SUM(cb2.payout) FROM ".DBPREF."currency_balance cb2 WHERE (cb2.date < cb1.date) OR (cb2.date = cb1.date AND cb2.id <= cb1.id)) AS balance
		  FROM ".DBPREF."currency_balance cb1
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY cb1.date ASC, cb1.id ASC
		  LIMIT ".$pager['from'].", ".$perpage;
*/
$query = "SELECT cb1.*, b.name AS branch, b.division, b.machines
		  FROM ".DBPREF."currency_balance cb1
		  LEFT JOIN ".DBPREF."branch b ON (cb1.shop = b.branch_id)
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY cb1.id DESC
		  LIMIT ".$pager['from'].", ".$perpage;
$items = dibi::query($query)->fetchAssoc('id');

$query = "SELECT *
		  FROM ".DBPREF."branch 
		  ORDER BY name ASC";
$branches = dibi::query($query)->fetchAssoc('branch_id');

$smarty->assign(array(
	'items' => $items,
    'branches' => $branches,
	'pager' => $pager,
	'count' => $cnt,
));
