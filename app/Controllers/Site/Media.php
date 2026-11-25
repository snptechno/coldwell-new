<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;
use App\Models\AwardsModel;
use App\Models\ColdwellBankerIndustryNewsModel;
use App\Models\ColdwellBankerKnowledgeSeriesModel;
use App\Models\ColdwellBankerNewsletterModel;
use App\Models\ColdwellBankerNewsModel;
use App\Models\PagesModel;
use App\Models\ResearchModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Media Center (news, industry news, knowledge series, newsletter), Awards and
 * Research pages, their lightbox iframes and "load more" AJAX endpoints.
 * CI3: controllers/Home.php (media_center .. research, iframe_*, load_more_*).
 *
 * Each page passes `act` as well as `page_active`: partials/navigation.php marks
 * the current menu item from $act (CI3 views passed $data1['act'] = $page_active
 * to the navigation view); the shared view data reaches the included partial.
 */
class Media extends BaseController
{
    /**
     * Loads a `pages` row as an object, as the CI3 views expect ($page_info->title),
     * and 404s if it is missing (CI3 would have crashed on a null row).
     */
    private function pageInfo(int $id): object
    {
        $row = model(PagesModel::class)->pageById($id);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (object) $row;
    }

    /** CI3: Home::media_center (controllers/Home.php:96) */
    public function mediaCenter(): string
    {
        return view('site/media_center', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_MEDIA_CENTER),
            'page_active' => 'media center',
            'act'         => 'media center',
            // views/media_center.php:48
            'rz1'         => model(ColdwellBankerNewsModel::class)->latest(),
        ]);
    }

    /** CI3: Home::industry_news (controllers/Home.php:104) */
    public function industryNews(): string
    {
        return view('site/industry_news', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_MEDIA_CENTER),
            'page_active' => 'media center',
            'act'         => 'media center',
            // views/industry_news.php:51
            'rz1'         => model(ColdwellBankerIndustryNewsModel::class)->latest(),
        ]);
    }

    /** CI3: Home::knowledge_series (controllers/Home.php:111) */
    public function knowledgeSeries(): string
    {
        return view('site/knowledge_series', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_MEDIA_CENTER),
            'page_active' => 'media center',
            'act'         => 'media center',
            // views/knowledge_series.php:51
            'rz1'         => model(ColdwellBankerKnowledgeSeriesModel::class)->latest(),
        ]);
    }

    /**
     * CI3: Home::newsletter (controllers/Home.php:118).
     * The subscription form posts to newsletter_subscription (ported in a later step),
     * which sets the flash message shown above the form.
     */
    public function newsletter(): string
    {
        $session = session();

        return view('site/newsletter', [
            'page_info'     => $this->pageInfo(PagesModel::PAGE_MEDIA_CENTER),
            'page_active'   => 'media center',
            'act'           => 'media center',
            'flash_class'   => (string) $session->getFlashdata('class'),
            'flash_message' => (string) $session->getFlashdata('message'),
            // views/newsletter.php:59
            'rz1'           => model(ColdwellBankerNewsletterModel::class)->latest(),
        ]);
    }

    /** CI3: Home::awards (controllers/Home.php:125); Ui_model::awards reads pages id 6. */
    public function awards(): string
    {
        return view('site/awards', [
            'page_info'   => $this->pageInfo(PagesModel::PAGE_MEDIA_CENTER),
            'page_active' => 'awards',
            'act'         => 'awards',
            // views/awards.php:47
            'rz1'         => model(AwardsModel::class)->listNewestFirst(),
        ]);
    }

    /** CI3: Home::research (controllers/Home.php:139); Ui_model::research reads pages id 8.
     * Now renders via CMS page "research" (slug) with blocks: page_hero, research_tabs,
     * featured_report, research_grid, approach_panel, newsletter_cta.
     */
    public function research(): string
    {
        $preview = (string) session('admin_user') !== '' && $this->request->getGet('preview') === '1';
        $data    = Cms::page('research', $preview);
        if ($data === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $page   = $data['page'];
        $isHome = (int) $page['is_home'] === 1;

        if ($preview) {
            $this->response->setHeader('X-Robots-Tag', 'noindex');
            $this->response->setHeader('Cache-Control', 'no-store, max-age=0');
        }

        return view('cms/page', [
            'page'        => $page,
            'blocks'      => $data['blocks'],
            'is_home'     => $isHome,
            'preview'     => $preview,
            'page_active' => 'research',
            'current_url' => site_url('research'),
            'seo'         => $this->seo($page, $data['blocks'], $isHome),
        ]);
    }

    /**
     * AJAX Load More for Research grid.
     * POST: type (Project Reports|Area Insights), offset, limit
     * Returns HTML fragment of research cards.
     */
    public function loadMoreResearch(): ResponseInterface
    {
        helper('cb');

        $type     = trim((string) $this->request->getPost('type'));
        $offset   = (int) $this->request->getPost('offset');
        $limit    = (int) $this->request->getPost('limit');
        $ctaLabel = trim((string) $this->request->getPost('cta_label'));
        if ($ctaLabel === '' || mb_strlen($ctaLabel) > 60) {
            $ctaLabel = 'Download';
        }

        if (! in_array($type, ['Project Reports', 'Area Insights'], true) || $offset < 0 || $limit <= 0) {
            throw PageNotFoundException::forPageNotFound();
        }
        $limit = min($limit, 24);

        $model = model(ResearchModel::class);
        $items = $model->activeByType($type);
        $slice = array_slice($items, $offset, $limit);

        if ($slice === []) {
            return $this->response->setBody('');
        }

        $badge = $type === 'Area Insights' ? 'Insight' : 'Report';
        $html  = '';
        foreach ($slice as $item) {
            $imageUrl = ! empty($item['image_name'])
                ? base_url('uploads/research/' . rawurlencode((string) $item['image_name']))
                : base_url('assets/images/home-v2/news-research.jpg');
            $hasPdf   = ! empty($item['pdf_name']);
            $pdfUrl   = $hasPdf ? base_url('uploads/research/' . rawurlencode((string) $item['pdf_name'])) : '';
            $date     = ! empty($item['created_date']) ? date('M Y', strtotime((string) $item['created_date'])) : '';
            $meta     = $date !== '' ? $date . ' · ' . $type : $type;
            $title    = esc((string) ($item['title'] ?? ''));

            $btnHtml = '';
            if ($hasPdf) {
                $btnHtml = '<a href="' . esc($pdfUrl, 'attr') . '" target="_blank" rel="noopener noreferrer" class="cb-pill cb-pill--light cb-pill--on-section">'
                    . '<span>' . esc($ctaLabel) . '</span><span class="cb-pill__icon">' . cb_icon('arrow') . '</span>'
                    . '<span class="cb-sr"> (opens PDF in a new tab)</span></a>';
            }

            $html .= '<article class="cb-rcard">'
                . '<div class="cb-rcard__img">'
                . '<img src="' . esc($imageUrl, 'attr') . '" alt="' . esc((string) ($item['title'] ?? ''), 'attr') . '" width="384" height="250" loading="lazy" decoding="async">'
                . '</div>'
                . '<p class="cb-research-meta"><span class="cb-chip">' . esc($badge) . '</span><span>' . esc($meta) . '</span></p>'
                . '<h3 class="cb-rcard__title">' . $title . '</h3>'
                . '<div class="cb-rcard__spacer" aria-hidden="true"></div>'
                . $btnHtml
                . '</article>';
        }

        return $this->response->setBody($html);
    }

    /**
     * Lightbox page for one image (views/iframe.php). CI3 rendered an empty image
     * (src ".../uploads/<type>/") for an unknown id; that is now a 404.
     */
    private function iframe(?array $row, string $imageType): string
    {
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $row['image_type'] = $imageType;

        return view('site/iframe', ['iframe' => $row]);
    }

    /** CI3: Home::iframe_image (controllers/Home.php:662) */
    public function iframeImage(string $id): string
    {
        return $this->iframe(model(ColdwellBankerNewsModel::class)->findById((int) $id), 'news');
    }

    /** CI3: Home::iframe_knowledge_image (controllers/Home.php:668) */
    public function iframeKnowledgeImage(string $id): string
    {
        return $this->iframe(model(ColdwellBankerKnowledgeSeriesModel::class)->findById((int) $id), 'knowledge_series');
    }

    /** CI3: Home::iframe_industry_news (controllers/Home.php:674) */
    public function iframeIndustryNews(string $id): string
    {
        return $this->iframe(model(ColdwellBankerIndustryNewsModel::class)->findById((int) $id), 'industry_news');
    }

    /** CI3: Home::iframe_newsletter (controllers/Home.php:680) */
    public function iframeNewsletter(string $id): string
    {
        return $this->iframe(model(ColdwellBankerNewsletterModel::class)->findById((int) $id), 'newsletter');
    }

    /** CI3: Home::iframe_awards (controllers/Home.php:686) */
    public function iframeAwards(string $id): string
    {
        return $this->iframe(model(AwardsModel::class)->findById((int) $id), 'awards');
    }

    /**
     * Shared body of the four "load more" endpoints: POST start_no -> the next page
     * of <li> items as an HTML fragment (markup exactly as CI3 built it).
     *
     * CI3 concatenated start_no into "LIMIT start, total - start", so a missing,
     * non-integer or negative start_no, or one past the total, was an SQL error
     * (HTTP 500); those requests are now a 404. start_no == total gives an empty
     * body, as before (the JS then hides the "Load More" button).
     *
     * @param ColdwellBankerNewsModel|ColdwellBankerIndustryNewsModel|ColdwellBankerKnowledgeSeriesModel|ColdwellBankerNewsletterModel $model
     */
    private function loadMore(object $model, string $iframePath, string $uploadDir): ResponseInterface
    {
        $start = trim((string) $this->request->getPost('start_no'));
        if (preg_match('/^\+?\d+$/', $start) !== 1) {
            throw PageNotFoundException::forPageNotFound();
        }
        $startNo = (int) $start;
        $totalNo = (int) ($model->countTotal()['count'] ?? 0);
        if ($startNo > $totalNo) {
            throw PageNotFoundException::forPageNotFound();
        }

        $str = '';
        foreach ($model->loadMore($totalNo, $startNo) as $row) {
            $str .= '<li><a class="fancybox fancybox.iframe" href="' . base_url() . $iframePath . '/' . esc((string) $row['id'], 'url')
                . '" data-fancybox-group="gallery"><img src="' . base_url() . 'uploads/' . $uploadDir . '/' . esc((string) $row['image'], 'url')
                . '"  id="fancy_img" alt="" class="fancyb ox-image"><h5>' . date('d-m-Y', strtotime((string) $row['created_date']))
                . '</small></h5></a></li>';
        }

        return $this->response->setBody($str);
    }

    /** CI3: Home::load_more_newsletter (controllers/Home.php:692) */
    public function loadMoreNewsletter(): ResponseInterface
    {
        return $this->loadMore(model(ColdwellBankerNewsletterModel::class), 'iframe-newsletter', 'newsletter');
    }

    /** CI3: Home::load_more_knowledge_series (controllers/Home.php:707) */
    public function loadMoreKnowledgeSeries(): ResponseInterface
    {
        return $this->loadMore(model(ColdwellBankerKnowledgeSeriesModel::class), 'iframe-knowledge-image', 'knowledge_series');
    }

    /** CI3: Home::load_more_industry_news (controllers/Home.php:723) */
    public function loadMoreIndustryNews(): ResponseInterface
    {
        return $this->loadMore(model(ColdwellBankerIndustryNewsModel::class), 'iframe-industry-news', 'industry_news');
    }

    /** CI3: Home::load_more_news (controllers/Home.php:738) */
    public function loadMoreNews(): ResponseInterface
    {
        return $this->loadMore(model(ColdwellBankerNewsModel::class), 'iframe-image', 'news');
    }

    /**
     * <head> values for CMS pages rendered by this controller.
     */
    private function seo(array $page, array $blocks, bool $isHome): array
    {
        $legacy = [];
        if ($isHome) {
            try {
                $legacy = model(PagesModel::class)->pageById(PagesModel::PAGE_HOME) ?? [];
            } catch (\Throwable $e) {
                log_message('error', 'CMS home legacy SEO: {msg}', ['msg' => $e->getMessage()]);
            }
        }
        $pick = static function (string ...$vals): string {
            foreach ($vals as $v) {
                if (trim($v) !== '') {
                    return trim($v);
                }
            }
            return '';
        };

        $heroImage = '';
        if (($blocks[0] ?? null) !== null) {
            $b         = $blocks[0];
            $heroImage = match ($b['type']) {
                'hero_slider' => (string) ($b['items']['slide'][0]['image'] ?? ''),
                'page_hero'   => (string) ($b['settings']['image'] ?? ''),
                default       => '',
            };
        }
        $image = '';
        foreach ([(string) $page['og_image'], (string) $page['featured_image'], $heroImage] as $candidate) {
            if (($image = CmsUrl::imageUrl($candidate)) !== '') {
                break;
            }
        }

        return [
            'title'       => $pick((string) $page['meta_title'], (string) ($legacy['title'] ?? ''), (string) $page['title']),
            'description' => $pick((string) $page['meta_description'], (string) ($legacy['meta_description'] ?? ''), $page['type'] === 'article' ? (string) $page['excerpt'] : ''),
            'keywords'    => $pick((string) $page['meta_keywords'], (string) ($legacy['meta_keywords'] ?? '')),
            'image'       => $image,
            'noindex'     => (int) $page['noindex'] === 1,
        ];
    }
}
