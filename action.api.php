<?php
  if (!cmsms()) exit;

if($this->GetPreference('enable_api', false))
{
  if(isset($params['command']))
  {
    switch($params['command'])
    {
      case 'list':
        $selection = MCFile::doSelect();
        if(is_array($selection))
        {
          $items = MCFile::itemsToArray($selection);
          
          $callback = $_REQUEST['callback'];
          if ($callback) {
              header('Content-type: text/javascript');
            echo $callback . '(' . utf8_encode(json_encode(array('results' => $items))) . ');';
          } else {        
              header('Content-type: application/x-json');
              echo utf8_encode(json_encode(array('results' => $items)));
          }
        }
        else
        {
          header('Content-type: application/x-json');  
          echo json_encode(array('error' => 'No item found'));
        }
        exit;
        break;
    }
  }

  header('Content-type: application/x-json');  
  echo json_encode(array('error' => 'Invalid call'));
}
else
{
  header('Content-type: application/x-json');  
  echo json_encode(array('error' => 'API Not enabled'));
}