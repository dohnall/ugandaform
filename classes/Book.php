<?php

class Book {

	var $data = array();

	public function __construct($item_id) {
		$this->item_id = $item_id;
	}

	public function getData() {
		$query = "SELECT * FROM ".DBPREF."book WHERE book_id = %i";
		$this->data = dibi::query($query, $this->item_id)->fetch();
		
		$this->data->authors = $this->getAuthors();

		$query = "SELECT c.codebook_id, c.value
				  FROM ".DBPREF."book_genre bg
				  LEFT JOIN ".DBPREF."codebook c ON (c.codebook_id = bg.genre_id)
				  WHERE bg.book_id = %i";
		$genres = dibi::query($query, $this->item_id)->fetchAssoc('codebook_id');
		$this->data->genres = $genres;

		$query = "SELECT c.codebook_id, c.value
				  FROM ".DBPREF."book_award ba
				  LEFT JOIN ".DBPREF."codebook c ON (c.codebook_id = ba.award_id)
				  WHERE ba.book_id = %i";
		$awards = dibi::query($query, $this->item_id)->fetchAssoc('codebook_id');
		$this->data->awards = $awards;

		$query = "SELECT c.codebook_id, c.value
				  FROM ".DBPREF."book_tag bt
				  LEFT JOIN ".DBPREF."codebook c ON (c.codebook_id = bt.tag_id)
				  WHERE bt.book_id = %i";
		$tags = dibi::query($query, $this->item_id)->fetchAssoc('codebook_id');
		$this->data->tags = $tags;

//d($return);
		return $this->data;
	}

	public function getAuthors() {
		$query = "SELECT p.people_id, p.fullname, p.url
				  FROM ".DBPREF."book_author ba
				  LEFT JOIN ".DBPREF."people p ON (p.people_id = ba.author_id)
				  WHERE ba.book_id = %i";
		$return = dibi::query($query, $this->item_id)->fetchAll();
		return $return;
	}

	public function getComments($origname, $authors, $limit = 0, $offset = 0) {
		$book_ids = $this->getEditionIds($origname, $authors);
		$query = "SELECT bc.*, u.username, b.name, b.year, c.value AS format
				  FROM ".DBPREF."book_comment bc
				  LEFT JOIN ".DBPREF."user u ON (u.user_id = bc.user_id)
				  LEFT JOIN ".DBPREF."book b ON (b.book_id = bc.book_id)
				  LEFT JOIN ".DBPREF."codebook c ON (b.format_id = c.codebook_id AND c.type = 'format')
				  WHERE bc.book_id IN (".implode(', ', $book_ids).") AND
				  		bc.status = 'A' AND
				  		(bc.subject != '' OR
				  		 bc.message != '')
				  ORDER BY bc.inserted DESC";
		if($limit) {
			$query.= " LIMIT ".$offset.", ".$limit;
		}
		$return = dibi::query($query)->fetchAll();
		return $return;
	}

	public function getListsCount() {
		$authors = [];
		foreach($this->data->authors as $a) {
			$authors[] = $a->people_id;
		}
		$ids = $this->getEditionIds($this->data->name_orig, $authors);
		$query = "SELECT COUNT(DISTINCT(user_id)) AS cnt, list_id
				  FROM ".DBPREF."user_book_list
				  WHERE book_id IN (".implode(', ', $ids).")
				  GROUP BY list_id";
		$return = dibi::query($query)->fetchAssoc('list_id');
		return $return;
	}

	public function getEditionIds($origname, $authors, $other = false, $main = false) {
		$ids = dibi::select('book_id')->from(DBPREF.'book')->where('name_orig = %s', $origname);
		if($other === true) {
			$ids->where('book_id <> %i', $this->item_id);
		}
		if($main === true) {
			$ids->where('main = %s', 'Y');
		}
		$return = [];
		foreach($ids as $row) {
			$book = new Book($row->book_id);
			$editionAuthors = [];
			foreach($book->getAuthors() as $a) {
				$editionAuthors[] = $a->people_id;
			}
			if($authors == $editionAuthors) {
				$return[] = $row->book_id;
			}
		}
		return $return;
	}

	public function update($values, $authors = null, $genres = null, $awards = null, $tags = null) {
		if(isset($values['name'])) {
			$values['url'] = self::checkUrl(Common::friendlyUrl($values['name']), $this->item_id);
		}
		dibi::update(DBPREF."book", $values)
		->where('book_id = %i', $this->item_id)
		->execute();
		
		if($authors) {
			self::setAuthors($this->item_id, $authors);
		}
		if($genres) {
			self::setGenres($this->item_id, $genres);
		}
		if($awards) {
			self::setAwards($this->item_id, $awards);
		}
		if($tags) {
			self::setTags($this->item_id, $tags);
		}
	}

	public static function isMain($mainData, $data) {
		$return = false;
		if(in_array($data['format_id'], array(15, 452)) && $data['year'] > $mainData['year']) {
			$return = true;
		}
		return $return;
	}

	public static function insert($values, $authors = null, $genres = null, $awards = null, $tags = null) {
		$values = array_merge($values, [
			'url' => self::checkUrl(Common::friendlyUrl($values['name'])),
			'inserted%sql' => 'NOW()',
		]);
		dibi::insert(DBPREF."book", $values)->execute();
		$item_id = dibi::getInsertId();

		if($authors) {
			self::setAuthors($item_id, $authors);
		}
		if($genres) {
			self::setGenres($item_id, $genres);
		}
		if($awards) {
			self::setAwards($item_id, $awards);
		}
		if($tags) {
			self::setTags($item_id, $tags);
		}

		return $item_id;
	}

	public static function delete($item_id) {
		dibi::delete(DBPREF."book")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."book_author")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."book_award")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."book_comment")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."book_genre")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."book_tag")->where('book_id = %i', $item_id)->execute();
		dibi::delete(DBPREF."user_book_list")->where('book_id = %i', $item_id)->execute();
	}

	public static function setAuthors($item_id, $authors) {
		dibi::delete(DBPREF."book_author")
		->where('book_id = %i', $item_id)
		->execute();

		foreach($authors as $author_id) {
			$values = [
				'book_id' => $item_id,
				'author_id' => $author_id,
			];
			dibi::insert(DBPREF."book_author", $values)->execute();			
		}
	}

	public static function setGenres($item_id, $genres) {
		dibi::delete(DBPREF."book_genre")
		->where('book_id = %i', $item_id)
		->execute();

		foreach($genres as $genre_id) {
			$values = [
				'book_id' => $item_id,
				'genre_id' => $genre_id,
			];
			dibi::insert(DBPREF."book_genre", $values)->execute();			
		}
	}

	public static function setAwards($item_id, $awards) {
		dibi::delete(DBPREF."book_award")
		->where('book_id = %i', $item_id)
		->execute();

		foreach($awards as $award_id) {
			$values = [
				'book_id' => $item_id,
				'award_id' => $award_id,
			];
			dibi::insert(DBPREF."book_award", $values)->execute();			
		}
	}

	public static function setTags($item_id, $tags) {
		dibi::delete(DBPREF."book_tag")
		->where('book_id = %i', $item_id)
		->execute();

		foreach($tags as $tag_id) {
			$values = [
				'book_id' => $item_id,
				'tag_id' => $tag_id,
			];
			dibi::insert(DBPREF."book_tag", $values)->execute();			
		}
	}

	public static function getBookByUrl($url) {
		$query = "SELECT book_id FROM ".DBPREF."book WHERE status = 'A' AND editation = 'N' AND url = %s";
		$return = dibi::query($query, $url)->fetchSingle();
		return $return ? $return : false;
	}

	public static function recountEvaluation($book_id) {
		$query = "SELECT AVG(stars) AS evaluation FROM ".DBPREF."book_comment WHERE book_id = %i GROUP BY user_id";
		$evaluation = dibi::query($query, $book_id)->fetchSingle();

		$values = [
			'evaluation' => $evaluation,
		];

		dibi::update(DBPREF."book", $values)
		->where('book_id = %i', $book_id)
		->execute();
	}

	private static function checkUrl($url, $book_id = 0, $iteration = 1) {
		$newurl = $iteration > 1 ? $url.'-'.$iteration : $url;
		$result = dibi::select('book_id')->from(DBPREF.'book')->where('url = %s', $newurl);
		if($book_id) {
			$result->where('book_id <> %i', $book_id);
		}
		if($result->execute()->getRowCount()) {
			$iteration++;
			$newurl = self::checkUrl($url, $book_id, $iteration);
		}
		return $newurl;
	}

}
