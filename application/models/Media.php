<?php

class Application_Model_Media
{
	protected $_hashname;
	protected $_type;
	protected $_description;
	protected $_id;
	protected $_user;
	protected $_created;
	protected $_tags = array();
	protected $_files = array();
	
	public function __construct(array $options = null)
	{
		if(is_array($options))
		{
			$this->setOptions($options);
		}
	}

	public function __set($name, $value)
	{
		$method = 'set' . $name;

		if(('mapper' == $name) || !method_exists($this, $method))
		{
			throw new Exception('Invalid media property');
		}
		$this->$method($value);
	}

	public function __get($name)
	{
		$method = 'get' . $name;
		if(('mapper' == $name) || !method_exists($this, $method))
		{
			throw new Exception('Invalid media property');
		}
		return $this->$method();
	}

	public function setOptions(array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) 
		{
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) 
			{
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setHashName($hashname)
	{
		$this->_hashname = $hashname;
		return $this;
	}

	public function getHashName()
	{
		return $this->_hashname;
	}

	public function setType($type)
	{
		$this->_type = $type;
		return $this;
	}

	public function getType()
	{
		return $this->_type;
	}

	public function setId($id)
	{
		$this->_id = $id;
		return $this;
	}

	public function getId()
	{
		return $this->_id;
	}

	public function setUser($user)
	{
		$this->_user = $user;
		return $this;
	}
	
	public function getUser()
	{
		return $this->_user;
	}

	public function setCreated($created)
	{
		$this->_created = $created;
		return $this;
	}

	public function getCreated()
	{
		return $this->_created;
	}

	public function setDescription($description)
	{
		$this->_description = $description;
		return $this;
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setTags(array $tags)
	{
		foreach($tags as $tag)
		{
			$this->_tags[$tag['id']] = $tag['tag'];
		}
	}

	public function getTags()
	{
		return $this->_tags;	
	}

	public function addFile($file, array $tags)
	{
		// Create array of tags
		$tagst = array();
		foreach($tags as $tag)
		{
			error_log("TAG: ".$tag['id']);
			$tagst[$tag['id']] = $tag['tag'];
		}

		// Add to the array
		$this->_files[$file] = $tagst; 		
	}

	public function getFiles()
	{
		return $this->_files;
	}
}

