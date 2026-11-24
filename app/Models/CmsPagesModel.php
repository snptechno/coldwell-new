<?php

namespace App\Models;

/**
 * Table `cms_pages` (CI4-owned CMS table, see migration CreateCmsTables and docs/CMS.md).
 */
class CmsPagesModel extends BaseModel
{
    protected $table         = 'cms_pages';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $allowedFields = ['type','title','slug','is_home','nav_key','excerpt','body','featured_image','image_alt','status','published_at','meta_title','meta_description','meta_keywords','og_image','noindex'];
}
