<?php

if (!cmsms()) exit;

/** @var MCMedias $db */
$db = $this->GetDb();
$dict = NewDataDictionary($db);

switch (true) {
    case version_compare($oldversion, '0.9', '<'):
        // $sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_mcfactory_modules', 'filters X');
        // $dict->ExecuteSQLArray($sql);
        $this->AddEventHandler('Cron', 'Cron15min', false);
        $this->AddEventHandler('Cron', 'CronHourly', false);
    case version_compare($oldversion, '1.0.11', '<'):
        $sql = $dict->AddColumnSQL(cms_db_prefix() . 'module_MCMedias_files', 'remote_ID I');
        $dict->ExecuteSQLArray($sql);
}

$this->Audit(0, $this->getFriendlyName(), $this->Lang('upgraded', $this->GetVersion()));
