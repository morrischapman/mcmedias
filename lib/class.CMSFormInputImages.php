<?php

  /*
    CMSForm Input Images
    
    This class is designed to handle multiple files scenario
  */
if(class_exists('CMSFormInputFiles'))
{
  class CMSFormInputImages extends CMSFormInputFiles
  {
    public function setup($id, $name, &$form, $module_name, $settings = array())
    {
      parent::setup($id, $name, $form, $module_name, $settings);
      
      $this->setSetting('upload_button_text', 'Select images to upload');
      
      $this->setSetting('admin_list_action', 'adminPictureList');
      
      $this->setSetting('validation', '
      validation: {
              allowedExtensions: [\'jpeg\', \'jpg\', \'gif\', \'png\']
              // ,sizeLimit: 51200 // 50 kB = 50 * 1024 bytes
            },');

      return $this;
    }
  }
}