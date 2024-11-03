<?php
namespace Awz\AutoUnp;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Security;
use Bitrix\Main\Loader;

class HandlersBx {

    private static $showScript = false;

    public static function enableScript(){
        self::$showScript = true;
    }
    public static function dissableScript(){
        self::$showScript = true;
    }
    public static function isEnabledScript(){
        return self::$showScript === true;
    }

    public static function OnPageStart(){

        $context = Application::getInstance()->getContext();
        $request = $context->getRequest();
        $uriString = $request->getRequestUri();
        if($context->getRequest()->isAdminSection()) return;
        if(
            Option::get(Helper::MODULE_ID, 'SHOW', 'N', $context->getSite())==="Y" &&
            Option::get(Helper::MODULE_ID, 'FIELD_UNP', '', $context->getSite())
        ){
            $pages = [];
            foreach(explode("\n", Option::get(Helper::MODULE_ID, 'PAGES', '', SITE_ID)) as $page){
                if(trim($page)){
                    $pages[] = $page;
                }
            }
            if(!empty($pages)){
                foreach($pages as $page){
                    if(mb_strpos(mb_strtolower($uriString),mb_strtolower($page))!==false) {
                        self::enableScript();
                        break;
                    }
                }
            }else{
                self::enableScript();
            }
        }
        if(self::isEnabledScript()){
            Loader::includeModule(Helper::MODULE_ID);
            \CJsCore::init(['awz_autounp']);
        }
    }

    public static function OnEndBufferContent(&$content){
        if(!self::isEnabledScript()) return;
        if(
            mb_strpos(mb_substr($content,-20), '</body>')!==false
        ){

            $context = Application::getInstance()->getContext();
            $request = $context->getRequest();
            $uriString = $request->getRequestUri();

            $signer = new Security\Sign\Signer();

            $options = [
                'node'=>Option::get(Helper::MODULE_ID, 'FIELD_UNP', '', $context->getSite())
            ];
            $options['signedParameters'] = $signer->sign(base64_encode(serialize(array(
                'uriString'=>$uriString,
                'site_id'=>$context->getSite(),
                's_id'=>\bitrix_sessid(),
                'options_hash'=>Helper::getOptionsHash($context->getSite())
            ))));

            $html = '<script>var AwzAutoUnp_ob = new window.AwzAutoUnp('.\CUtil::PHPToJSObject($options).');</script>';
            $content = str_replace('</body>',$html."\n".'</body>',$content);
        }
    }



}