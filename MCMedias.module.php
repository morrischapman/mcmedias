<?php
#-------------------------------------------------------------------------
# Module: MCMedias - Multiple files & images fields for CMSForms
# Version: 0.0.3, Jean-Christophe Cuvelier
#

class MCMedias extends CMSModule
{

  var $jquery_loaded = false;
  
  public static $frontend_templates = array(
      'default'     => 'default'
    );
  

  public function GetName()             { return 'MCMedias'; }
  public function GetFriendlyName()     { return 'M&C Medias'; }
  public function GetVersion()          { return '1.0.8'; }
  public function GetHelp()             { return $this->Lang('help'); }
  public function GetAuthor()           { return 'Jean-Christophe Cuvelier';  }
  public function GetAuthorEmail()      { return 'jcc@morris-chapman.com';  }
  // public function GetChangeLog()        { return $this->Lang('changelog');    }
  public function IsPluginModule()      { return true;  }
  public function HasAdmin()            { return true;  }
  public function GetAdminSection()     { return 'extensions';  }
  // public function GetAdminDescription()   { return $this->Lang('admindescription'); }
  public function VisibleToAdminUser()     { return $this->CheckAccess('Manage MC Medias');  }
  public function CheckAccess($perm = 'Manage MCMedias')  { return $this->CheckPermission($perm);  }

  public function GetDependencies()     { return array('CMSForms' => '1.0.3');  }
  public function MinimumCMSVersion()   { return "1.10";  }

  // function InstallPostMessage()    { return $this->Lang('postinstall');  }
  // function UninstallPostMessage()    { return $this->Lang('postuninstall');  }
  // function UninstallPreMessage()   { return $this->Lang('really_uninstall'); }
  function SetParameters()     {   $this->InitializeGlobal();  }
  
  function InitializeGlobal() {  
    $this->RegisterModulePlugin();
    
    $this->smarty->register_function('Thumbnail',  array('MCMedias','thumbnail'));
    
    $this->RegisterRoute('/mcmedias\/api\/(?P<command>[a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'api', 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));

    $this->RegisterRoute('/mcmedias\/sync\/(?P<command>[a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'sync', 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    
    $this->RegisterRoute('/thumbnail\/(?P<width>[0-9]+)\/(?P<height>[0-9]+)\/(?P<src>[.\/a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'thumbnail', 'redirect' => true,  'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    
    $this->RegisterRoute('/thumbnail\/(?P<size>[0-9]+)\/(?P<src>[.\/a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'thumbnail', 'redirect' => true, 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    
    $this->RegisterRoute('/thumbnail\/(?P<src>[.\/a-zA-Z0-9_-]+)(\/.*?)?$/', array('action' => 'thumbnail', 'redirect' => true, 'showtemplate' => 'false','returnid' => cmsms()->GetContentOperations()->GetDefaultPageID()));
    
  }
  
  public function GetHeaderHTML() {
    
    $html = '';
    // Check version
    global $CMS_VERSION;
    if(($CMS_VERSION != '') && version_compare($CMS_VERSION, '1.11', '<'))
    {
      $html .= '    <script src="'.$this->GetModuleURLPath().'/lib/js/jquery-1.8.3.js"></script>';
      $html .= '    <script src="'.$this->GetModuleURLPath().'/lib/js/jquery-ui-1.9.2.custom.min.js"></script>';
    }
    
    $html .= '
      <link href="'.$this->GetModuleURLPath().'/lib/css/admin.css" rel="stylesheet">  
      <link href="'.$this->GetModuleURLPath().'/lib/js/fineuploader/fineuploader-3.4.1.css" rel="stylesheet">  
      <link href="'.$this->GetModuleURLPath().'/lib/js/fineuploader/fineuploader-fix.css" rel="stylesheet">  
      <script src="'.$this->GetModuleURLPath().'/lib/js/fineuploader/jquery.fineuploader-3.4.1.min.js"></script>
      <script src="'.$this->GetModuleURLPath().'/lib/js/jquery.jeditable.min.js"></script>
      ';
    
    $this->jquery_loaded = true;
    
    return $html;
  }
  
  public function loadHeader()  {
    if(!$this->jquery_loaded) {
      return $this->GetHeaderHTML();
    }
    return false;
  }
  
  public static function getImage($image)
  {
    $module = cms_utils::get_module('MCMedias');
   return $module->GetModuleURLPath() . '/images/' . $image;  
  }
  
  public static function getImageTag($image, $alt='')
  {
    return '<img src="' . self::getImage($image) . '" alt="'.$alt.'"/>';
  }
  
  // EVENTS
  function HandlesEvents ()
  {
    return true;
  }
  
  public function DoEvent($originator, $eventname, &$params) {
		if (($eventname == 'CronDaily') || ($eventname == 'Cron15min')) {
			// Do desired action here
      if($this->GetPreference('remote_gallery', false))
      {
        // IMPORT
      }
      // if ($this->GetPreference('send_emails') == 'true')
      // {
      // 
      // }
		}
	}

  /**
  * Import from an external gallery
  */
  
  public function Import()
  {
     if($url = $this->GetPreference('remote_gallery', false))
      {
        $result = MCFile::SyncFromUrl($url);
      }
  }
  
  /**
  * Execute an action and return the output instead of showing it
  */
  public function ExecuteAction($name,$id,$params,$returnid='') {
    @ob_start();
    $this->DoAction($name, $id, $params,$returnid);
    $of_the_jedi = ob_get_contents();
    @ob_end_clean();
    return $of_the_jedi;
  }
  
  // TEMPLATES
  
  public function GetDefaultTemplates() {
    $array = unserialize($this->GetPreference('default_templates'));
    if (is_array($array))
    {
      return $array;
    }
    return array();
  } 
  
  public function SetDefaultTemplates($list = array())  {
    return $this->SetPreference('default_templates', serialize($list));
  }
  
  public function AddDefaultTemplate($action, $template)  {
    $list = $this->GetDefaultTemplates();
    $list[$action] = $template;
    $this->SetDefaultTemplates($list);
  }
  
  public function GetDefaultTemplate($action) {
      $list = $this->GetDefaultTemplates();
      if (!is_array($list)) $list = array();
      if (array_key_exists($action, $list)) // TODO: Possible problem with list
      {
        return $list[$action];
      }
      else
      {
        return false;
      }
  }
  
  public function isDefaultTemplate($template)  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      return $action;
    }
    return false;
  }  
  
  public function removeDefaultTemplate($template)  {    
    $list = $this->GetDefaultTemplates();
    $action = array_search($template, $list);
    if($action !== false)
    {
      unset($list[$action]);
      $this->SetDefaultTemplates($list);
    }
    return false;
  }
  
  public function ProcessTemplateFor($action, $params = array())  {
    if (isset($params['template']) && $this->GetTemplate($params['template'])) {
      return $this->ProcessTemplateFromDatabase($params['template']);
    }
    elseif (($template = $this->GetDefaultTemplate($action))  &&  ($this->GetTemplate($template) !== false))
    {
      return $this->ProcessTemplateFromDatabase($template);
    }
    else
    {
      return $this->ProcessTemplate('frontend.'.$action.'.tpl');
    }
  }

  // IMAGES PART
  
  public function getPreviewsUrl()  {
    $config = $this->getConfig();
    $relative_path = str_replace($config['root_path'], '', $config['previews_path']);
    
    return $config['root_url'] . $relative_path;
  }

  public function getThumbnail($params) {
    
    if(isset($params['src'])) $params['file'] = $params['src'];
    
    if(isset($params['size'])) { $params['width'] = (int)$params['size'];  $params['height'] = (int)$params['size']; }
    
    if(isset($params['file']))
    {
      $file = str_replace('..', '', $params['file']);
      $file = str_replace(' ', '', $file);
      $file = str_replace(';', '', $file);

      if(!isset($params['mode'])) $params['mode'] = 'default';
      if(!isset($params['width'])) $params['width'] = '100';
      if(!isset($params['height'])) $params['height'] = '100';

      $config = $this->getConfig();
      
      if(strpos($file, '/uploads/') === 0)
      {
        $origin = $config['uploads_path'] . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, str_replace('/uploads/', '', $file));
      }
      elseif(strpos($file, 'uploads/') === 0)
      {
        $origin = $config['uploads_path'] . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, str_replace('uploads/', '', $file));
      }
      else
      {
        $origin = $config['uploads_path'] . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR . 'MCMedias' . DIRECTORY_SEPARATOR . $file;
      }

      if(is_file($origin))
      {
        // BEGIN THUMBNAIL PROCESS
        $destination_url = $this->getPreviewsUrl();
        $destination_path = $config['previews_path'];
        $thumbnail_name = MCFile::generateThumbnailFilename($file, $params);
        $destination = $destination_path . DIRECTORY_SEPARATOR . $thumbnail_name;

        if(!is_file($destination))
        {
          // Generate thumbnail        
          MCFile::generateThumbnail($origin, $destination, $params);
        }
        // TEST
        // MCFile::generateThumbnail($origin, $destination, $params); // GENERATE IT ALL THE TIME

        // RETURN URL
        return $destination_url . '/' . $thumbnail_name;
      }
    }
  }
}


?>
