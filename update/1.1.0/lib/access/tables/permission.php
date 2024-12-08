<?php
namespace Awz\Autounp\Access\Tables;

use Bitrix\Main\Access\Permission\AccessPermissionTable;

class PermissionTable extends AccessPermissionTable
{
    public static function getTableName()
    {
        return 'awz_autounp_permission';
    }
}