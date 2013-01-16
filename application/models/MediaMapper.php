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
	}

	public function find($id, Application_Model_Guestbook $media)
	{
	}
 
	public function fetchAll()
	{
	}

	public function getNewMedia($amount, $start)
	{
		// Our final entries
		$entries = array();

		// Database Adapter
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		// Calculate the amount
		$bigger = $start-1-$amount;
		if($bigger < 1)
			$bigger = 0;

		// Create the filter
		if($start == 0)
			$filter = " order by(id) desc limit $amount";
		else
			$filter = " media.id<$start order by(id) desc limit $amount";

		if($start == 0)
		{
			$query = "select media.*, types.name as type from media join types on media.media_type=types.id" . $filter;
		}
		else
		{
			$query = "select media.*, types.name as type from media join types on media.media_type=types.id where" . $filter;
		}
		error_log("NEW QUERY $query");
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();

		// Select is empty	
		if(empty($rows))
			return $entries;

		// Create array of Media instances
		foreach($rows as $row)
		{
			$media = new Application_Model_Media();

			// Select media tags
			$mediaid = $row['id'];
			$query = "select tags.id, tags.tag from media_tag join tags on tags.id=media_tag.tag_id where media_tag.media_id=$mediaid";
			$stmt = $db->query($query);
			$tagr = $stmt->fetchAll();

			// Select user info
			$query = "select users.username from media join users on media.user_id=users.id where media.id=$mediaid";
			$stmt = $db->query($query);
			$user = $stmt->fetchAll();


			// Create media
			$media->setHashName($row['hash_name'])
			      ->setType($row['type'])
						->setDescription($row['description'])
			      ->setId($row['id'])
			      ->setUser($user[0]['username'])
			      ->setCreated($row['created'])
			      ->setTags($tagr);

			// Select all media files
			$query = "select files.id, files.hash_name from media  join media_file on media.id=media_file.media_id join files on media_file.file_id=files.id where media.id=$mediaid";
			$stmt = $db->query($query);
			$mfiles = $stmt->fetchAll();

			// Add all files to the media
			foreach($mfiles as $file)
			{			
				$fileid = $file['id'];
				$filesname = $file['hash_name'];
				$query = "select tags.id, tags.tag from file_tag join tags on file_tag.tag_id=tags.id where file_tag.file_id=$fileid";
				$stmt = $db->query($query);
				$tagfr = $stmt->fetchAll();
				$media->addFile($filesname, $tagfr);
			}

			// Add the media to the entries array
			$entries[] = $media;
		}

		return $entries;
	}

	public function getMediaDetails($hash)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		// Create one media object
		$media = new Application_Model_Media();

		// Gather $amount of media entries to view
		$query = "select media.*, types.name as type from media join types on media.media_type=types.id where media.hash_name=\"$hash\"";
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();
		error_log("QUERY: $query");

		// Select is empty	
		if(empty($rows))
		{
			return $media;
		}
		else
			$row = $rows[0];
			

		// Select media tags
		$mediaid = $row['id'];
		$query = "select tags.id, tags.tag from media_tag join tags on tags.id=media_tag.tag_id where media_tag.media_id=$mediaid";
		$stmt = $db->query($query);
		$tagr = $stmt->fetchAll();

		// Select user info
		$query = "select users.username from media join users on media.user_id=users.id where media.id=$mediaid";
		$stmt = $db->query($query);
		$user = $stmt->fetchAll();

		// Create media
		$media->setHashName($row['hash_name'])
		      ->setType($row['type'])
					->setDescription($row['description'])
		      ->setId($row['id'])
		      ->setUser($user[0]['username'])
		      ->setCreated($row['created'])
		      ->setTags($tagr);

		// Select all media files
		$query = "select files.id, files.hash_name from media  join media_file on media.id=media_file.media_id join files on media_file.file_id=files.id where media.id=$mediaid";
		$stmt = $db->query($query);
		$mfiles = $stmt->fetchAll();

		// Add all files to the media
		foreach($mfiles as $file)
		{			
			$fileid = $file['id'];
			$filesname = $file['hash_name'];
			$query = "select tags.id, tags.tag from file_tag join tags on file_tag.tag_id=tags.id where file_tag.file_id=$fileid";
			$stmt = $db->query($query);
			$tagfr = $stmt->fetchAll();
			$media->addFile($filesname, $tagfr);
		}

		return $media;
	}

	public function getTaggedImages($tags, $having, $size, $amount=20, $start=0)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		// Calculate the amount
		$bigger = $start-1-$amount;
		if($bigger < 1)
			$bigger = 0;

		// Create the filter
		if($start == 0)
			$filter = " order by(media_id) desc limit $amount";
		else
			$filter = " and media_file.media_id<$start order by(media_id) desc limit $amount";

		// Create the query string with the filter
		if($having == "")
		{
			$query = "select media_file.media_id, media_file.file_id,files.hash_name from media_file,files,(select media_id, count(media_id) as amount from (select media.id as media_id from media join media_tag on id = media_id join tags on tag_id=tags.id where tags.tag in $tags union all select media.id as media_id from media join media_file on media.id=media_file.file_id join files on files.id=file_id join file_tag on files.id=file_tag.file_id join tags on tag_id=tags.id where tags.tag in $tags) as tbl group by media_id having amount=$size) as tbl2 where tbl2.media_id=media_file.media_id and files.id=media_file.file_id" . $filter;
		}
		else if ($tags == "" and $having != "")
		{
			$query = "select media_file.media_id, media_file.file_id,files.hash_name from media_file,files,(select media_id, count(media_id) as amount from (select media.id as media_id from media join media_tag on id = media_id join tags on tag_id=tags.id where tags.tag like '$having%' union all select media.id as media_id from media join media_file on media.id=media_file.file_id join files on files.id=file_id join file_tag on files.id=file_tag.file_id join tags on tag_id=tags.id where tags.tag like '$having%') as tbl group by media_id having amount=1) as tbl2 where tbl2.media_id=media_file.media_id and files.id=media_file.file_id" . $filter;
		}
		else
		{
			if($start != 0)
				$filter = " where media_file.media_id<$start order by(media_id) desc limit $amount";
			
			$query="select media_id, SUM(amount) as amount, hash_name from (select tbl1.media_id, amount, files.hash_name from (select media_id, count(media_id) as amount from media_tag join tags on tags.id=media_tag.tag_id where tags.tag in $tags group by media_id having amount=".($size-1).") as tbl1, files, media_file where tbl1.media_id=media_file.media_id and media_file.file_id=files.id union all select tbl1.media_id, amount, files.hash_name from (select media_id, 1 as amount from media_tag join tags on tags.id=media_tag.tag_id where tags.tag like '$having%' group by media_id) as tbl1, files, media_file where tbl1.media_id=media_file.media_id and media_file.file_id=files.id union all select media_id, count(media_id) as amount, files.hash_name from media_file join files on media_file.file_id=files.id join file_tag on files.id=file_tag.file_id join tags on file_tag.tag_id=tags.id where tags.tag in $tags union all select media_id, count(media_id), files.hash_name as amount from media_file join files on media_file.file_id=files.id join file_tag on files.id=file_tag.file_id join tags on file_tag.tag_id=tags.id where tags.tag like '$having%') as tbl3 group by media_id having amount >=$size" . $filter;

		}

		error_log($query);

		// Query the SQL with correct SQL statement		
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();

		// Create array of Media objects
		$entries = array();

		// Select is empty	
		if(empty($rows))
			return $entries;

		foreach($rows as $row)
		{
			// Media to be nothing
			$media = NULL;

			// Media in array already or not
			if(array_key_exists($row['media_id'], $entries))
			{
				$media = $entries[$row['media_id']];
			}
			else
			{
				$media = new Application_Model_Media();

				// Select media tags
				$mediaid = $row['media_id'];
				$query = "select tags.id, tags.tag from media_tag join tags on tags.id=media_tag.tag_id where media_tag.media_id=$mediaid";
				$stmt = $db->query($query);
				$tagr = $stmt->fetchAll();

				// Select user info
				$query = "select users.username from media join users on media.user_id=users.id where media.id=$mediaid";
				$stmt = $db->query($query);
				$user = $stmt->fetchAll();

				// Select other media info
				$query = "select media.*, types.name as type from media join types on media.media_type=types.id where media.id=$mediaid";
				$stmt = $db->query($query);
				$tmpmediainfo = $stmt->fetchAll();
				$mediainfo = $tmpmediainfo[0];

				// Create media
				$media->setHashName($mediainfo['hash_name'])
				      ->setType($mediainfo['type'])
							->setDescription($mediainfo['description'])
				      ->setId($mediainfo['id'])
				      ->setUser($user[0]['username'])
				      ->setCreated($mediainfo['created'])
				      ->setTags($tagr);

				// Select all media files
				//$query = "select files.id, files.hash_name from media join media_file on media.id=media_file.media_id join files on media_file.file_id=files.id where media.id=$mediaid";
				//$stmt = $db->query($query);
				//$mfiles = $stmt->fetchAll();		
			}

			$file_hash = $row['hash_name'];
			$query = "select id, hash_name from files where hash_name='$file_hash'";
			error_log("QUERY 1: $query");
			$stmt = $db->query($query);
			$mfile = $stmt->fetchAll();
			$file = $mfile[0];

			// Add file to media		
			$fileid = $file['id'];
			$filesname = $file['hash_name'];
			$query = "select tags.id, tags.tag from file_tag join tags on file_tag.tag_id=tags.id where file_tag.file_id=$fileid";
			$stmt = $db->query($query);
			$tagfr = $stmt->fetchAll();
			$media->addFile($filesname, $tagfr);

			error_log("Added file ".$filesname." to media_id ".$media->getId());

			// Add the media to the entries array
			$mykey = $media->getId();
			error_log("KEY 1: $mykey");
			$entries[$mykey] = $media;
		}

		// Return array of objects
		return $entries;
	}

	function getFilePrefix($hash)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		// Gather $amount of media entries to view
		$query = "select * from files where hash_name=\"$hash\"";
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();
		error_log("QUERY: $query");
		
		// There should be only one row
		if(count($rows) != 1)
		{
			return "";
		}

		// Get prefix name from prefixes table
		$prefixId = $rows[0]['prefix'];
		$query = "select prefix from prefixes where id=$prefixId";
		$stmt = $db->query($query);
		$rows = $stmt->fetchAll();
	
		if(count($rows) != 1)
		{
			return "";
		}

		// Everything OK
		return $rows[0]['prefix'];	
	}
}

