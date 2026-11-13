<?php

namespace Tests\Support\Parity;

use App\Models\CareersTestimonialsModel;
use App\Models\CbIndiaVideosModel;
use App\Models\FeaturedVideosModel;
use App\Models\FindingHomeVideosModel;
use App\Models\SellingHomeVideosModel;
use App\Models\SuccessStoryModel;
use App\Models\TestimonialVideosModel;

/**
 * Parity checks for the videos domain (finding_home_videos, selling_home_videos,
 * testimonial_videos, cb_india_videos, featured_videos, success_story,
 * careers_testimonials, plus the `pages` meta rows used by two video pages).
 * Read queries only; write methods are reviewed, not executed.
 */
class VideosParity implements ParitySuite
{
    public function domain(): string
    {
        return 'videos';
    }

    public function checks(): array
    {
        $finding  = new FindingHomeVideosModel();
        $selling  = new SellingHomeVideosModel();
        $testim   = new TestimonialVideosModel();
        $cbIndia  = new CbIndiaVideosModel();
        $featured = new FeaturedVideosModel();
        $success  = new SuccessStoryModel();
        $careers  = new CareersTestimonialsModel();

        return [
            // --- finding_home_videos -------------------------------------------------
            [
                'label'   => 'home_model::show_finding_home_videos (home_model.php:898)',
                'old_sql' => 'SELECT * FROM `finding_home_videos` ORDER BY `id` DESC',
                'new'     => static fn () => $finding->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_finding_home_video_by_id (home_model.php:904) id=4',
                'old_sql' => "SELECT * FROM `finding_home_videos` WHERE `id` = '4'",
                'new'     => static fn () => $finding->byId(4),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_finding_home_video_by_id (home_model.php:904) missing id',
                'old_sql' => "SELECT * FROM `finding_home_videos` WHERE `id` = '999999'",
                'new'     => static fn () => $finding->byId(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/finding_a_home.php:59',
                'old_sql' => "select id, title, description, video_url from finding_home_videos where is_active='1' ",
                'new'     => static fn () => $finding->activeVideos(),
                'compare' => 'set',
            ],
            [
                'label'   => 'views/videos.php:65',
                'old_sql' => "select id, title, description, video_url from finding_home_videos where is_active='1' ",
                'new'     => static fn () => $finding->activeVideos(),
                'compare' => 'set',
            ],

            // --- selling_home_videos -------------------------------------------------
            [
                'label'   => 'home_model::show_selling_home_videos (home_model.php:927)',
                'old_sql' => 'SELECT * FROM `selling_home_videos` ORDER BY `id` DESC',
                'new'     => static fn () => $selling->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_selling_home_video_by_id (home_model.php:933) id=3',
                'old_sql' => "SELECT * FROM `selling_home_videos` WHERE `id` = '3'",
                'new'     => static fn () => $selling->byId(3),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/selling_a_home.php:61',
                'old_sql' => "select id, title, description, video_url from selling_home_videos where is_active='1' ",
                'new'     => static fn () => $selling->activeVideos(),
                'compare' => 'set',
            ],

            // --- testimonial_videos --------------------------------------------------
            [
                'label'   => 'home_model::show_testimonial_videos (home_model.php:972)',
                'old_sql' => 'SELECT * FROM `testimonial_videos` ORDER BY `id` DESC',
                'new'     => static fn () => $testim->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_testimonial_video_by_id (home_model.php:978) id=7 (inactive row)',
                'old_sql' => "SELECT * FROM `testimonial_videos` WHERE `id` = '7'",
                'new'     => static fn () => $testim->byId(7),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/testimonials.php:60',
                'old_sql' => "select id, title, description, video_url from testimonial_videos where is_active='1' ORDER BY id DESC",
                'new'     => static fn () => $testim->activeVideosLatestFirst(),
            ],

            // --- cb_india_videos -----------------------------------------------------
            [
                'label'   => 'home_model::show_cb_india_videos (home_model.php:1009)',
                'old_sql' => 'SELECT * FROM `cb_india_videos` ORDER BY `id` DESC',
                'new'     => static fn () => $cbIndia->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_cb_india_video_by_id (home_model.php:1015) id=6',
                'old_sql' => "SELECT * FROM `cb_india_videos` WHERE `id` = '6'",
                'new'     => static fn () => $cbIndia->byId(6),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/cb_india_videos.php:60',
                'old_sql' => "select id, title, description, video_url from cb_india_videos where is_active='1' ",
                'new'     => static fn () => $cbIndia->activeVideos(),
                'compare' => 'set',
            ],
            [
                'label'   => 'Ui_model::cb_india_videos (ui/Ui_model.php:21)',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '2'",
                'new'     => static fn () => $cbIndia->pageInfo(),
                'compare' => 'row',
                'note'    => 'row() object returned as an array (view step switches -> to [] access)',
            ],

            // --- featured_videos -----------------------------------------------------
            [
                'label'   => 'home_model::show_featured_videos (home_model.php:1046)',
                'old_sql' => 'SELECT * FROM `featured_videos` ORDER BY `id` DESC',
                'new'     => static fn () => $featured->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_featured_video_by_id (home_model.php:1052) id=13',
                'old_sql' => "SELECT * FROM `featured_videos` WHERE `id` = '13'",
                'new'     => static fn () => $featured->byId(13),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/featured_videos.php:60',
                'old_sql' => "select id, title, description, video_url from featured_videos where is_active='1' ",
                'new'     => static fn () => $featured->activeVideos(),
                'compare' => 'set',
            ],
            [
                'label'   => 'Ui_model::featured_videos (ui/Ui_model.php:27)',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '2'",
                'new'     => static fn () => $featured->pageInfo(),
                'compare' => 'row',
                'note'    => 'row() object returned as an array (view step switches -> to [] access)',
            ],

            // --- success_story -------------------------------------------------------
            [
                'label'   => 'home_model::success_stories_videos (home_model.php:2043)',
                'old_sql' => 'SELECT * FROM `success_story` ORDER BY `id` DESC',
                'new'     => static fn () => $success->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_success_story_video_by_id (home_model.php:2050) id=10',
                'old_sql' => "SELECT * FROM `success_story` WHERE `id` = '10'",
                'new'     => static fn () => $success->byId(10),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/career.php:130',
                'old_sql' => "select * from success_story where is_active='1' order by id desc",
                'new'     => static fn () => $success->activeLatestFirst(),
            ],
            [
                'label'   => 'views/career-old.php:130',
                'old_sql' => "select * from success_story where is_active='1' order by id desc",
                'new'     => static fn () => $success->activeLatestFirst(),
            ],
            [
                'label'   => 'views/franchisees_openings.php:124',
                'old_sql' => "select * from success_story where is_active='1' order by id desc",
                'new'     => static fn () => $success->activeLatestFirst(),
            ],

            // --- careers_testimonials ------------------------------------------------
            [
                'label'   => 'home_model::show_careers_testimonials (home_model.php:1226)',
                'old_sql' => 'SELECT * FROM `careers_testimonials` ORDER BY `id` DESC',
                'new'     => static fn () => $careers->allLatestFirst(),
            ],
            [
                'label'   => 'home_model::show_careers_testimonials_by_id (home_model.php:1232) id=2',
                'old_sql' => "SELECT * FROM `careers_testimonials` WHERE `id` = '2'",
                'new'     => static fn () => $careers->byId(2),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_careers_testimonials_by_id (home_model.php:1232) missing id',
                'old_sql' => "SELECT * FROM `careers_testimonials` WHERE `id` = '999999'",
                'new'     => static fn () => $careers->byId(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/franchisees_openings.php:103',
                'old_sql' => 'select * from careers_testimonials ',
                'new'     => static fn () => $careers->allTestimonials(),
                'compare' => 'set',
            ],
        ];
    }
}
