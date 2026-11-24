<?php

namespace App\Models;

/**
 * Table `cms_menus` (CI4-owned CMS table, see migration CreateCmsTables and docs/CMS.md).
 */
class CmsMenusModel extends BaseModel
{
    protected $table         = 'cms_menus';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['location','name'];
}
