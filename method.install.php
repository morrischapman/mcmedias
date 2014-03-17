<?php
if (!cmsms()) exit;

$db = $this->GetDb();
$dict = NewDataDictionary($db);

$flds = array(
	'id I KEY AUTO',
    'remote_ID I',
	'collection_id C(255)',
	'created_at I',
	'created_by I',
	'updated_at I',
	'updated_by I',
	'title C(255)',
	'position I',
	'filename	XL',
	'original_filename	XL'
);

$sql = $dict->CreateTableSQL(cms_db_prefix() . 'module_MCMedias_files', implode(',', $flds), array('mysql' => 'TYPE=MyISAM'));
$dict->ExecuteSQLArray($sql);

// $this->SetPreference('uploads_path', $this->config['uploads_path']);

$this->CreatePermission('Manage MCMedias', 'Manage MC Medias');

// $cron = cms_utils::get_module('Cron');
// $cron->CreateEvent('Cron15min');

$this->AddEventHandler('Cron', 'Cron15min', false);
$this->AddEventHandler('Cron', 'CronHourly', false);
$this->Audit(0, $this->GetName(), $this->Lang('installed', $this->GetVersion()));
