<?php

class Application_Model_MediaDetailsMapper
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
 
	public function save(Application_Model_MediaDetails $media)
	{
	}
 
	public function find(Application_Model_MediaDetails $media, $filehash)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
	
		// Select media details
		//$query = "select media.*, types.name as type from media join types on media.media_type=types.id where hash_name='$mediahash'";
		$query = "select media.*, types.name as type from files join media_file on files.id=media_file.file_id join media on media_file.media_id=media.id join types on media.media_type=types.id where files.hash_name='$filehash'";
		$stmt = $db->query($query);
		$medr = $stmt->fetchAll();

		$mediahash = $medr[0]['hash_name'];

		// Select media tags
		$query = "select tags.id, tags.tag from media join media_tag on media.id=media_tag.media_id join tags on media_tag.tag_id=tags.id where media.hash_name='$mediahash'";
		$stmt = $db->query($query);
		$tagr = $stmt->fetchAll();

		// Select user info
		$query = "select users.username from media join users on media.user_id=users.id where hash_name='$mediahash'";
		$stmt = $db->query($query);
		$user = $stmt->fetchAll();
		
		$medinfo = $medr[0];

		$media->setHashName($medinfo['hash_name'])
			->setType($medinfo['type'])
			->setDescription($medinfo['description'])
			->setId($medinfo['id'])
			->setUser($user[0]['username'])
			->setCreated($medinfo['created'])
			->setTags($tagr);

		// Select all files and tags
		$medianame = $medinfo['hash_name'];
		$query = "select files.id, files.hash_name from media join media_file on media.id=media_file.media_id join files on media_file.file_id=files.id where media.hash_name='$medianame'";
		$stmt = $db->query($query);
		$filer = $stmt->fetchAll();

		foreach($filer as $file)
		{
			
			$filesid = $file['id'];
			$filesname = $file['hash_name'];
			$query = "select tags.tag from file_tag join tags on file_tag.tag_id=tags.id where file_tag.file_id=$filesid";
			$stmt = $db->query($query);
			$tagfr = $stmt->fetchAll();
			$media->addFile($filesname, $tagfr);
		}		
	}
 
	public function fetchAll()
	{
	}

}

