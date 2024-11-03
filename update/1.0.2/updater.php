<?
$moduleId = "awz.autounp";
if(IsModuleInstalled($moduleId)) {
	$updater->CopyFiles("install/js", "js/".$moduleId);
}
?>