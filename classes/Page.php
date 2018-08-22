<?php

class Page {

	public function __construct($item_id) {
		$this->item_id = $item_id;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."page WHERE page_id = %i";
		$return = dibi::query($query, $this->item_id)->fetch();
		return $return;
	}

	public function update($values) {
		$values['url'] = Common::friendlyUrl($values['name']);
		dibi::update(DBPREF."page", $values)
		->where('page_id = %i', $this->item_id)
		->execute();
	}

	public static function insert($type, $values) {
		$values = array_merge($values, [
			'url' => Common::friendlyUrl($values['name']),
			'type' => $type,
			'inserted%sql' => 'NOW()',
		]);
		dibi::insert(DBPREF."page", $values)->execute();
		$item_id = dibi::getInsertId();
		return $item_id;
	}

	public static function delete($type, $item_id) {
		dibi::delete(DBPREF."page")
		->where('type = %s', $type)
		->where('page_id = %i', $item_id)
		->execute();
	}

	public static function getPageByUrl($type, $url) {
		$query = "SELECT page_id FROM ".DBPREF."page WHERE type = %s AND url = %s";
		$return = dibi::query($query, $type, $url)->fetchSingle();
		return $return ? $return : false;
	}

}
