<?php

namespace App\Models;

/**
 * Table `cms_menu_items` (CI4-owned CMS table, see migration CreateCmsTables and docs/CMS.md).
 */
class CmsMenuItemsModel extends BaseModel
{
    protected $table         = 'cms_menu_items';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['menu_id','parent_id','label','page_id','url','new_tab','active_keys','sort_order','status'];
}
