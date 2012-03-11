<?php

class Application_Model_AjaxMapper
{
	/*
        // Generate tag list
        $str = "(";
        foreach($_GET as $value)
        {
                $str = $str + "'" +$value +"'";
                if($value != end($_GET))
                        $str = $str + ", ";
        }
        $str = $str + ")";
        $size = $_GET.length;

        // Generate string

        // Request from SQL
        echo $stmt;
	*/

	protected $_dbTable;
	
	public function setDbTable($table)
	{
		if(is_string($dbTable))
		{
			$dbTable = new $dbTable();			
		}

		if(!$dbTable instanceof Zend_Db_Table_Abstract)
		{
			throw new Exception('Invalid table data gateway provided');
		}

		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable()
	{
		if(null === $this->_dbTable)
		{
			$this->setDbTable('Application_Model_DbTable_AjaxImage');		
		}
		return $this->_dbTable;
	}

	public function getTaggedImages($tags, $having, $size)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		if($having == "")
		{
			$query = "select media_file.media_id, media_file.file_id,files.hash_name from media_file,files,(select media_id, count(media_id) as amount from (select media.id as media_id from media join media_tag on id = media_id join tags on tag_id=tags.id where tags.tag in $tags union all select media.id as media_id from media join media_file on media.id=media_file.file_id join files on files.id=file_id join file_tag on files.id=file_tag.file_id join tags on tag_id=tags.id where tags.tag in $tags) as tbl group by media_id having amount=$size) as tbl2 where tbl2.media_id=media_file.media_id and files.id=media_file.file_id";
		}
		else if ($tags == "" and $having != "")
		{
			$query = "select media_file.media_id, media_file.file_id,files.hash_name from media_file,files,(select media_id, count(media_id) as amount from (select media.id as media_id from media join media_tag on id = media_id join tags on tag_id=tags.id where tags.tag like '$having%' union all select media.id as media_id from media join media_file on media.id=media_file.file_id join files on files.id=file_id join file_tag on files.id=file_tag.file_id join tags on tag_id=tags.id where tags.tag like '$having%') as tbl group by media_id having amount=1) as tbl2 where tbl2.media_id=media_file.media_id and files.id=media_file.file_id";
		}
		else
		{
			$query="select media_id, SUM(amount) as amount, hash_name from (select tbl1.media_id, amount, files.hash_name from (select media_id, count(media_id) as amount from media_tag join tags on tags.id=media_tag.tag_id where tags.tag in $tags group by media_id having amount=".($size-1).") as tbl1, files, media_file where tbl1.media_id=media_file.media_id and media_file.file_id=files.id union all select tbl1.media_id, amount, files.hash_name from (select media_id, count(media_id) as amount from media_tag join tags on tags.id=media_tag.tag_id where tags.tag like '$having%' group by media_id) as tbl1, files, media_file where tbl1.media_id=media_file.media_id and media_file.file_id=files.id union all select media_id, count(media_id) as amount, files.hash_name from media_file join files on media_file.file_id=files.id join file_tag on files.id=file_tag.file_id join tags on file_tag.tag_id=tags.id where tags.tag in $tags union all select media_id, count(media_id), files.hash_name as amount from media_file join files on media_file.file_id=files.id join file_tag on files.id=file_tag.file_id join tags on file_tag.tag_id=tags.id where tags.tag like '$having%') as tbl3 group by media_id having amount >=$size";

		}

		$stmt = $db->query($query);
		return $stmt->fetchAll();
	}

	public function getLatestImages($amount)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		
		$query = "select files.hash_name from files where id>=((select MAX(id) from files)-$amount)";

		$stmt = $db->query($query);
		return $stmt->fetchAll();
	}

	public function getTagsWithName($name)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();

		$query = "select tags.id as id, tags.tag as name from tags where tags.tag like '%$name%'";
		$stmt = $db->query($query);

		return $stmt->fetchAll();
	}
}

