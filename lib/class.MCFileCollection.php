<?php
  /*
    A collection is a set of files.
  */
  class MCFileCollection
  {
    protected $collection_id;
    protected $files = array(); // Array of MCFile objects
    protected $last_file; // Last file added
    
    public function __construct($collection_id = null) {
			if(is_null($collection_id))
			{
				$collection_id = self::createUniqueCollectionId();
			}
      $this->collection_id = $collection_id;
    }
    
    public function getId() {
      return $this->collection_id;
    }
    
    public function getCollectionId() {
      return $this->collection_id;
    }
    
    public function hasFiles()  {
      return (bool)count($this->files);
    }
    
    public function getFiles()  {
      return (array) $this->files;
    }
    
    public function getFirstFile()  {
      reset($this->files);
      return current($this->files);
    }
    
		public function getLastFile()
		{
			return $this->last_file;
		}

    public function load()
    {
     // Load files 
     $this->files = MCFile::doSelect(array('where' => array('collection_id' => $this->collection_id)));
    }
    
    public function loadOne()
    {
      $this->files = MCFile::doSelect(array('where' => array('collection_id' => $this->collection_id), 'limit' => '1'));
    }
    
    public static function createUniqueCollectionId()
    {
      // TODO Check in the DB first...
      return time() . rand(0,1000);
    }
    
		public function addFile($file, $move = true)
		{
      $this->last_file = new MCFile($this->collection_id);
			
			return $this->last_file->uploadFromLocal($file, $move);
		}

    public function addUploadedFile($fieldname)
    {
      $this->last_file = new MCFile($this->collection_id);

      return $this->last_file->saveUploadedFile($fieldname);
    }
  }