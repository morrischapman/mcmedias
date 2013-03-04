<?php

if (!cmsms()) exit;
// 
// $db = $this->GetDb();
// $dict = NewDataDictionary($db);

switch(true) {
  case version_compare($oldversion, '0.9', '<'):
    // $sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_mcfactory_modules', 'filters X');
    // $dict->ExecuteSQLArray($sql);
    $this->AddEventHandler('Cron', 'Cron15min', false);
    $this->AddEventHandler('Cron', 'CronHourly', false);
}

$this->Audit(0, $this->getFriendlyName(), $this->Lang('upgraded', $this->GetVersion()));
