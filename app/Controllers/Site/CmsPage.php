<?php

namespace App\Controllers\Site;

use App\Controllers\BaseController;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;
use App\Models\PagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Public CMS pages (docs/CMS.md "Public rendering").
 *
 *   GET /                 home page (cms_pages.is_home = 1); also /index and /index/<anything> (CI3)
 *   GET /<slug>           any other published page or article (catch-all route, registered last)
 *
 * Preview: a logged-in admin adds ?preview=1 to see drafts, scheduled pages and hidden
 * blocks; the response then carries X-Robots-Tag: noindex and a "Preview" ribbon.
 */
class CmsPage extends BaseController
{
    /** CI3: Home::index($id = '') accepted and ignored an optional segment; so does this. */
    public function home(?string $ignored = null): string
    {
        return $this->render(null);
    }

    public function show(string $slug): RedirectResponse|string
    {
        if (! preg_match('/^[a-z0-9][a-z0-9-]{0,189}$/', $slug)) {
            throw PageNotFoundException::forPageNotFound();
        }
        $preview = $this->isPreview();

        // The home page lives at "/" only.
        $home = Cms::page(null, $preview);
        if ($home !== null && $home['page']['slug'] === $slug) {
            return redirect()->to(site_url('/') . ($preview ? '?preview=1' : ''), 301);
        }

        return $this->render($slug);
    }

    private function isPreview(): bool
    {
        return (string) session('admin_user') !== '' && $this->request->getGet('preview') === '1';
    }

    private function render(?string $slug): string
    {
        $preview = $this->isPreview();
        $data    = Cms::page($slug, $preview);
        if ($data === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $page   = $data['page'];
        $isHome = (int) $page['is_home'] === 1;

        // Hero blocks hold the site header, so only the first block may be one; a hero
        // further down would repeat the header (and its ids), so it is skipped.
        $blocks = [];
        foreach ($data['blocks'] as $i => $b) {
            if ($i > 0 && BlockTypes::isHero($b['type'])) {
                continue;
            }
            $blocks[] = $b;
        }

        if ($preview) {
            $this->response->setHeader('X-Robots-Tag', 'noindex');
            $this->response->setHeader('Cache-Control', 'no-store, max-age=0');
        }

        return view('cms/page', [
            'page'        => $page,
            'blocks'      => $blocks,
            'is_home'     => $isHome,
            'preview'     => $preview,
            'page_active' => trim((string) $page['nav_key']) !== '' ? trim((string) $page['nav_key']) : (string) $page['slug'],
            'current_url' => $isHome ? site_url('/') : site_url($page['slug']),
            'seo'         => $this->seo($page, $blocks, $isHome),
        ]);
    }

    /**
     * <head> values. The home page falls back to the legacy `pages` row (id 1) the old
     * home page used, so its title/description stay the same until an admin sets them.
     *
     * @return array{title: string, description: string, keywords: string, image: string, noindex: bool}
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
