<?php
namespace Awz\Autounp\Access\Tables;

use Bitrix\Main\Access\Role\AccessRoleRelationTable;

class RoleRelationTable extends AccessRoleRelationTable
{
    public static function getTableName()
    {
        return 'awz_autounp_role_relation';
    }

}