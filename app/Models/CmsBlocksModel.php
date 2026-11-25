<?php

namespace App\Models;

/**
 * Table `cms_blocks` (CI4-owned CMS table, see migration CreateCmsTables and docs/CMS.md).
 */
class CmsBlocksModel extends BaseModel
{
    protected $table         = 'cms_blocks';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['page_id','type','label','settings','sort_order','status'];
}
