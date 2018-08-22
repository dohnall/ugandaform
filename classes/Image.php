<?php

class Image {

	public function __construct($image_id) {
		$this->image_id = $image_id;
	}

	public function getData() {
		$data = dibi::select('*')
				->from(DBPREF."image")
				->where('image_id = %s', $this->image_id)
				->fetch();
		$data->subdir = substr($data->hash, 0, 2);

		$parts = explode('.', $data->name);
		$fileext = array_pop($parts);
		$data->hashfile = $data->hash.'.'.$fileext;

		return $data;
	}

	public static function insert($file) {
		$filehash = md5_file($file['tmp_name']);

		$image_id = dibi::select('image_id')
		->from(DBPREF."image")
		->where('hash = %s', $filehash)
		->fetchSingle();		
		if($image_id) {
			return $image_id;
		}

		$subdir = substr($filehash, 0, 2);
		$filename = $file['name'];
		$parts = explode('.', $filename);
		$fileext = array_pop($parts);
		$filesize = $file['size'];
		$filemime = $file['type'];

		if(move_uploaded_file($file['tmp_name'], FILES.$subdir.DS.$filehash.'.'.$fileext)) {
			$values = [
				'name' => $filename,
				'hash' => $filehash,
				'size' => $filesize,
				'mimetype' => $filemime,
				'inserted%sql' => 'NOW()',
			];
			dibi::insert(DBPREF."image", $values)->execute();
			return dibi::insertId();
		}
	}

}
