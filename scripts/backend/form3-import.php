<?php

if(isset($_POST['save']) || isset($_POST['savenew'])) {
	if(!$_POST['game'] || empty($_FILES) || $_FILES['file']['error'] != 0 || $_FILES['file']['size'] < 1 || !preg_match('/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/', $_POST['date'])) {
		$session->alert = 'Please, fill all mandatory fields!';
		$session->alert_type = 'danger';
		$session->data = $_POST;
		Common::redirect();
	}
	//d($_FILES);
    //Apollo export
	if($_FILES['file']['type'] == 'text/xml') {
        $filename = IMPORT . $session->user_id . '-daily-data-import-' . date('Y-m-d-H-i-s') . '.xml';
        move_uploaded_file($_FILES['file']['tmp_name'], $filename);

        $xml = simplexml_load_file($filename);
        if(!$xml) {
            exit('Failed to open import file.');
        }

        foreach($xml->terminal as $row) {
            $shop = (String)$row->sn;
            $payin = (int)$row->vlt_in;
            $payout = (int)$row->vlt_out;

            if(!$payin && !$payout) {
                continue;
            }
//d($shop, $payin, $payout);
            $query = "SELECT branch_id
                  FROM " . DBPREF . "host
                  WHERE name = %s";
            $shop_id = dibi::query($query, $shop)->fetchSingle();

            $values = [
                'user_id' => $session->user_id,
                'date' => $_POST['date'],
                'game' => is_numeric($_POST['game']) ? $_POST['game'] : 0,
                'shop' => is_numeric($shop_id) ? $shop_id : 0,
                'tickets' => 0,
                'payin' => (int)$payin,
                'payout' => (int)$payout,
                'inserted%sql' => 'NOW()',
            ];
            //d($values);
            dibi::insert(DBPREF . "daily_data", $values)->execute();
        }
    //BUMP bets export
    } elseif(is_numeric($_POST['game']) && $_POST['game'] == 1) {
        $filename = IMPORT . $session->user_id . '-daily-data-import-' . date('Y-m-d-H-i-s') . '.xlsx';
        move_uploaded_file($_FILES['file']['tmp_name'], $filename);

        $inputFileType = PHPExcel_IOFactory::identify($filename);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filename);

        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        $i = 3;
        while ($sheet->getCell('A' . $i)->getValue()) {
            $shop = (String)$sheet->getCell('A' . $i)->getValue();
            $tickets1 = (String)$sheet->getCell('F' . $i)->getValue();
            $tickets2 = (String)$sheet->getCell('H' . $i)->getValue();
            $payin1 = (String)$sheet->getCell('E' . $i)->getValue();
            $payin2 = (String)$sheet->getCell('G' . $i)->getValue();
            $payout1 = (String)$sheet->getCell('I' . $i)->getValue();
            $payout2 = (String)$sheet->getCell('K' . $i)->getValue();

            $query = "SELECT branch_id
                  FROM " . DBPREF . "host
                  WHERE name = %s";
            $shop_id = dibi::query($query, $shop)->fetchSingle();

            $values = [
                'user_id' => $session->user_id,
                'date' => $_POST['date'],
                'game' => is_numeric($_POST['game']) ? $_POST['game'] : 0,
                'shop' => is_numeric($shop_id) ? $shop_id : 0,
                'tickets' => $tickets1 - $tickets2,
                'payin' => $payin1 - $payin2,
                'payout' => $payout1 + $payout2,
                'inserted%sql' => 'NOW()',
            ];
            //d($values);
            dibi::insert(DBPREF . "daily_data", $values)->execute();

            if($shop_id == 91) {
                $payin = (String)$sheet->getCell('Y' . $i)->getValue();
                $payout = (String)$sheet->getCell('Z' . $i)->getValue();
                $values = [
                    'user_id' => $session->user_id,
                    'date' => $_POST['date'],
                    'game' => 3,
                    'shop' => is_numeric($shop_id) ? $shop_id : 0,
                    'tickets' => 0,
                    'payin' => $payin,
                    'payout' => $payout,
                    'inserted%sql' => 'NOW()',
                ];
                //d($values);
                dibi::insert(DBPREF . "daily_data", $values)->execute();
            }

            $i++;
        }
    //Other exports
    } else {
        $filename = IMPORT . $session->user_id . '-daily-data-import-' . date('Y-m-d-H-i-s') . '.xls';
        move_uploaded_file($_FILES['file']['tmp_name'], $filename);

        $inputFileType = PHPExcel_IOFactory::identify($filename);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($filename);

        $sheet = $objPHPExcel->setActiveSheetIndex(0);

        $i = 2;
        while ($sheet->getCell('A' . $i)->getValue()) {
            $shop = (String)$sheet->getCell('A' . $i)->getValue();
            $tickets = (String)$sheet->getCell('E' . $i)->getValue();
            $payin = (String)$sheet->getCell('G' . $i)->getValue();
            $payout = (String)$sheet->getCell('H' . $i)->getValue();

            if($shop == '999') {
                continue;
            }

            $query = "SELECT branch_id
                  FROM " . DBPREF . "host
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
            dibi::insert(DBPREF . "daily_data", $values)->execute();

            $i++;
        }
    }

	$session->alert = 'File was imported!';

	if(isset($_POST['savenew'])) {
		Common::redirect(ROOT.'admin/form3-import');
	} else {
		Common::redirect(ROOT.'admin/form3');
	}
}
