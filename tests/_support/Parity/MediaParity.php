<?php

namespace Tests\Support\Parity;

use App\Models\AwardsModel;
use App\Models\ColdwellBankerIndustryNewsModel;
use App\Models\ColdwellBankerKnowledgeSeriesModel;
use App\Models\ColdwellBankerNewsletterModel;
use App\Models\ColdwellBankerNewsModel;
use App\Models\ResearchModel;

/**
 * Parity checks for the "media" domain: news, industry news, knowledge series,
 * newsletters, research and awards (home_model.php, ui/Ui_model.php and the
 * inline queries of the public media views).
 */
class MediaParity implements ParitySuite
{
    public function domain(): string
    {
        return 'media';
    }

    public function checks(): array
    {
        return array_merge(
            $this->contentChecks(ColdwellBankerNewsModel::class, 'coldwell_banker_news', [
                'show'    => 'home_model::show_coldwell_banker_news (home_model.php:1219)',
                'showId'  => 'home_model::show_coldwell_banker_news_id (home_model.php:1390)',
                'iframe'  => 'Ui_model::get_iframe_image (Ui_model.php:309)',
                'count'   => 'Ui_model::count_total_news (Ui_model.php:406)',
                'more'    => 'Ui_model::load_more_news (Ui_model.php:412)',
                'view'    => 'views/media_center.php:48',
                'id'      => 45,
                'iframeId' => 80,
                'missing' => 56,
                'starts'  => [8, 48],
            ]),
            $this->contentChecks(ColdwellBankerIndustryNewsModel::class, 'coldwell_banker_industry_news', [
                'show'    => 'home_model::show_coldwell_banker_industry_news (home_model.php:1458)',
                'showId'  => 'home_model::show_coldwell_banker_industry_news_id (home_model.php:1464)',
                'iframe'  => 'Ui_model::iframe_industry_news (Ui_model.php:321)',
                'count'   => 'Ui_model::count_total_industry_news (Ui_model.php:383)',
                'more'    => 'Ui_model::load_more_industry_news (Ui_model.php:389)',
                'view'    => 'views/industry_news.php:51',
                'id'      => 6,
                'iframeId' => 8,
                'missing' => 3,
                'starts'  => [0, 2],
            ]),
            $this->contentChecks(ColdwellBankerKnowledgeSeriesModel::class, 'coldwell_banker_knowledge_series', [
                'show'    => 'home_model::show_coldwell_banker_knowledge_series (home_model.php:1536)',
                'showId'  => 'home_model::show_coldwell_banker_knowledge_series_id (home_model.php:1542)',
                'iframe'  => 'Ui_model::iframe_knowledge_image (Ui_model.php:315)',
                'count'   => 'Ui_model::count_total_knowledge_series (Ui_model.php:361)',
                'more'    => 'Ui_model::load_more_knowledge_series (Ui_model.php:367)',
                'view'    => 'views/knowledge_series.php:51',
                'id'      => 7,
                'iframeId' => 3,
                'missing' => 2,
                'starts'  => [8, 1],
            ]),
            $this->contentChecks(ColdwellBankerNewsletterModel::class, 'coldwell_banker_newsletter', [
                'show'    => 'home_model::show_coldwell_banker_newsletter (home_model.php:1613)',
                'showId'  => 'home_model::show_coldwell_banker_newsletter_id (home_model.php:1619)',
                'iframe'  => 'Ui_model::iframe_newsletter (Ui_model.php:327)',
                'count'   => 'Ui_model::count_total_newletter (Ui_model.php:339)',
                'more'    => 'Ui_model::load_more_newsletter (Ui_model.php:345)',
                'view'    => 'views/newsletter.php:59',
                'id'      => 20,
                'iframeId' => 31,
                'missing' => 2,
                'starts'  => [8, 24],
            ]),
            $this->researchChecks(),
            $this->awardsChecks(),
        );
    }

    /**
     * The four news-like tables share the same CI3 functions; $p holds the
     * labels and sample parameters taken from the live data.
     */
    private function contentChecks(string $modelClass, string $table, array $p): array
    {
        $m     = model($modelClass, false);
        $total = (int) db_connect()->query("SELECT count(*) as count FROM {$table}")->getRowArray()['count'];

        $checks = [
            [
                'label'   => $p['show'],
                'old_sql' => "SELECT * FROM `{$table}` ORDER BY `id` DESC",
                'new'     => static fn () => $m->listNewestFirst(),
            ],
            [
                'label'   => $p['showId'] . " id={$p['id']}",
                'old_sql' => "SELECT * FROM `{$table}` WHERE `id` = '{$p['id']}' ORDER BY `id` DESC",
                'new'     => static fn () => $m->findById($p['id']),
                'compare' => 'row',
            ],
            [
                'label'   => $p['iframe'] . " id={$p['iframeId']}",
                'old_sql' => "SELECT * FROM `{$table}` WHERE `id` = '{$p['iframeId']}'",
                'new'     => static fn () => $m->findById($p['iframeId']),
                'compare' => 'row',
            ],
            [
                'label'   => $p['iframe'] . " missing id={$p['missing']}",
                'old_sql' => "SELECT * FROM `{$table}` WHERE `id` = '{$p['missing']}'",
                'new'     => static fn () => $m->findById($p['missing']),
                'compare' => 'row',
            ],
            [
                'label'   => $p['count'],
                'old_sql' => "SELECT count(*) as count FROM {$table}",
                'new'     => static fn () => $m->countTotal(),
                'compare' => 'row',
            ],
            [
                'label'   => $p['view'],
                'old_sql' => "select * from {$table} order by id desc limit 0,8",
                'new'     => static fn () => $m->latest(),
            ],
        ];

        foreach ([...$p['starts'], $total] as $start) {
            $countRow = $total - $start;
            $limit    = $countRow < 8 ? $countRow : 8;
            $checks[] = [
                'label'   => $p['more'] . "({$total}, {$start})",
                'old_sql' => "select * from {$table} order by id desc limit {$start},{$limit}",
                'new'     => static fn () => $m->loadMore($total, $start),
            ];
        }

        // Start past the end: CI3 sent a negative LIMIT (SQL error); the port returns [].
        $past     = $total + 8;
        $checks[] = [
            'label'   => $p['more'] . "({$total}, {$past})",
            'old_sql' => "select * from {$table} order by id desc limit {$past},0",
            'new'     => static fn () => $m->loadMore($total, $past),
            'note'    => 'CI3 sent "limit ' . $past . ',-8" (SQL error); compared with LIMIT n,0 (empty result)',
        ];

        return $checks;
    }

    private function researchChecks(): array
    {
        $m = model(ResearchModel::class, false);

        return [
            [
                'label'   => 'home_model::show_research (home_model.php:1083)',
                'old_sql' => 'SELECT * FROM `research` ORDER BY `id` DESC',
                'new'     => static fn () => $m->listNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_research_by_id (home_model.php:1089) id=27',
                'old_sql' => "SELECT * FROM `research` WHERE `id` = '27'",
                'new'     => static fn () => $m->findById(27),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_research_by_id (home_model.php:1089) missing id=12',
                'old_sql' => "SELECT * FROM `research` WHERE `id` = '12'",
                'new'     => static fn () => $m->findById(12),
                'compare' => 'row',
            ],
            [
                'label'   => 'views/research.php:53',
                'old_sql' => "select * from research where type='Project Reports' and is_active='1' order by id desc ",
                'new'     => static fn () => $m->activeByType('Project Reports'),
            ],
            [
                'label'   => 'views/area_insights.php:53',
                'old_sql' => "select * from research where type='Area Insights' and is_active='1' order by id desc",
                'new'     => static fn () => $m->activeByType('Area Insights'),
            ],
            [
                'label'   => 'Ui_model::research (Ui_model.php:99)',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '8'",
                'new'     => static fn () => $m->pageInfo(),
                'compare' => 'row',
                'note'    => 'CI3 row() object; the port returns the same row as an array',
            ],
        ];
    }

    private function awardsChecks(): array
    {
        $m = model(AwardsModel::class, false);

        return [
            [
                'label'   => 'home_model::show_coldwell_banker_awards (home_model.php:1773)',
                'old_sql' => 'SELECT * FROM `awards` ORDER BY `id` DESC',
                'new'     => static fn () => $m->listNewestFirst(),
            ],
            [
                'label'   => 'views/awards.php:47',
                'old_sql' => 'select * from awards order by id desc ',
                'new'     => static fn () => $m->listNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_coldwell_banker_award_by_id (home_model.php:1789) id=10',
                'old_sql' => "SELECT * FROM `awards` WHERE `id` = '10'",
                'new'     => static fn () => $m->findById(10),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::iframe_awards (Ui_model.php:333) id=12',
                'old_sql' => "SELECT * FROM `awards` WHERE `id` = '12'",
                'new'     => static fn () => $m->findById(12),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::iframe_awards (Ui_model.php:333) missing id=7',
                'old_sql' => "SELECT * FROM `awards` WHERE `id` = '7'",
                'new'     => static fn () => $m->findById(7),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::awards (Ui_model.php:87)',
                'old_sql' => "SELECT * FROM `pages` WHERE `id` = '6'",
                'new'     => static fn () => $m->pageInfo(),
                'compare' => 'row',
                'note'    => 'CI3 row() object; the port returns the same row as an array',
            ],
        ];
    }
}
