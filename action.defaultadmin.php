<?php
  if (!cmsms()) exit;
  if (!$this->CheckAccess('Modify Templates')) {
    return $this->DisplayErrorPage();
  }

  echo $this->StartTabHeaders();
  echo $this->SetTabHeader('templates', $this->Lang('tab_templates'),false);
  echo $this->SetTabHeader('options', $this->Lang('tab_options'),(isset($params['option_tab']))?true:false);
  echo $this->EndTabHeaders();
  
  echo $this->StartTabContent();
  
  
  // TEMPLATES
  echo $this->StartTab('templates');

  $list_templates = $this->ListTemplates();
  $templates = array();
  foreach($list_templates as $template) {
  	$row = array(
  		'titlelink' => $this->CreateLink($id, 'template_edit', $returnid, $template, array('template' => $template), '', false, false, 'class="itemlink"'),
  		'deletelink' => $this->CreateLink($id, 'template_delete', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/delete.gif', $this->Lang('delete'), '', '', 'systemicon'), array('template' => $template), $this->lang('are you sure you want to delete this template')),
  		'editlink' => $this->CreateLink($id, 'template_edit', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/edit.gif', $template, '', '', 'systemicon'), array('template' => $template))
  	);

  	if ($this->isDefaultTemplate($template) !== false)
  	{
  		$row['default'] = $this->lang('default template for', $this->isDefaultTemplate($template));
  	}
  	else
  	{
  		$row['default'] = '';
  	}

  	$templates[] = $row;
  }
  $this->smarty->assign('templates', $templates);
  $this->smarty->assign('add_templates_link', $this->CreateLink($id, 'template_edit', $returnid, $this->Lang('add template')));
  $this->smarty->assign('add_templates_icon', $this->CreateLink($id, 'template_edit', $returnid, cmsms()->get_variable('admintheme')->DisplayImage('icons/system/newobject.gif', $this->Lang('add template'), '', '', 'systemicon')));

  echo $this->ProcessTemplate('admin.templates.tpl');
  echo $this->EndTab();

  echo $this->StartTab('options');
  
  $form = new CMSForm('MCMedias', $id, 'defaultadmin', $returnid);
  
  $form->setWidget('option_tab', 'hidden', array('value' => 1));

	$sync_url = 	$api_url = $this->config->smart_root_url() . '/mcmedias/sync/all';
  $form->setWidget('remote_gallery', 'text', array('preference' => 'remote_gallery', 'tips' => '<a href="'.$sync_url.'" ref="external" target="_new">Sync</a>'));

	$api_url = $this->config->smart_root_url() . '/mcmedias/api/list';
  $form->setWidget('enable_api', 'checkbox', array('preference' => 'enable_api', 'tips' => '<a href="'.$api_url.'" ref="external" target="_new">'.$api_url.'</a>'));
  
  if($form->isPosted())
  {
    $form->process();
    if($form->noError())
    {
      //
    }
  }
  
  echo $form->render();
  
  echo $this->EndTab();


  echo $this->EndTabContent();

	if(cms_utils::get_module('ModuleXtender'))
	{
		echo '<div style="margin-top: 15px;"><p>'.$this->CreateLink($id, 'migrateMX', $returnid, 'Migrate content from ModuleXtender', array(), '', false, true, ' class="pageback ui-state-default ui-corner-all"').'</p></div>';
	}