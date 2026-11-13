<?php

namespace Tests\Support\Parity;

use App\Models\EventsModel;
use App\Models\GalleryModel;
use App\Models\GalleryThumbsModel;
use App\Models\LearningModel;
use App\Models\LifeAtColdwellBankerImagesModel;
use App\Models\LifeAtColdwellBankerModel;

/**
 * Parity checks for the "learning" domain: learning overview, gallery,
 * gallery_thumbs, events, life_at_coldwell_banker(_images).
 *
 * Sample ids come from the staging copy of the live data.
 */
class LearningParity implements ParitySuite
{
    public function domain(): string
    {
        return 'learning';
    }

    public function checks(): array
    {
        return [
            // learning
            [
                'label'   => 'home_model::show_overview_list (home_model.php:15)',
                'old_sql' => 'SELECT * FROM `learning` ORDER BY `id` DESC',
                'new'     => static fn () => model(LearningModel::class)->overviewList(),
            ],
            [
                'label'   => 'home_model::get_overview_data (home_model.php:21)',
                'old_sql' => "SELECT * FROM `learning` WHERE `id` = '2'",
                'new'     => static fn () => model(LearningModel::class)->findOverview(2),
                'compare' => 'row',
            ],
            [
                'label'   => 'learning_development.php:53 (learning id 2)',
                'old_sql' => "select * from learning where id='2' ",
                'new'     => static fn () => model(LearningModel::class)->findOverview(2),
                'compare' => 'row',
            ],

            // gallery
            [
                'label'   => 'home_model::show_gallery_list (home_model.php:56)',
                'old_sql' => 'SELECT * FROM `gallery` ORDER BY `id` DESC',
                'new'     => static fn () => model(GalleryModel::class)->galleryList(),
            ],
            [
                'label'   => 'home_model::show_image_by_id (home_model.php:109)',
                'old_sql' => "SELECT * FROM `gallery` WHERE `id` = '12'",
                'new'     => static fn () => model(GalleryModel::class)->findImage(12),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::load_group_images_gallery (Ui_model.php:428)',
                'old_sql' => 'select * from gallery where group_id=2 order by id desc ',
                'new'     => static fn () => model(GalleryModel::class)->imagesByGroup(2),
            ],
            [
                'label'   => 'Ui_model::load_group_images_gallery, group with inactive images (Ui_model.php:428)',
                'old_sql' => 'select * from gallery where group_id=1 order by id desc ',
                'new'     => static fn () => model(GalleryModel::class)->imagesByGroup(1),
            ],
            [
                'label'   => 'learning_development.php:198 (lightbox images)',
                'old_sql' => "select * from gallery where status='1'  order by group_id, date_time",
                'new'     => static fn () => model(GalleryModel::class)->activeLightboxImages(),
            ],

            // gallery_thumbs
            [
                'label'   => 'home_model::show_gallery_thumbnails_list (home_model.php:1947)',
                'old_sql' => 'SELECT * FROM `gallery_thumbs` ORDER BY `id` DESC',
                'new'     => static fn () => model(GalleryThumbsModel::class)->thumbnailList(),
            ],
            [
                'label'   => 'home_model::show_gallery_thumbnail_by_id (home_model.php:2000)',
                'old_sql' => "SELECT * FROM `gallery_thumbs` WHERE `id` = '11'",
                'new'     => static fn () => model(GalleryThumbsModel::class)->findThumbnail(11),
                'compare' => 'row',
            ],
            [
                'label'   => 'learning_development.php:162 (one thumbnail per group)',
                'old_sql' => "select t.* from gallery_thumbs t\n"
                    . "        inner join (select min(id) as id from gallery_thumbs where status='1' group by group_id) firsts on firsts.id = t.id\n"
                    . '        order by t.date_time desc',
                'new'     => static fn () => model(GalleryThumbsModel::class)->firstActivePerGroup(),
                'note'    => 'old_sql is the view query as already rewritten for MySQL 8 (the original SELECT * ... GROUP BY group_id is rejected under ONLY_FULL_GROUP_BY)',
            ],

            // events
            [
                'label'   => 'home_model::show_events_list (home_model.php:134)',
                'old_sql' => 'SELECT * FROM `events` ORDER BY `id` DESC',
                'new'     => static fn () => model(EventsModel::class)->eventList(),
            ],
            [
                'label'   => 'home_model::show_events_by_id (home_model.php:165)',
                'old_sql' => "SELECT * FROM `events` WHERE `id` = '3'",
                'new'     => static fn () => model(EventsModel::class)->findEvent(3),
                'compare' => 'row',
            ],
            [
                'label'   => 'learning_development.php:111 (training calendar)',
                'old_sql' => "select * from events where status='1' order by start_date asc ",
                'new'     => static fn () => model(EventsModel::class)->activeByStartDate(),
            ],

            // life_at_coldwell_banker
            [
                'label'   => 'home_model::show_life_at_coldwell_banker (home_model.php:1305)',
                'old_sql' => 'SELECT * FROM `life_at_coldwell_banker` ORDER BY `id` DESC',
                'new'     => static fn () => model(LifeAtColdwellBankerModel::class)->entryList(),
            ],
            [
                'label'   => 'home_model::show_life_at_coldwell_banker_by_id (home_model.php:1311)',
                'old_sql' => "SELECT * FROM `life_at_coldwell_banker` WHERE `id` = '1'",
                'new'     => static fn () => model(LifeAtColdwellBankerModel::class)->findEntry(1),
                'compare' => 'row',
            ],
            [
                'label'   => 'career.php:43 / career-old.php:43 / franchisees_openings.php:45',
                'old_sql' => 'select * from life_at_coldwell_banker ',
                'new'     => static fn () => model(LifeAtColdwellBankerModel::class)->firstEntry(),
                'compare' => 'row',
            ],

            // life_at_coldwell_banker_images
            [
                'label'   => 'home_model::show_life_at_coldwell_banker_images (home_model.php:1347)',
                'old_sql' => 'SELECT * FROM `life_at_coldwell_banker_images` ORDER BY `id` DESC',
                'new'     => static fn () => model(LifeAtColdwellBankerImagesModel::class)->imageList(),
            ],
            [
                'label'   => 'home_model::show_life_at_coldwell_banker_image_by_id (home_model.php:1353)',
                'old_sql' => "SELECT * FROM `life_at_coldwell_banker_images` WHERE `id` = '3'",
                'new'     => static fn () => model(LifeAtColdwellBankerImagesModel::class)->findImage(3),
                'compare' => 'row',
            ],
            [
                'label'   => 'career.php:159 / career-old.php:159 / franchisees_openings.php:153',
                'old_sql' => 'select * from life_at_coldwell_banker_images ',
                'new'     => static fn () => model(LifeAtColdwellBankerImagesModel::class)->allImages(),
                'compare' => 'set',
            ],
        ];
    }
}
