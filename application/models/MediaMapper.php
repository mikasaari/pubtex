<?php

class Application_Model_MediaMapper
{
	protected $_dbTable;

	public function setDbTable($dbTable)
	{
		if (is_string($dbTable)) 
		{
			$dbTable = new $dbTable();
		}

		if (!$dbTable instanceof Zend_Db_Table_Abstract) 
		{
			throw new Exception('Invalid table data gateway provided');
		}

		$this->_dbTable = $dbTable;
		return $this;
	}
 
	public function getDbTable()
	{
		if (null === $this->_dbTable) 
		{
			$this->setDbTable('Application_Model_DbTable_Media');
		}
		return $this->_dbTable;
	}
 
	public function save(Application_Model_Guestbook $media)
	{
		$data = array(
			'email'   => $media->getEmail(),
			'comment' => $guestbook->getComment(),
			'created' => date('Y-m-d H:i:s'),
		);
 
		if (null === ($id = $guestbook->getId())) 
		{
			unset($data['id']);
			$this->getDbTable()->insert($data);
		} 
		else 
		{
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}
 
	public function find($id, Application_Model_Guestbook $guestbook)
	{
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) 
		{
			return;
		}
		$row = $result->current();
		$guestbook->setId($row->id)
			->setEmail($row->email)
			->setComment($row->comment)
			->setCreated($row->created);
	}
 
	public function fetchAll()
	{
		
		$resultSet = $this->getDbTable()->fetchAll();
		$entries   = array();
		foreach ($resultSet as $row) 
		{
			$entry = new Application_Model_Guestbook();
			$entry->setId($row->id)
				->setEmail($row->email)
				->setComment($row->comment)
				->setCreated($row->created);
			$entries[] = $entry;
		}
		return $entries;
	}

	public function getNewMedia($amount)
	{
		$entries = array();

		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		// Gather $amount of media entries to view
		$query = "select media.id as media_id, files.id as files_id, files.hash_name, prefixes.prefix,tags.tag from media,files,media_file,prefixes,media_tag,tags where media.id>((select MAX(id) from media) - $amount) and media.id=media_file.media_id and media_file.file_id=files.id and prefixes.id=files.prefix and media_tag.media_id=media.id and tags.id=media_tag.tag_id union select media.id as media_id, files.id as files_id, files.hash_name, prefixes.prefix,tags.tag from media,files,media_file,prefixes,file_tag,tags where media.id>((select MAX(id) from media) - $amount) and media.id=media_file.media_id and media_file.file_id=files.id and prefixes.id=files.prefix and file_tag.file_id=files.id and tags.id=file_tag.tag_id order by media_id, files_id";
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();

		// Select is empty	
		if(empty($rows))
			return $entries;

		// First round variables
		$cm = $rows[0]['media_id'];
		$entry = new Application_Model_Media();
		$tags = array();
		$files = array();
		$names = array();

		// Go through all rows and generate Media array
		foreach($rows as $row)
		{
			if($cm != $row['media_id'])
			{
				$entry->setMediaId($row['media_id'])
				      ->setFilesIds($files)
				      ->setHashNames($names)
				      ->setTags($tags);
				$entries[] = $entry;
			
				$entry = new Application_Model_Media();
				$files = array();
				$tags = array();
				$names = array();

				$cm = $row['media_id'];
			}

			if(!in_array($row['files_id'], $files))
				$files[] = $row['files_id'];
			$tags[] = $row['tag'];
			if(!in_array($row['hash_name'], $names))
				$names[] = $row['hash_name'];
		}

		$last=end($rows);				
		$entry->setMediaId($last['media_id'])
		      ->setFilesIds($files)
		      ->setHashNames($names)
		      ->setTags($tags);
		$entries[] = $entry;

		return $entries;
	}
}

