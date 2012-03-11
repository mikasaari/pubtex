<?php

class Application_Model_Media
{
	protected $_mediaid;
	protected $_filesids = array();
	protected $_hashnames = array();
	protected $_prefix;
	protected $_tags = array();

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
	
	public function setMediaId($id)
	{
		$this->_mediaid = $id;
		return $this;
	}

	public function getMediaId()
	{
		return $this->_mediaid;
	}


	public function setFilesIds(array $ids)
	{
		$this->_filesids = $ids;
		return $this;
	}

	public function getFilesIds()
	{
		return $this->_filesids;
	}	

	
	public function setHashNames(array $names)
	{
		$this->_hashnames = $names;
		return $this;
	}

	public function getHashNames()
	{
		return $this->_hashnames;
	}


	public function setPrefix($prefix)
	{
		$this->_prefix = (string) $prefix;
		return $this;
	}

	public function getPrefix()
	{
		return $this->_prefix;
	}

	public function setTags(array $tags)
	{
		$this->_tags = $tags;
		return $this;
	}

	public function getTags()
	{
		return $this->_tags;
	}
}

