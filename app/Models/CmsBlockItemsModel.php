<?php

namespace App\Models;

/**
 * Table `cms_block_items` (CI4-owned CMS table, see migration CreateCmsTables and docs/CMS.md).
 */
class CmsBlockItemsModel extends BaseModel
{
    protected $table         = 'cms_block_items';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['block_id','item_group','kicker','badge','title','subtitle','body','cta_label','cta_url','image','image_alt','sort_order','status'];
}
