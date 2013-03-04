<?php
  header('Content-type: text/plain');
  
  
  if (!cmsms()) {
    echo json_encode(array('success' => false));
    exit;
  }
  elseif(isset($params['collection_id']))
  {
    $collection = new MCFileCollection($params['collection_id']);
    
    // @ob_start();
    // var_dump($_FILES);
    // var_dump($_REQUEST);
    // $_SESSION['upload_result'] = ob_get_contents();
    // @ob_end_clean();
    
    $result = $collection->addUploadedFile('qqfile');
           
    echo $result;
    // if($result)
    // {
    //   echo json_encode(array('success' => true));
    // }
    // else
    // {
    //    echo json_encode(array('success' => false, 'error' => 'An error occurred during the upload', 'preventRetry' => true));
    // }
    exit;
  }
  else
  {
    echo json_encode(array('success' => false, 'error' => 'The collection key is missing', 'preventRetry' => true));
  }
  
  // echo json_encode(array('success' => true));
  