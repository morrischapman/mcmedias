<?php
header('Content-type: text/plain');
if (!cmsms()) exit;
if (!$this->CheckAccess()) {
  return $this->DisplayErrorPage();
}

if(isset($params['media_id']))
{
  $media_id = str_replace('media_', '', $params['media_id']);
  
  $media = MCFile::retrieveByPk($media_id);
  
  if($media)
  {
    $media->setTitle($params['value']);
    $media->save();
    echo $params['value'];
  }
  else
  {
    echo 'An error occured: We cannot find this media. Try again.';
  }
}
else
{
    echo 'An error occured: No media ID defined. Try again.';
    // var_dump($params);
}