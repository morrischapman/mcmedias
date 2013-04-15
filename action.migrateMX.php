<?php
  if (!cmsms()) exit;
  if (!$this->CheckAccess()) {
    return $this->DisplayErrorPage();
  }

	// Script to migrate images or documents from ModuleXtender to MCMedias
	
	// We need MCFactory
	
	if(!cms_utils::get_module('MCFactory', '3.4.9') || !cms_utils::get_module('ModuleXtender', '1.11.2	'))
	{
		echo 'You don\'t have MCFactory or ModuleXtender or the version you have is not the latest one (MCFactory 3.4.10 and ModuleXtender 1.11.2 required)';
		exit;
	}
	
	if(isset($params['cancel']))
	{
    	$this->Redirect($id, 'defaultadmin', $returnid);
	}
		
	if(isset($params['module']))
	{
		$module = MCFModuleRepository::retrieveByName($params['module']);
		$fields = $module->getFieldsWithTypes(array('images', 'files'));
		$images_fields = $module->getFieldsWithTypes(array('images'));
		$documents_fields = $module->getFieldsWithTypes(array('files'));
		
		if(isset($params['fields']))
		{
			// Start the migration
			
			$mod = cms_utils::get_module($module->getModuleName());
			// var_dump($module);
			$c = new MCFCriteria();
			$entities = $mod->doSelect($c);
			echo '<p>'.count($entities).' to update</p>';			
			$mx_entities = MX_Document::doSelectByModuleName($mod->getName());			
			// var_dump($mx_entities);
			if($entities)
			{
				foreach($entities as $entity)
				{
					echo '<p> => Update entity with id '.$entity->getId().'</p>';
					// Documents
					if(isset($mx_entities['document']))
					{
						if((count($documents_fields) > 0) && (count($mx_entities['document'][$entity->getId()]) > 0))
						{
							echo '<p> ==> '.count($mx_entities['document'][$entity->getId()]).' document(s) to import</p>';

							foreach($params['fields'] as $field)
							{
								if(isset($documents_fields[$field]))
								{
									$f = $documents_fields[$field];
									// The migration start
									echo '<p> ===> Migrate to field '.$f['name'].'</p>';
									
									if($entity->$f['name'] != '')
									{
										$collection = new MCFileCollection($entity->$f['name']);
									}
									else
									{
										$collection = new MCFileCollection();
										$entity->$f['name'] = $collection->getCollectionId();
										$entity->save();
									}
									
									foreach($mx_entities['document'][$entity->getId()] as $file)
									{
										$move = false;
										if(isset($params['move'])) { $move = true; }
										$collection->addFile($file->getDocumentPath(), $move);
										$collection->getLastFile()->setTitle($file->getTitle());
										$collection->getLastFile()->save();
									}
								}
							}
						}
					}
					// Images
					if(isset($mx_entities['image']))
					{
						if((count($images_fields) > 0) && (count($mx_entities['image'][$entity->getId()]) > 0))
						{
							echo '<p> ==> '.count($mx_entities['image'][$entity->getId()]).' image(s) to import</p>';

							foreach($params['fields'] as $field)
							{
								if(isset($images_fields[$field]))
								{
									$f = $images_fields[$field];
									// The migration start
									echo '<p> ===> Migrate to field '.$f['name'].'</p>';
									
									if($entity->$f['name'] != '')
									{
										$collection = new MCFileCollection($entity->$f['name']);
									}
									else
									{
										$collection = new MCFileCollection();
										$entity->$f['name'] = $collection->getCollectionId();
										$entity->save();
									}
									
									foreach($mx_entities['image'][$entity->getId()] as $file)
									{
										$move = false;
										// if(isset($params['move'])) { $move = true; }
										$collection->addFile($file->getDocumentPath(), $move);
										
										$collection->getLastFile()->setTitle($file->getTitle());
										$collection->getLastFile()->save();
									}
								}
							}
						}
					}
				
				}
				
				echo '<p>Your files has been migrated. Don\'t forget to delete them form the server uploads directory.</p>';
			}
			
			
			
		}
		else
		{
			$form = new CMSForm('MCMedias', $id, 'migrateMX', $returnid);
			$form->setWidget('module', 'hidden');
		
			$choice = array();
			
			foreach($fields as $field)
			{
				$choice[$field['name']] = $field['name'] . ' ('.$field['type'].')';
			}

			$form->setWidget('fields', 'select', array('values' => $choice, 'multiple' => true, 'expanded' => true, 'tips' => $this->lang('form_select_fields_to_populate')));

			$form->setLabel('submit', $this->lang('Continue'));			
			echo $form->render();
		}
	}
	else
	{
		$modules = MCFModuleRepository::getModulesWithFieldTypes(array('images','files'));
		
		if(count($modules) > 0)
		{
			$form = new CMSForm('MCMedias', $id, 'migrateMX', $returnid);
			
			$modules_name = array();
			foreach($modules as $module)
			{
				$modules_name[$module->getModuleName()] = $module->getModuleFriendlyName();
			}
			
			$form->setWidget('module', 'select', array('values' => $modules_name, 'tips' => $this->lang('form_select_module_for_migration')));
			
			$form->setLabel('submit', $this->lang('Continue'));
			
			echo $form->render();
		}
  	
	}