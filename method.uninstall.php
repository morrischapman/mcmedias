<?php

if (!cmsms()) exit;

$db = $this->GetDb();

$dict = NewDataDictionary($db);

$sql = $dict->DropTableSQL(cms_db_prefix() . 'module_MCMedias_files');
$dict->ExecuteSQLArray($sql);

$this->RemovePermission('Manage MCMedias');
$this->RemoveEventHandler('Cron', 'Cron15min');

$this->RemovePreference();
    
$this->Audit(0, $this->getFriendlyName(), $this->Lang('uninstalled'));

?>