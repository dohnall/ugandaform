<?php

if(isset($_POST['save']) || isset($_POST['savenew'])) {
	if(empty($_FILES) || $_FILES['file']['error'] != 0 || $_FILES['file']['size'] < 1) {
		$session->alert = 'Please, fill all mandatory fields!';
		$session->alert_type = 'danger';
		$session->data = $_POST;
		Common::redirect();
	}
	//d($_FILES);
	$filename = IMPORT.$session->user_id.'-usd-import-'.date('Y-m-d-H-i-s').'.xls';
	move_uploaded_file($_FILES['file']['tmp_name'], $filename);

    $inputFileType = PHPExcel_IOFactory::identify($filename);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($filename);

	$sheet = $objPHPExcel->setActiveSheetIndex(0);

	$i = 2;
	while($sheet->getCell('A'.$i)->getValue()) {
	    $year = date('Y');
	    $month = (String)$sheet->getCell('B'.$i)->getValue();
        $month = $month < 10 ? '0'.$month : $month;
        $day = (String)$sheet->getCell('C'.$i)->getValue();
        $day = $day < 10 ? '0'.$day : $day;
        $particulars = (String)$sheet->getCell('D'.$i)->getValue();
        $shop = (String)$sheet->getCell('F'.$i)->getValue();
		$payin = (String)$sheet->getCell('I'.$i)->getValue();
		$payout = (String)$sheet->getCell('J'.$i)->getValue();
        $note = (String)$sheet->getCell('K'.$i)->getValue();

        $query = "SELECT branch_id
                  FROM ".DBPREF."host
                  WHERE name = %s";
        $shop_id = dibi::query($query, $shop)->fetchSingle();

        $particulars_id = array_search($particulars, $TEXT[LANG]['particulars']);
        
		$values = [
		    'user_id' => $session->user_id,
            'date' => $year.'-'.$month.'-'.$day,
			'particulars' => is_numeric($particulars_id) ? $particulars_id : 0,
			'shop' => is_numeric($shop_id) ? $shop_id : 0,
            'payin' => $payin,
			'payout' => $payout,
            'note' => $note,
            'currency' => 'USD',
			'inserted%sql' => 'NOW()',
		];
		//d($values);
		dibi::insert(DBPREF."currency_balance", $values)->execute();

		$i++;
	}

	$session->alert = 'Excel file was imported!';

	if(isset($_POST['savenew'])) {
		Common::redirect(ROOT.'admin/form2-import');
	} else {
		Common::redirect(ROOT.'admin/form2');
	}
}
