<?php
  header('Content-type: text/plain');
  if (!cmsms()) exit;
  if (!$this->CheckAccess()) {
    return $this->DisplayErrorPage();
  }

  if(isset($params['file_id']))
  {
    $file = MCFile::doSelectOne(array('where' => array('id' => $params['file_id'])));
        
    if(!is_null($file))
    {
      $file->delete();
      echo json_encode(array('success' => true));
      exit;
    }
  }
  echo json_encode(array('error' => true));