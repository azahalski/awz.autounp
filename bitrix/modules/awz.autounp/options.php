<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);
global $APPLICATION;
$module_id = "awz.autounp";
$MODULE_RIGHT = $APPLICATION->GetGroupRight($module_id);
$zr = "";
if (! ($MODULE_RIGHT >= "R"))
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));

$APPLICATION->SetTitle(Loc::getMessage('AWZ_AUTOUNP_OPT_TITLE'));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

Loader::includeModule($module_id);

$siteRes = SiteTable::getList(['select'=>['LID','NAME'],'filter'=>['ACTIVE'=>'Y']])->fetchAll();
$context = Application::getInstance()->getContext();
$request = $context->getRequest();

if ($request->isPost() && $MODULE_RIGHT == "W" && strlen($request->get("Update")) > 0 && check_bitrix_sessid())
{
    $shows = $request->get('SHOW');
    $FIELD_UNP = $request->get('FIELD_UNP');
    $FIELD_MAME1 = $request->get('FIELD_MAME1');
    $FIELD_MAME2 = $request->get('FIELD_MAME2');
    $FIELD_UR_ADRESS = $request->get('FIELD_UR_ADRESS');
    $PAGES = $request->get('PAGES');
    if(!is_array($shows)) $shows = [];
    foreach($siteRes as $arSite){
        if(!isset($shows[$arSite['LID']]) || !$shows[$arSite['LID']]) {
            $shows[$arSite['LID']] = 'N';
        }
        Option::set($module_id, 'SHOW', $shows[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'FIELD_UNP', $FIELD_UNP[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'FIELD_MAME1', $FIELD_MAME1[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'FIELD_MAME2', $FIELD_MAME2[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'FIELD_UR_ADRESS', $FIELD_UR_ADRESS[$arSite['LID']], $arSite['LID']);
        Option::set($module_id, 'PAGES', $PAGES[$arSite['LID']], $arSite['LID']);
    }
}

$aTabs = array();

$aTabs[] = array(
    "DIV" => "edit1",
    "TAB" => Loc::getMessage('AWZ_AUTOUNP_OPT_SECT1'),
    "ICON" => "vote_settings",
    "TITLE" => Loc::getMessage('AWZ_AUTOUNP_OPT_SECT1')
);

$aTabs[] = array(
    "DIV" => "edit3",
    "TAB" => Loc::getMessage('AWZ_AUTOUNP_OPT_SECT2'),
    "ICON" => "vote_settings",
    "TITLE" => Loc::getMessage('AWZ_AUTOUNP_OPT_SECT2')
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>
    <style>.adm-workarea option:checked {background-color: rgb(206, 206, 206);}</style>
    <form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialcharsbx($module_id)?>&lang=<?=LANGUAGE_ID?>&mid_menu=1" id="FORMACTION">

        <?
        $tabControl->BeginNextTab();
        \Bitrix\Main\UI\Extension::load("ui.alerts");
        ?>
        <tr>
            <td colspan="2">
                <div class="ui-alert ui-alert-primary">
                    <span class="ui-alert-message">
                        <?=Loc::getMessage('AWZ_AUTOUNP_OPT_SHOW_DESC')?>.
                    </span>
                </div>

            </td>
        </tr>
        <?
        foreach($siteRes as $arSite){

            $allProps = [];
            $candidatesInn = 0;
            if(Loader::includeModule('sale')){
                $r = \Bitrix\Sale\Internals\OrderPropsTable::getList([
                    'filter'=>['=ACTIVE'=>'Y','=PERSON_TYPE.PERSON_TYPE_SITE.SITE_ID'=>$arSite['LID']],
                    'select'=>['ID','NAME','CODE','PERSON_TYPE_NAME'=>'PERSON_TYPE.NAME'],
                    'order'=>['PERSON_TYPE.SORT'=>'ASC','PERSON_TYPE.NAME'=>'ASC','SORT'=>'ASC']
                ]);
                while($data = $r->fetch()){
                    if(mb_strpos(mb_strtolower($data['PERSON_TYPE_NAME']), mb_strtolower(Loc::getMessage('AWZ_AUTOUNP_SEARCH_1')))!==false){
                        if(mb_strpos(mb_strtolower($data['NAME']),mb_strtolower(Loc::getMessage('AWZ_AUTOUNP_SEARCH_2')))!==false){
                            $candidatesInn = $data['ID'];
                        }else if(mb_strpos(mb_strtolower($data['NAME']),mb_strtolower(Loc::getMessage('AWZ_AUTOUNP_SEARCH_3')))!==false){
                            $candidatesInn = $data['ID'];
                        }
                    }
                    $allProps[] = $data;
                }
            }
        ?>
        <tr class="heading">
            <td colspan="2">
                <b><?=$arSite['NAME']?></b>
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SHOW_TITLE')?></td>
            <td>
                <?$val = Option::get($module_id, "SHOW", "N",$arSite['LID']);?>
                <input type="checkbox" value="Y" name="SHOW[<?=$arSite['LID']?>]" <?if ($val=="Y") echo "checked";?>>
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_FIELD_UNP_TITLE')?></td>
            <td>
                <?$val = Option::get($module_id, "FIELD_UNP", "input[name=\"ORDER_PROP_".$candidatesInn."\"]",$arSite['LID']); ?>
                <input size="30" type="text" value="<?=htmlspecialcharsEx($val)?>" name="FIELD_UNP[<?=$arSite['LID']?>]">
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_FIELD_MAME1_TITLE')?></td>
            <td>
                <?$val = Option::get($module_id, "FIELD_MAME1", "",$arSite['LID']);?>
                <input size="30" type="text" value="<?=htmlspecialcharsEx($val)?>" name="FIELD_MAME1[<?=$arSite['LID']?>]">
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_FIELD_MAME2_TITLE')?></td>
            <td>
                <?$val = Option::get($module_id, "FIELD_MAME2", "",$arSite['LID']);?>
                <input size="30" type="text" value="<?=htmlspecialcharsEx($val)?>" name="FIELD_MAME2[<?=$arSite['LID']?>]">
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_FIELD_UR_ADRESS_TITLE')?></td>
            <td>
                <?$val = Option::get($module_id, "FIELD_UR_ADRESS", "",$arSite['LID']);?>
                <input size="30" type="text" value="<?=htmlspecialcharsEx($val)?>" name="FIELD_UR_ADRESS[<?=$arSite['LID']?>]">
            </td>
        </tr>
        <tr>
            <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_PAGES')?></td>
            <td>
                <?$val = Option::get($module_id, "PAGES", "",$arSite['LID']);?>
                <textarea cols="32" rows="6" type="text" name="PAGES[<?=$arSite['LID']?>]"><?=htmlspecialcharsEx($val)?></textarea>
            </td>
        </tr>
        <?if(!empty($allProps)){?>
            <tr>
                <td style="width:50%;"><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE')?><br>
                    <a href="/bitrix/admin/sale_order_props.php?lang=<?=LANGUAGE_ID?>">
                        <?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE_ALL')?>
                    </a>
                </td>
                <td>
                    <?
                    $styletd = ' style="padding:3px 5px;font-size:12px;border:1px solid #ededed;text-align: left;"';
                    ?>
                    <table style="width:100%;border-spacing:0;background:#ffffff;">
                        <tr>
                            <th<?=$styletd?>><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE_1')?></th>
                            <th<?=$styletd?>><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE_2')?></th>
                            <th<?=$styletd?>><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE_3')?></th>
                            <th<?=$styletd?>><?=Loc::getMessage('AWZ_AUTOUNP_OPT_SALE_TITLE_4')?></th>
                        </tr>
                    <?
                    foreach($allProps as $data){
                        ?>
                        <tr>
                            <td<?=$styletd?>><?=$data['PERSON_TYPE_NAME']?></td>
                            <td<?=$styletd?>><?=$data['ID']?></td>
                            <td<?=$styletd?>><?=$data['CODE']?></td>
                            <td<?=$styletd?>><?=$data['NAME']?></td>
                        </tr>
                        <?
                    }
                    ?>
                    </table>
                </td>
            </tr>
        <?}?>
        <?
        }
        $tabControl->BeginNextTab();
        require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/admin/group_rights.php");
        ?>

        <?
        $tabControl->Buttons();
        ?>
        <input <?if ($MODULE_RIGHT<"W") echo "disabled" ?> type="submit" class="adm-btn-green" name="Update" value="<?=Loc::getMessage('AWZ_AUTOUNP_OPT_L_BTN_SAVE')?>" />
        <input type="hidden" name="Update" value="Y" />
        <?$tabControl->End();?>
    </form>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");