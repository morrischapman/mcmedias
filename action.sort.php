<?php
  header('Content-type: text/plain');
  if (!cmsms()) exit;
  if (!$this->CheckAccess()) {
    return $this->DisplayErrorPage();
  }
  
  if(isset($params['media_id']) && is_array($params['media_id']))
  {
    foreach($params['media_id'] as $position => $media_id)
    {
      // echo $position;
     $file = MCFile::retrieveByPk($media_id);
     $file->setPosition($position+1);
     $file->save();
    }  

    echo json_encode(array('success' => true));
    // exit;
    
  }
  
  echo json_encode(array('error' => true));