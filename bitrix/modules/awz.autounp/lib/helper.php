<?php
namespace Awz\AutoUnp;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Result;
use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class Helper {

    const CACHE_TIME = 31600000;
    const MODULE_ID = 'awz.autounp';
    const API_URL = 'http://grp.nalog.gov.by/api/grp-public/data';

    public static function getInfo(int $unp): Result{
        $reqResult = new \Bitrix\Main\Result();
        if(mb_strlen((string)$unp)!==9){
            $reqResult->addError(
                new Error(
                    Loc::getMessage('AWZ_AUTOUNP_HELPER_ERR_UNP'),
                    100
                )
            );
            return $reqResult;
        }

        $obCache = \Bitrix\Main\Data\Cache::createInstance();
        if( $obCache->initCache(self::CACHE_TIME,$unp,"/awz/".self::MODULE_ID) ){
            $res = $obCache->GetVars();
        }elseif( $obCache->startDataCache()){
            $r = new HttpClient([
                'socketTimeout'=>5,
                'streamTimeout'=>5
            ]);
            $params = [
                'unp'=>$unp,
                'charset'=>'UTF-8',
                'type'=>'json'
            ];
            $res = $r->get(self::API_URL.'?'.http_build_query($params));
            if(!$res){
                $obCache->abortDataCache();
            }
            $obCache->endDataCache($res);
        }

        try{
            $reqResult->setData(Json::decode($res));
        }catch (\Exception $e){
            $reqResult->setData(['content'=>$res]);
            $reqResult->addError(new Error(
                $e->getMessage(),
                200
            ));
        }
        return $reqResult;
    }

    public static function checkOptionsHash(string $siteId, string $hash): bool
    {
        return self::getOptionsHash($siteId) === $hash;
    }

    public static function getOptionsHash(string $siteId): string
    {
        static $optionsData;
        if(!$optionsData){
            $optionsData = [
                Option::get(self::MODULE_ID, 'FIELD_UNP', '', $siteId),
                Option::get(self::MODULE_ID, 'FIELD_MAME1', '', $siteId),
                Option::get(self::MODULE_ID, 'FIELD_MAME2', '', $siteId),
                Option::get(self::MODULE_ID, 'FIELD_UR_ADRESS', '', $siteId),
                Option::get(self::MODULE_ID, 'PAGES', '', $siteId)
            ];
        }
        return md5(serialize($optionsData));
    }

}