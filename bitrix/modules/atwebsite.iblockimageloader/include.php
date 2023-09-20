<?
IncludeModuleLangFile(__FILE__);

$moduleId = 'atwebsite.iblockimageloader';

$pathLang = BX_ROOT.'/modules/'.$moduleId.'/lang/'.LANGUAGE_ID;
CModule::AddAutoloadClasses(
	$moduleId,
	array(
		'CAllIblockImageLoader' => 'classes/general/IbImageLoader.php',
	)
);

?>