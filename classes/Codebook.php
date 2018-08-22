<?php

class Codebook {

	public function __construct($item_id) {
		$this->item_id = $item_id;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."codebook WHERE codebook_id = %i";
		$return = dibi::query($query, $this->item_id)->fetch();
		return $return;
	}

	public function update($values) {
		$query = "UPDATE ".DBPREF."codebook SET";
		dibi::update(DBPREF."codebook", $values)
		->where('codebook_id = %i', $this->item_id)
		->execute();
	}

	public static function nextCode($type) {
		$code = dibi::select('MAX(code)+1')->as('code')
		->from(DBPREF."codebook")
		->where('type = %s', $type)
		->fetchSingle();
		$code = $code ? $code : 1;
		return $code;
	}

	public static function getList($type) {
		$return = dibi::select('*')
		->from(DBPREF."codebook")
		->where('type = %s', $type)
		->orderBy('value')->asc()
		->fetchAll();
		return $return;
	}

	public static function insert($type, $values) {
		$values = array_merge($values, [
			'type' => $type,
			'code' => Codebook::nextCode($type),
			'inserted%sql' => 'NOW()',
		]);
		dibi::insert(DBPREF."codebook", $values)->execute();
		return dibi::getInsertId();
	}

	public static function delete($type, $item_id) {
		dibi::delete(DBPREF."codebook")
		->where('type = %s', $type)
		->where('codebook_id = %i', $item_id)
		->execute();
	}

	public static function getByValue($type, $value) {
		$query = "SELECT codebook_id FROM ".DBPREF."codebook WHERE type = %s AND value = %s";
		$return = dibi::query($query, $type, $value)->fetchSingle();
		return $return;
	}

	public static function getById($type, $id) {
		$query = "SELECT value FROM ".DBPREF."codebook WHERE type = %s AND codebook_id = %i";
		$return = dibi::query($query, $type, $id)->fetchSingle();
		return $return;
	}
}
