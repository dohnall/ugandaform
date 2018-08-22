<?php

class Logger {

	public static function log($filename, $str, $daily = false) {

		if($daily && file_exists(LOG.$filename)) {
			$time = fileatime(LOG.$filename);
			$date = date('Y-m-d', $time);
			$curdate = date('Y-m-d');
			if($date < $curdate) {
				unlink(LOG.$filename);
			}
		}

		$f = fopen(LOG.$filename, 'ab');

		$str = iconv('UTF-8', 'CP1250', $str);
		$str = strtr($str, '��������������������������ͼ���؊����ݎ', 'aacdeeeilnoorstuuuyzAACDEEEILNOORSTUUUYZ');

		fwrite($f, date("Y-m-d H:i:s")." - ".preg_replace("/[\s]{2,}/", " ", $str)."\n");
		fclose($f);
	}

}
