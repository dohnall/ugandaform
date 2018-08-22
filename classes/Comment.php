<?php

class Comment {

	var $data = array();

	public function __construct($item_id) {
		$this->item_id = $item_id;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."book_comment WHERE comment_id = %i";
		$this->data = dibi::query($query, $this->item_id)->fetch();
		return $this->data;
	}

	public function update($values) {
		dibi::update(DBPREF."book_comment", $values)
		->where('comment_id = %i', $this->item_id)
		->execute();
	}

	public static function insert($values) {
		$values = array_merge($values, [
			'inserted%sql' => 'NOW()',
		]);
		dibi::insert(DBPREF."book_comment", $values)->execute();
		$item_id = dibi::getInsertId();

		return $item_id;
	}

	public static function delete($item_id) {
		dibi::delete(DBPREF."book_comment")
		->where('comment_id = %i', $item_id)
		->execute();
	}

	public static function isEvaluated($user_id, $book_id) {
		$query = "SELECT COUNT(*) AS cnt FROM ".DBPREF."book_comment WHERE user_id = %i AND book_id = %i AND stars > 0";
		$cnt = dibi::query($query, $user_id, $book_id)->fetchSingle();
		return $cnt ? true : false;
	}

	public static function resetEvaluation($user_id, $book_id, $stars) {
		$values = [
			'stars' => $stars,
		];
		dibi::update(DBPREF."book_comment", $values)
		->where('user_id = %i', $user_id)
		->where('book_id = %i', $book_id)
		->execute();		
	}

}
