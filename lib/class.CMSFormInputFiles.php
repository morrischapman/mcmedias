<?php

  /*
    CMSForm Input Files
    
    This class is designed to handle multiple files scenario
  */
if(class_exists('CMSFormInputHidden'))
{
  class CMSFormInputFiles extends CMSFormInputHidden
  {
    public function setup($id, $name, &$form, $module_name, $settings = array())
    {
      parent::setup($id, $name, $form, $module_name, $settings);
      
      $this->setSetting('default_value', MCFileCollection::createUniqueCollectionId());
      $this->setSetting('upload_button_text', 'Select files to upload');
      
      $this->setSetting('admin_list_action', 'adminList');

			$form->setMultipartForm();
			
      return $this;
    }
    
    private static function cutUrl($origin)
    {
      list($url, $query) = explode('?', $origin);
      
      $p = explode('&amp;', $query);
      $params = array();
      
      foreach($p as $va)
      {
        list($k,$v) = explode('=',$va);
        $params[$k] = $v;
      }
      
      return array('base' => $url, 'params' => $params);
    }
    
    public function getInput()
    {     
      $input = parent::getInput();
      $collection_id = $this->getValue();
      
      $module = cms_utils::get_module('MCMedias');
      $header = $module->loadHeader();
      // $uploader = $module->GetModuleURLPath() . '/uploader.php';
      
      $url = self::cutUrl($module->CreateLink($this->id,'upload','','',array('collection_id' => $collection_id), '', true) . '&amp;suppressoutput=1&amp;showtemplate=0');
      
      $module->smarty->assign('edit', str_replace('&amp;','&', $module->CreateLink($id,'editTitle','','',array(), '', true) . '&amp;suppressoutput=1&amp;showtemplate=0'));
      
      // $config = cms_utils::get_config();
      
      $list = $module->ExecuteAction($this->getSetting('admin_list_action'), $this->id, array('collection_id' => $collection_id));
      $listurl = str_replace('&amp;', '&', $module->CreateLink($this->id, $this->getSetting('admin_list_action'), '', '', array('collection_id' => $collection_id), '', true) . '&amp;suppressoutput=1&amp;showtemplate=0');
        
      $module->smarty->assign('collection_id', $collection_id);
      $module->smarty->assign('endpoint', $url['base']);
      $module->smarty->assign('listurl', $listurl);
      $module->smarty->assign('params', json_encode($url['params']));
      $module->smarty->assign('uploadButtonText', $this->getSetting('upload_button_text'));
      $module->smarty->assign('fu_validation', $this->getSetting('validation'));      
      
      $js = $module->ProcessTemplate('list.js');
        
      $html = $header . $list . '
       <div id="fuploader_'.$collection_id.'"></div>
       <script>'.$js.'</script>
      ';
      
      // var_dump($config);
      
      return $input . $html;
    }
  }
}