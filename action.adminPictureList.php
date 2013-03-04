<?php
  if (!cmsms()) exit;
  if (!$this->CheckAccess()) {
    return $this->DisplayErrorPage();
  }

  if(isset($params['collection_id']))
  {
    $collection = new MCFileCollection($params['collection_id']);
    $collection->load();
    
    $this->smarty->assign('id',$id);
    $this->smarty->assign('collection', $collection);
    
    $this->smarty->assign('delete', $this->CreateLink($id,'delete','','',array(), '', true));
    $this->smarty->assign('delete_icon', $this->getImageTag('bin_closed.png', 'Delete'));
    
    $this->smarty->assign('edit', str_replace('&amp;','&', $this->CreateLink($id,'editTitle','','',array(), '', true) . '&amp;suppressoutput=1&amp;showtemplate=0'));
    $this->smarty->assign('sort', str_replace('&amp;','&', $this->CreateLink($id,'sort','','',array(), '', true) . '&amp;suppressoutput=1&amp;showtemplate=0'));
    
    echo $this->ProcessTemplate('admin.pictureList.tpl');
  }
  
