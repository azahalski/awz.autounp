<?php
namespace Awz\AutoUnp\Api\Controller;

use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\ActionFilter\Scope;
use Awz\AutoUnp\Api\Filters\Sign;
use Awz\AutoUnp\Helper;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

use Awz\AutoUnp\HandlersBx;
use Bitrix\Main\Request;

Loc::loadMessages(__FILE__);

class mnsRb extends Controller
{

    private string $moduleId = '';

    public function __construct(Request $request = null)
    {
        $this->moduleId = Helper::MODULE_ID;
        parent::__construct($request);
    }

    public function configureActions()
    {
        return array(
            'find' => array(
                'prefilters' => array(
                    new Scope(Scope::AJAX),
                    new Sign(array('site_id','s_id','options_hash'))
                )
            )
        );
    }

    public function findAction(int $unp, string $site_id, string $options_hash){

        if(!Helper::checkOptionsHash($site_id, $options_hash)){
            $this->addError(
                new Error(
                    Loc::getMessage('AWZ_AUTOUNP_CONTROLLER_ERR_HASH'),
                    100
                )
            );
            return null;
        }

        $reqResult = Helper::getInfo($unp);
        if(!$reqResult->isSuccess()){
            $this->addErrors($reqResult->getErrors());
            return null;
        }

        $data = $reqResult->getData()['row'];
        $finData = [
            'mns'=>$data,
            'replace'=>[]
        ];
        foreach($data as $key=>$val){
            if(($opt = Option::get(Helper::MODULE_ID, 'FIELD_MAME1', '', $site_id)) && $key=='vnaimp'){
                $finData['replace'][] = [
                    $opt, (string) $val
                ];
            }
            if(($opt = Option::get(Helper::MODULE_ID, 'FIELD_MAME2', '', $site_id)) && $key=='vnaimk'){
                $finData['replace'][] = [
                    $opt, (string) $val
                ];
            }
            if(($opt = Option::get(Helper::MODULE_ID, 'FIELD_UR_ADRESS', '', $site_id)) && $key=='vpadres'){
                $finData['replace'][] = [
                    $opt, (string) $val
                ];
            }
        }

        return $finData;

    }
}