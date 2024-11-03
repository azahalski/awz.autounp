<?
$module_id = 'awz.autounp';

$arJsConfig = array(
    'awz_autounp' => array(
        'js' => '/bitrix/js/'.$module_id.'/script.js',
        'css' => '/bitrix/css/'.$module_id.'/style.css',
        'lang' => '/bitrix/modules/'.$module_id.'/lang/'.LANGUAGE_ID.'/js/js_script.php',
        'rel' => array('jquery','ajax'),
    ),
);
foreach ($arJsConfig as $ext => $arExt) {
    \CJSCore::RegisterExt($ext, $arExt);
}