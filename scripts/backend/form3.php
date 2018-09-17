<?php
$page = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] : 1;
$perpage = 200;

if(isset($_GET['nofilter'])) {
    $filter = $session->filter;
    $filter['form3'] = array();
    $session->filter = $filter;
    Common::redirect();
} elseif(isset($_POST['filter'])) {
    $filter = $session->filter;
    $filter['form3'] = $_POST;
    $session->filter = $filter;
    Common::redirect();
}

$filter = $session->filter;
$where = array(1);
if(isset($filter['form3']) && $filter['form3']) {
    $filter = $filter['form3'];
    if(isset($filter['date_from']) && $filter['date_from']) {
        $where[] = "cb1.date >= '".$filter['date_from']."'";
    }
    if(isset($filter['date_to']) && $filter['date_to']) {
        $where[] = "cb1.date - INTERVAL 1 DAY <= '".$filter['date_to']."'";
    }
    if(isset($filter['game']) && $filter['game']) {
        $where[] = "cb1.game = ".$filter['game'];
    }
    if(isset($filter['shop']) && $filter['shop']) {
        $where[] = "cb1.shop = ".$filter['shop'];
    }
    if(isset($filter['tickets']) && $filter['tickets']) {
        $where[] = "cb1.tickets = '".$filter['tickets']."'";
    }
    if(isset($filter['payin']) && $filter['payin']) {
        $where[] = "cb1.payin = '".$filter['payin']."'";
    }
    if(isset($filter['payout']) && $filter['payout']) {
        $where[] = "cb1.payout = '".$filter['payout']."'";
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
    $objPHPExcel = PHPExcel_IOFactory::load(dirname(__FILE__).DS.'export-daily.xlsx');

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("VictoriaTech, s.r.o.")
        ->setLastModifiedBy("VictoriaTech, s.r.o.")
        ->setTitle("Office 2007 XLSX Daily data")
        ->setSubject("Office 2007 XLSX Daily data")
        ->setDescription("Daily export.")
        ->setKeywords("office 2007 openxml php")
        ->setCategory("Daily");

    $sheet = $objPHPExcel->setActiveSheetIndex(0);

    $query = "SELECT cb1.*, b.name AS branch, b.division, b.machines
		  FROM ".DBPREF."daily_data cb1
		  LEFT JOIN ".DBPREF."branch b ON (cb1.shop = b.branch_id)
		  WHERE ".implode(' AND ', $where)."
		  ORDER BY cb1.date ASC, cb1.id ASC";
    $items = dibi::query($query)->fetchAssoc('id');
//d($actions);

    $i = 2;
    foreach($items as $row) {

        $sheet->setCellValue('A'.$i, date('d.m.Y', strtotime($row['date'])));
        $sheet->setCellValue('B'.$i, date('j', strtotime($row['date'])));
        $sheet->setCellValue('C'.$i, date('W', strtotime($row['date'])));
        $sheet->setCellValue('D'.$i, date('n', strtotime($row['date'])));
        $sheet->setCellValue('E'.$i, date('Y', strtotime($row['date'])));
        $sheet->setCellValue('F'.$i, $TEXT[LANG]['game'][$row['game']]);
        $sheet->setCellValue('G'.$i, $row['branch']);
        $sheet->setCellValue('H'.$i, isset($TEXT[LANG]['division'][$row['division']]) ? $TEXT[LANG]['division'][$row['division']] : '');
        $sheet->setCellValue('I'.$i, $row['game'] == 6 ? $row['machines'] : '');
        $sheet->setCellValue('J'.$i, $row['tickets']);
        $sheet->setCellValue('K'.$i, $row['payin']);
        $sheet->setCellValue('L'.$i, $row['payout']);

        $i++;
    }

// Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="daily-'.date('Y-m-d-H-i-s').'.xlsx"');
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
		  FROM ".DBPREF."daily_data cb1
		  WHERE ".implode(' AND ', $where);
$cnt = dibi::query($query)->fetchSingle();

$pagerObj = new Pager($cnt, $perpage, $page);
$pagerObj->process();
$pager = $pagerObj->getPager();

$query = "SELECT cb1.*, b.name AS branch, b.division, b.machines
		  FROM ".DBPREF."daily_data cb1
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
