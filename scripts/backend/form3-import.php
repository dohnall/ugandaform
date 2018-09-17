<?php

if(isset($_POST['save']) || isset($_POST['savenew'])) {
	if(!$_POST['game'] || empty($_FILES) || $_FILES['file']['error'] != 0 || $_FILES['file']['size'] < 1 || !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $_POST['date'])) {
		$session->alert = 'Please, fill all mandatory fields!';
		$session->alert_type = 'danger';
		$session->data = $_POST;
		Common::redirect();
	}
	//d($_FILES);
	$filename = IMPORT.$session->user_id.'-daily-data-import-'.date('Y-m-d-H-i-s').'.xls';
	move_uploaded_file($_FILES['file']['tmp_name'], $filename);

    $inputFileType = PHPExcel_IOFactory::identify($filename);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($filename);

	$sheet = $objPHPExcel->setActiveSheetIndex(0);

	$i = 2;
	while($sheet->getCell('A'.$i)->getValue()) {
        $shop = (String)$sheet->getCell('B'.$i)->getValue();
	    $tickets = (String)$sheet->getCell('E'.$i)->getValue();
		$payin = (String)$sheet->getCell('G'.$i)->getValue();
		$payout = (String)$sheet->getCell('H'.$i)->getValue();

        $query = "SELECT branch_id
                  FROM ".DBPREF."host
                  WHERE name = %s";
        $shop_id = dibi::query($query, $shop)->fetchSingle();

		$values = [
		    'user_id' => $session->user_id,
			'date' => $_POST['date'],
			'game' => is_numeric($_POST['game']) ? $_POST['game'] : 0,
			'shop' => is_numeric($shop_id) ? $shop_id : 0,
            'tickets' => $tickets,
            'payin' => $payin,
			'payout' => $payout,
			'inserted%sql' => 'NOW()',
		];
		//d($values);
		dibi::insert(DBPREF."daily_data", $values)->execute();

		$i++;
	}

	$session->alert = 'Excel file was imported!';

	if(isset($_POST['savenew'])) {
		Common::redirect(ROOT.'admin/form3-import');
	} else {
		Common::redirect(ROOT.'admin/form3');
	}
}
