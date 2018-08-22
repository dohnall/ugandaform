<?php

class People {

	public function __construct($item_id) {
		$this->item_id = $item_id;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."people WHERE people_id = %i";
		$return = dibi::query($query, $this->item_id)->fetch();
		return $return;
	}

	public function getAwards() {
		$query = "SELECT c.value
				  FROM ".DBPREF."people_award pa
				  LEFT JOIN ".DBPREF."codebook c ON (c.codebook_id = pa.award_id)
				  WHERE pa.people_id = %i";
		$return = dibi::query($query, $this->item_id)->fetchAll();
		return $return;
	}

	public function getBooks() {
		$query = "SELECT book_id
				  FROM ".DBPREF."book_author
				  WHERE author_id = %i";
		$return = dibi::query($query, $this->item_id)->fetchAll();
		return $return;
	}

	public function update($values, $awards = []) {
		$values['url'] = Common::friendlyUrl($values['fullname']);
		dibi::update(DBPREF."people", $values)
		->where('people_id = %i', $this->item_id)
		->execute();

		dibi::delete(DBPREF."people_award")
		->where('people_id = %i', $this->item_id)
		->execute();

		if($awards) {
			foreach($awards as $award_id) {
				$values = [
					'people_id' => $this->item_id,
					'award_id' => $award_id,
				];
				dibi::insert(DBPREF."people_award", $values)->execute();
			}
		}
	}

	public function getPseudonyms() {
		$query = "SELECT * FROM ".DBPREF."people WHERE parent_id = %i";
		$return = dibi::query($query, $this->item_id)->fetchAll();
		return $return;
	}

	public function getLastComments($cnt = 10) {
		$books = dibi::select('book_id')
			->from(DBPREF."book_author")
			->where('author_id = %i', $this->item_id)
			->fetchAssoc('book_id');

		$comments = dibi::select('*')
			->from(DBPREF."book_comment")
			->where('book_id IN (?)', array_keys($books))
			->where('status = %s', 'A')
			->where('message != %s', '')
			->orderBy('inserted')->desc()
			->limit($cnt)
			->fetchAll();

		foreach($comments as $k => $row) {
			$book = new Book($row->book_id);
			$user = new User($row->user_id);
			
			$comments[$k]->book = $book->getData();
			$comments[$k]->user = $user->getData();

			if(isset($comments[$k]->book->image_id)) {
				$image = new Image($comments[$k]->book->image_id);
				$comments[$k]->book->image = $image->getData();
			}

		}

		//d($comments);
		return $comments;
	}

	public static function getList($role = 0, $status = 'A') {
		$return = dibi::select('*')->from(DBPREF."people");
		if($role) {
			$return = $return->where('role_id = %i', $role);
		}
		if($status) {
			$return = $return->where('status = %s', $status);
		}
		$return->orderBy('lname')->asc()->orderBy('fname')->asc()->fetchAll();
		return $return;
	}

	public static function insert($values, $awards = []) {
		$values = array_merge($values, [
			'url' => Common::friendlyUrl($values['fullname']),
			'inserted%sql' => 'NOW()',
		]);
		dibi::insert(DBPREF."people", $values)->execute();
		$people_id = dibi::getInsertId();

		if($awards) {
			foreach($awards as $award_id) {
				$values = [
					'people_id' => $people_id,
					'award_id' => $award_id,
				];
				dibi::insert(DBPREF."people_award", $values)->execute();
			}
		}

		return $people_id;
	}

	public static function delete($item_id) {
		dibi::delete(DBPREF."people")
		->where('people_id = %i', $item_id)
		->execute();
	}

	public static function getByFullname($role, $fullname) {
		$query = "SELECT people_id FROM ".DBPREF."people WHERE role_id = %i AND fullname = %s";
		$return = dibi::query($query, $role, $fullname)->fetchSingle();
		return $return;
	}

	public static function getByUrl($role, $url) {
		$query = "SELECT people_id FROM ".DBPREF."people WHERE role_id = %i AND url = %s";
		$return = dibi::query($query, $role, $url)->fetchSingle();
		return $return;
	}
}
