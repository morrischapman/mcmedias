<?php
  if (!cmsms()) exit;
  
  if(isset($params['collection_id']))
  {
    $collection = new MCFileCollection($params['collection_id']);
    $collection->load();
    
    $this->smarty->assign('collection', $collection);
    
    // $params = array();
    
  }
  elseif(isset($params['collection_ids']))
  {
    $collection_ids = explode(',', $params['collection_ids']);
    $collections = array();
    foreach($collections_id as $collection_id)
    {
      $collections[$collection_id] = new MCFileCollection($collection_id);
    }
    
    $this->smarty->assign('collections', $collections);
  }

  $this->smarty->assign('mcmedias_params', $params);
  
  echo $this->ProcessTemplateFor('default', $params);