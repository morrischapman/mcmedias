<?php
  if (!cmsms()) exit;
  
  if(isset($params['collection_id']))
  {
    $collection = new MCFileCollection($params['collection_id']);
    $collection->loadOne();
    $file = $collection->getFirstFile();
    
    if($file)
    {
      $params['file'] = $file->getRelativePath();
      echo $this->getThumbnail($params);
    }
  }