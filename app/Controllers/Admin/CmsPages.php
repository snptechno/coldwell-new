<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\BlogContent;
use App\Libraries\Cms\BlockTypes;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;
use App\Models\CmsBlockItemsModel;
use App\Models\CmsBlocksModel;
use App\Models\CmsPagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Admin > CMS > Pages / Articles, their blocks and block items (docs/CMS.md).
 *
 * Routes: app/Config/Routes/admin_cms_pages.php (adminauth group). State changes are POST
 * only and carry the CSRF token. Block / item ids are always checked against the page /
 * block in the URL (wrong parent => 404). Every write calls Cms::clearCache().
 */
class CmsPages extends BaseController
{
    private const IMAGE_MIMES = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const IMAGE_EXTS  = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_BYTES   = 5_000_000;
    private const TYPES       = ['page' => 'Page', 'article' => 'Article'];

    /** First URL segments a CMS slug may never take (plus every public GET route, see reservedSlugs()). */
    private const RESERVED = ['admin', 'blog', 'blogs', 'assets', 'uploads', 'index', 'home'];

    // ------------------------------------------------------------------
    // Pages
    // ------------------------------------------------------------------

    public function index(): string
    {
        $model   = new CmsPagesModel();
        $filters = [
            'type'   => (string) $this->request->getGet('type'),
            'status' => (string) $this->request->getGet('status'),
            'q'      => trim((string) $this->request->getGet('q')),
        ];
        if (! isset(self::TYPES[$filters['type']])) {
            $filters['type'] = '';
        }
        if ($filters['type'] !== '') {
            $model->where('type', $filters['type']);
        }
        $now = date('Y-m-d H:i:s');
        switch ($filters['status']) {
            case 'published':
                $model->where('status', 1)->groupStart()->where('published_at IS NULL')->orWhere('published_at <=', $now)->groupEnd();
                break;

            case 'scheduled':
                $model->where('status', 1)->where('published_at >', $now);
                break;

            case 'draft':
                $model->where('status', 0);
                break;

            default:
                $filters['status'] = '';
        }
        if ($filters['q'] !== '') {
            $model->groupStart()->like('title', $filters['q'])->orLike('slug', $filters['q'])->groupEnd();
        }
        $rows = $model->orderBy('is_home', 'DESC')->orderBy('updated_at', 'DESC')->orderBy('id', 'DESC')->paginate(25);

        $counts = [];
        if ($rows !== []) {
            foreach (db_connect()->table('cms_blocks')->select('page_id, COUNT(*) AS n')->whereIn('page_id', array_column($rows, 'id'))
                ->groupBy('page_id')->get()->getResultArray() as $c) {
                $counts[(int) $c['page_id']] = (int) $c['n'];
            }
        }

        $label = $filters['type'] === 'article' ? 'Articles' : ($filters['type'] === 'page' ? 'Pages' : 'Pages & articles');

        return $this->renderAdmin('admin/cms/pages/list', [
            'title'      => 'CMS | ' . $label,
            'form_title' => $label,
            'info'       => $rows,
            'counts'     => $counts,
            'pager'      => $model->pager,
            'filters'    => $filters,
            'types'      => self::TYPES,
        ]);
    }

    public function form($id = null): string|RedirectResponse
    {
        $model    = new CmsPagesModel();
        $editId   = ($id !== null && $id !== '') ? (int) $id : null;
        $existing = $editId !== null ? $model->find($editId) : null;
        if ($editId !== null && $existing === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $isHome = $existing !== null && (int) $existing['is_home'] === 1;

        $errors = [];
        $input  = null;
        if ($this->request->is('post')) {
            $input = $this->request->getPost();
            $rules = [
                'title'            => ['label' => 'Title', 'rules' => 'required|trim|max_length[255]'],
                'slug'             => ['label' => 'URL slug', 'rules' => 'permit_empty|trim|max_length[190]'],
                'type'             => ['label' => 'Type', 'rules' => 'required|in_list[page,article]'],
                'nav_key'          => ['label' => 'Menu key', 'rules' => 'permit_empty|trim|alpha_dash|max_length[60]'],
                'status'           => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
                'published_at'     => ['label' => 'Publish date', 'rules' => 'permit_empty|valid_date[Y-m-d\TH:i]'],
                'image_alt'        => ['label' => 'Image alt text', 'rules' => 'permit_empty|trim|max_length[255]'],
                'meta_title'       => ['label' => 'Meta title', 'rules' => 'permit_empty|trim|max_length[255]'],
                'meta_description' => ['label' => 'Meta description', 'rules' => 'permit_empty|trim|max_length[320]'],
                'meta_keywords'    => ['label' => 'Meta keywords', 'rules' => 'permit_empty|trim|max_length[255]'],
            ];
            $valid = $this->validate($rules);
            if (! $valid) {
                $errors = $this->validator->getErrors();
            }

            $type = $isHome ? 'page' : ((string) ($input['type'] ?? '') === 'article' ? 'article' : 'page');
            $title = trim((string) ($input['title'] ?? ''));

            // Slug: the home page keeps "home"; otherwise typed slug (strict) or one built from the title.
            if ($isHome) {
                $slug = 'home';
            } else {
                $slugInput = trim((string) ($input['slug'] ?? ''));
                if ($slugInput !== '') {
                    $slug = BlogContent::slugify($slugInput, 190);
                    if ($slug === '') {
                        $errors['slug'] = 'The URL slug must contain letters or numbers.';
                    } elseif ($this->isReservedSlug($slug)) {
                        $errors['slug'] = 'The URL "' . $slug . '" is reserved by the site. Choose another one.';
                    } elseif ($this->slugTaken($slug, $editId)) {
                        $errors['slug'] = 'This URL slug is already used by another page.';
                    }
                } else {
                    $slug = $this->uniqueSlug($title, $editId);
                    if ($slug === '' && $title !== '') {
                        $errors['slug'] = 'Enter a URL slug: the title has no letters or numbers.';
                    }
                }
            }

            $body = BlogContent::sanitize((string) ($input['body'] ?? ''));
            if ($type === 'article' && trim(strip_tags($body, '<img><iframe>')) === '') {
                $errors['body'] = 'The article text is required.';
            }

            $status = (int) ($input['status'] ?? 0) === 1 ? 1 : 0;
            $pubIn  = (string) ($input['published_at'] ?? '');
            $pubAt  = $pubIn !== '' && strtotime($pubIn) !== false
                ? date('Y-m-d H:i:s', strtotime($pubIn))
                : ($existing['published_at'] ?? date('Y-m-d H:i:s'));
            if ($isHome) {
                if ($status !== 1) {
                    $errors['status'] = 'The home page cannot be unpublished.';
                }
                if ($pubAt > date('Y-m-d H:i:s')) {
                    $errors['published_at'] = 'The home page cannot be scheduled in the future.';
                }
            }

            $image = $this->storeUploadedImage('featured_image', $errors);
            $og    = $this->storeUploadedImage('og_image', $errors);

            if ($errors === []) {
                $data = [
                    'type'             => $type,
                    'title'            => $title,
                    'slug'             => $slug,
                    'nav_key'          => trim((string) ($input['nav_key'] ?? '')) ?: null,
                    'excerpt'          => trim((string) ($input['excerpt'] ?? '')) ?: null,
                    'body'             => $body !== '' ? $body : null,
                    'image_alt'        => trim((string) ($input['image_alt'] ?? '')) ?: null,
                    'status'           => $status,
                    'published_at'     => $pubAt,
                    'meta_title'       => trim((string) ($input['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($input['meta_description'] ?? '')) ?: null,
                    'meta_keywords'    => trim((string) ($input['meta_keywords'] ?? '')) ?: null,
                    'noindex'          => ! empty($input['noindex']) ? 1 : 0,
                ];
                if ($image !== null) {
                    $data['featured_image'] = $image;
                } elseif (! empty($input['remove_featured_image'])) {
                    $data['featured_image'] = null;
                }
                if ($og !== null) {
                    $data['og_image'] = $og;
                } elseif (! empty($input['remove_og_image'])) {
                    $data['og_image'] = null;
                }

                if ($existing !== null) {
                    $model->update($editId, $data);
                    foreach (['featured_image', 'og_image'] as $f) {
                        if (array_key_exists($f, $data) && $existing[$f] && $existing[$f] !== $data[$f]) {
                            $this->releaseImage($existing[$f]);
                        }
                    }
                    $newId = $editId;
                } else {
                    $data['is_home'] = 0;
                    $newId           = (int) $model->insert($data);
                }
                Cms::clearCache();
                session()->setFlashdata('success_msg', ($type === 'article' ? 'Article' : 'Page') . ' saved.');

                return redirect()->to(site_url('admin/cms/pages/edit/' . $newId));
            }
            // Fresh uploads of a failed save are not referenced anywhere: remove them.
            foreach ([$image, $og] as $f) {
                $this->releaseImage($f);
            }
        }

        $type   = $existing['type'] ?? ((string) $this->request->getGet('type') === 'article' ? 'article' : 'page');
        $noun   = $type === 'article' ? 'article' : 'page';
        $blocks = $existing !== null ? (new CmsBlocksModel())->where('page_id', $editId)->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll() : [];

        return $this->renderAdmin('admin/cms/pages/form', [
            'title'       => 'CMS | ' . ($existing ? 'Edit ' . $noun : 'Add ' . $noun),
            'form_title'  => $existing ? 'Edit ' . $noun : 'Add ' . $noun,
            'page_info'   => $existing,
            'input'       => $input,
            'page_type'   => $type,
            'is_home'     => $isHome,
            'types'       => self::TYPES,
            'blocks'      => $blocks,
            'form_errors' => $errors,
        ]);
    }

    public function status($id, $status): RedirectResponse
    {
        $model = new CmsPagesModel();
        $row   = $model->find((int) $id);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ((int) $row['is_home'] === 1 && (int) $status !== 1) {
            session()->setFlashdata('error_msg', 'The home page cannot be unpublished.');

            return redirect()->back(303);
        }
        $model->setStatus((int) $id, (int) $status);
        Cms::clearCache();
        session()->setFlashdata('success_msg', (int) $status === 1 ? 'Published.' : 'Moved to draft.');

        return redirect()->back(303);
    }

    public function delete($id): RedirectResponse
    {
        $model = new CmsPagesModel();
        $row   = $model->find((int) $id);
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $back = site_url('admin/cms/pages' . ($row['type'] === 'article' ? '?type=article' : ''));
        if ((int) $row['is_home'] === 1) {
            session()->setFlashdata('error_msg', 'The home page cannot be deleted.');

            return redirect()->to($back, 303);
        }
        $images = [$row['featured_image'], $row['og_image']];
        $blocks = (new CmsBlocksModel())->where('page_id', (int) $id)->findAll();
        foreach ($blocks as $b) {
            $images = array_merge($images, $this->blockImages($b));
        }
        $db = db_connect();
        $db->transStart();
        if ($blocks !== []) {
            $db->table('cms_block_items')->whereIn('block_id', array_column($blocks, 'id'))->delete();
            $db->table('cms_blocks')->where('page_id', (int) $id)->delete();
        }
        $model->delete((int) $id);
        $db->transComplete();
        foreach (array_unique(array_filter($images)) as $img) {
            $this->releaseImage($img);
        }
        Cms::clearCache();
        session()->setFlashdata('success_msg', ($row['type'] === 'article' ? 'Article' : 'Page') . ' deleted.');

        return redirect()->to($back, 303);
    }

    // ------------------------------------------------------------------
    // Blocks
    // ------------------------------------------------------------------

    public function blockAdd($pageId): RedirectResponse
    {
        $page = $this->pageOr404($pageId);
        $type = (string) $this->request->getPost('type');
        $back = site_url('admin/cms/pages/edit/' . (int) $page['id']) . '#blocks';
        if (! BlockTypes::has($type)) {
            session()->setFlashdata('error_msg', 'Choose a block type.');

            return redirect()->to($back, 303);
        }
        $blocks = new CmsBlocksModel();
        $rows   = $blocks->where('page_id', (int) $page['id'])->findAll();
        if (BlockTypes::isHero($type)) {
            foreach ($rows as $r) {
                if (BlockTypes::isHero($r['type'])) {
                    session()->setFlashdata('error_msg', 'This page already has a hero block. A page can have only one, and it must be first.');

                    return redirect()->to($back, 303);
                }
            }
            $sort = $rows === [] ? 10 : min(array_map('intval', array_column($rows, 'sort_order'))) - 10;
        } else {
            $sort = $rows === [] ? 10 : max(array_map('intval', array_column($rows, 'sort_order'))) + 10;
        }
        $def      = BlockTypes::get($type);
        $settings = [];
        foreach ($def['settings'] as $k => $f) {
            $settings[$k] = isset($f['default']) ? (string) $f['default'] : '';
        }
        $blockId = (int) $blocks->insert([
            'page_id'    => (int) $page['id'],
            'type'       => $type,
            'label'      => mb_substr((string) $def['label'], 0, 120),
            'settings'   => $this->encodeSettings($settings),
            'sort_order' => $sort,
            'status'     => 0, // hidden until its content is filled in
        ]);
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Block added (hidden). Fill in its content, then set it to Visible.');

        return redirect()->to(site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . $blockId), 303);
    }

    public function blockSort($pageId): RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $back  = site_url('admin/cms/pages/edit/' . (int) $page['id']) . '#blocks';
        $order = $this->request->getPost('order');
        $order = is_array($order) ? $order : [];
        $model = new CmsBlocksModel();
        $rows  = $model->where('page_id', (int) $page['id'])->findAll();

        $new = [];
        foreach ($rows as $r) {
            $v = $order[$r['id']] ?? null;
            $new[(int) $r['id']] = is_scalar($v) && preg_match('/^-?\d{1,6}$/', trim((string) $v)) ? (int) $v : (int) $r['sort_order'];
        }
        if (! $this->heroFirst($rows, $new)) {
            session()->setFlashdata('error_msg', 'The hero block must stay the first block (lowest order number).');

            return redirect()->to($back, 303);
        }
        foreach ($new as $bid => $sort) {
            $model->update($bid, ['sort_order' => $sort]);
        }
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Block order saved.');

        return redirect()->to($back, 303);
    }

    public function blockForm($pageId, $blockId): string|RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        $def   = BlockTypes::get($block['type']);
        $saved = BlockTypes::settings($block['type'], $block['settings']);

        $errors = [];
        $input  = null;
        if ($this->request->is('post')) {
            $input  = $this->request->getPost();
            $posted = is_array($input['settings'] ?? null) ? $input['settings'] : [];
            $label  = trim((string) ($input['label'] ?? ''));
            if (mb_strlen($label) > 120) {
                $errors['label'] = 'The admin label must be at most 120 characters.';
            }
            $uploads  = [];
            $settings = [];
            foreach ($def['settings'] as $key => $field) {
                if (($field['type'] ?? 'text') === 'image') {
                    $up = $this->storeUploadedImage('upload_' . $key, $errors);
                    if ($up !== null) {
                        $uploads[] = $up;
                    }
                    $current = $saved[$key] ?? '';
                    $value   = $up ?? (! empty($input['remove_' . $key]) ? '' : $current);
                    if ($value === '' && ! empty($field['required']) && ! isset($errors['upload_' . $key])) {
                        $errors['upload_' . $key] = $field['label'] . ' is required.';
                    }
                    $settings[$key] = $value;

                    continue;
                }
                $settings[$key] = $this->cleanField($key, $field, $posted[$key] ?? '', $errors, 'settings.');
            }

            if ($errors === []) {
                (new CmsBlocksModel())->update((int) $block['id'], [
                    'label'    => $label !== '' ? $label : null,
                    'settings' => $this->encodeSettings($settings),
                    'status'   => (int) ($input['status'] ?? 0) === 1 ? 1 : 0,
                ]);
                foreach ($def['settings'] as $key => $field) {
                    if (($field['type'] ?? '') === 'image' && ($saved[$key] ?? '') !== '' && $saved[$key] !== $settings[$key]) {
                        $this->releaseImage($saved[$key]);
                    }
                }
                Cms::clearCache();
                session()->setFlashdata('success_msg', 'Block saved.');

                return redirect()->to(site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . (int) $block['id']));
            }
            foreach ($uploads as $u) {
                $this->releaseImage($u);
            }
        }

        $items = [];
        foreach ((new CmsBlockItemsModel())->where('block_id', (int) $block['id'])->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll() as $it) {
            $items[$it['item_group']][] = $it;
        }

        return $this->renderAdmin('admin/cms/blocks/form', [
            'title'       => 'CMS | Edit block',
            'form_title'  => 'Edit block: ' . $def['label'],
            'page_info'   => $page,
            'block'       => $block,
            'def'         => $def,
            'settings'    => $saved,
            'input'       => $input,
            'items'       => $items,
            'form_errors' => $errors,
        ]);
    }

    public function blockStatus($pageId, $blockId, $status): RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        (new CmsBlocksModel())->setStatus((int) $block['id'], (int) $status);
        Cms::clearCache();
        session()->setFlashdata('success_msg', (int) $status === 1 ? 'Block shown.' : 'Block hidden.');

        return redirect()->to(site_url('admin/cms/pages/edit/' . (int) $page['id']) . '#blocks', 303);
    }

    public function blockDelete($pageId, $blockId): RedirectResponse
    {
        $page   = $this->pageOr404($pageId);
        $block  = $this->blockOr404($page, $blockId);
        $images = $this->blockImages($block);
        $db     = db_connect();
        $db->transStart();
        $db->table('cms_block_items')->where('block_id', (int) $block['id'])->delete();
        (new CmsBlocksModel())->delete((int) $block['id']);
        $db->transComplete();
        foreach (array_unique($images) as $img) {
            $this->releaseImage($img);
        }
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Block deleted.');

        return redirect()->to(site_url('admin/cms/pages/edit/' . (int) $page['id']) . '#blocks', 303);
    }

    // ------------------------------------------------------------------
    // Block items
    // ------------------------------------------------------------------

    public function itemForm($pageId, $blockId, $group, $itemId = null): string|RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        $grp   = $this->groupOr404($block, (string) $group);
        $model = new CmsBlockItemsModel();
        $item  = null;
        if ($itemId !== null && $itemId !== '') {
            $item = $this->itemOr404($block, (string) $group, $itemId);
        }
        $back = site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . (int) $block['id']);

        if ($item === null && $this->groupCount($block, (string) $group) >= (int) $grp['max']) {
            session()->setFlashdata('error_msg', $grp['label'] . ': the maximum of ' . (int) $grp['max'] . ' is reached.');

            return redirect()->to($back . '#group-' . $group, 303);
        }

        $errors = [];
        $input  = null;
        if ($this->request->is('post')) {
            $input  = $this->request->getPost();
            $data   = [];
            $upload = null;
            foreach ($grp['fields'] as $col => $field) {
                if (($field['type'] ?? 'text') === 'image') {
                    $up = $this->storeUploadedImage('upload_' . $col, $errors);
                    if ($up !== null) {
                        $upload = $up;
                    }
                    $current = (string) ($item[$col] ?? '');
                    $value   = $up ?? (! empty($input['remove_' . $col]) ? '' : $current);
                    if ($value === '' && ! empty($field['required']) && ! isset($errors['upload_' . $col])) {
                        $errors['upload_' . $col] = $field['label'] . ' is required.';
                    }
                    $data[$col] = $value !== '' ? $value : null;

                    continue;
                }
                $v          = $this->cleanField($col, $field, $input[$col] ?? '', $errors);
                $data[$col] = $v !== '' ? $v : null;
            }
            $sortIn = trim((string) ($input['sort_order'] ?? ''));
            if ($sortIn !== '' && ! preg_match('/^-?\d{1,6}$/', $sortIn)) {
                $errors['sort_order'] = 'The order must be a whole number.';
            }

            if ($errors === []) {
                $data['status']     = (int) ($input['status'] ?? 1) === 1 ? 1 : 0;
                $data['sort_order'] = $sortIn !== '' ? (int) $sortIn : $this->nextItemSort($block, (string) $group);
                if ($item !== null) {
                    $model->update((int) $item['id'], $data);
                    foreach ($grp['fields'] as $col => $field) {
                        if (($field['type'] ?? '') === 'image' && ! empty($item[$col]) && $item[$col] !== $data[$col]) {
                            $this->releaseImage($item[$col]);
                        }
                    }
                } else {
                    $data['block_id']   = (int) $block['id'];
                    $data['item_group'] = (string) $group;
                    $model->insert($data);
                }
                Cms::clearCache();
                session()->setFlashdata('success_msg', 'Item saved.');

                return redirect()->to($back . '#group-' . $group);
            }
            $this->releaseImage($upload);
        }

        return $this->renderAdmin('admin/cms/blocks/item_form', [
            'title'       => 'CMS | ' . ($item ? 'Edit' : 'Add') . ' ' . $grp['label'],
            'form_title'  => ($item ? 'Edit' : 'Add') . ' item: ' . $grp['label'],
            'page_info'   => $page,
            'block'       => $block,
            'def'         => BlockTypes::get($block['type']),
            'group_key'   => (string) $group,
            'group'       => $grp,
            'item'        => $item,
            'input'       => $input,
            'form_errors' => $errors,
        ]);
    }

    public function itemSort($pageId, $blockId, $group): RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        $this->groupOr404($block, (string) $group);
        $order = $this->request->getPost('order');
        $order = is_array($order) ? $order : [];
        $model = new CmsBlockItemsModel();
        foreach ($model->where('block_id', (int) $block['id'])->where('item_group', (string) $group)->findAll() as $it) {
            $v = $order[$it['id']] ?? null;
            if (is_scalar($v) && preg_match('/^-?\d{1,6}$/', trim((string) $v))) {
                $model->update((int) $it['id'], ['sort_order' => (int) $v]);
            }
        }
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Order saved.');

        return redirect()->to(site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . (int) $block['id']) . '#group-' . $group, 303);
    }

    public function itemStatus($pageId, $blockId, $group, $itemId, $status): RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        $this->groupOr404($block, (string) $group);
        $item = $this->itemOr404($block, (string) $group, $itemId);
        (new CmsBlockItemsModel())->setStatus((int) $item['id'], (int) $status);
        Cms::clearCache();
        session()->setFlashdata('success_msg', (int) $status === 1 ? 'Item shown.' : 'Item hidden.');

        return redirect()->to(site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . (int) $block['id']) . '#group-' . $group, 303);
    }

    public function itemDelete($pageId, $blockId, $group, $itemId): RedirectResponse
    {
        $page  = $this->pageOr404($pageId);
        $block = $this->blockOr404($page, $blockId);
        $this->groupOr404($block, (string) $group);
        $item = $this->itemOr404($block, (string) $group, $itemId);
        (new CmsBlockItemsModel())->delete((int) $item['id']);
        $this->releaseImage($item['image']);
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Item deleted.');

        return redirect()->to(site_url('admin/cms/pages/' . (int) $page['id'] . '/blocks/' . (int) $block['id']) . '#group-' . $group, 303);
    }

    // ------------------------------------------------------------------
    // Lookups (ownership checks)
    // ------------------------------------------------------------------

    /** @return array<string, mixed> */
    private function pageOr404($id): array
    {
        $row = ctype_digit((string) $id) ? (new CmsPagesModel())->find((int) $id) : null;
        if (! is_array($row)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Block of THIS page with a known type, or 404. */
    private function blockOr404(array $page, $blockId): array
    {
        $row = ctype_digit((string) $blockId)
            ? (new CmsBlocksModel())->where('id', (int) $blockId)->where('page_id', (int) $page['id'])->first()
            : null;
        if (! is_array($row) || ! BlockTypes::has((string) $row['type'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    /** Item group declared by the block's type, or 404. */
    private function groupOr404(array $block, string $group): array
    {
        $groups = BlockTypes::get($block['type'])['groups'] ?? [];
        if (! isset($groups[$group])) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $groups[$group];
    }

    /** Item of THIS block and group, or 404. */
    private function itemOr404(array $block, string $group, $itemId): array
    {
        $row = ctype_digit((string) $itemId)
            ? (new CmsBlockItemsModel())->where('id', (int) $itemId)->where('block_id', (int) $block['id'])->where('item_group', $group)->first()
            : null;
        if (! is_array($row)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $row;
    }

    private function groupCount(array $block, string $group): int
    {
        return (new CmsBlockItemsModel())->where('block_id', (int) $block['id'])->where('item_group', $group)->countAllResults();
    }

    private function nextItemSort(array $block, string $group): int
    {
        $row = db_connect()->table('cms_block_items')->selectMax('sort_order', 'm')
            ->where('block_id', (int) $block['id'])->where('item_group', $group)->get()->getRowArray();

        return (int) ($row['m'] ?? 0) + 10;
    }

    /**
     * A hero block must have a strictly lower order than every other block of the page.
     *
     * @param list<array<string, mixed>> $rows
     * @param array<int, int>            $sort block id => new sort order
     */
    private function heroFirst(array $rows, array $sort): bool
    {
        $heroes = array_filter($rows, static fn ($r) => BlockTypes::isHero((string) $r['type']));
        if ($heroes === []) {
            return true;
        }
        if (count($heroes) > 1) {
            return false;
        }
        $hero = (int) reset($heroes)['id'];
        foreach ($sort as $bid => $s) {
            if ($bid !== $hero && $s <= $sort[$hero]) {
                return false;
            }
        }

        return true;
    }

    // ------------------------------------------------------------------
    // Field cleaning (block settings and items)
    // ------------------------------------------------------------------

    /**
     * Validates and normalises one non-image field value from a BlockTypes definition.
     *
     * @param array<string, mixed>  $field
     * @param array<string, string> $errors
     */
    private function cleanField(string $key, array $field, mixed $raw, array &$errors, string $errPrefix = ''): string
    {
        $type  = $field['type'] ?? 'text';
        $label = (string) ($field['label'] ?? $key);
        $value = is_scalar($raw) ? (string) $raw : '';
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $value = $type === 'html' ? BlogContent::sanitize($value) : trim($value);
        if ($type === 'text' || $type === 'url' || $type === 'number' || $type === 'select') {
            $value = str_replace("\n", ' ', $value);
        }
        $ek = $errPrefix . $key;

        $empty = $type === 'html' ? trim(strip_tags($value, '<img><iframe>')) === '' : $value === '';
        if ($empty) {
            if (! empty($field['required'])) {
                $errors[$ek] = $label . ' is required.';
            }

            return $type === 'select' ? (string) ($field['default'] ?? '') : '';
        }
        if (isset($field['max']) && mb_strlen($value) > (int) $field['max']) {
            $errors[$ek] = $label . ' must be at most ' . (int) $field['max'] . ' characters.';
        }
        switch ($type) {
            case 'url':
                if (CmsUrl::href($value) === '') {
                    $errors[$ek] = $label . ': use a site path (e.g. contact-us), a full https:// link, #anchor, mailto: or tel:.';
                }
                break;

            case 'number':
                if (! is_numeric($value)) {
                    $errors[$ek] = $label . ' must be a number.';
                }
                break;

            case 'select':
                if (! array_key_exists($value, $field['options'] ?? [])) {
                    $errors[$ek] = $label . ': choose one of the options.';
                }
                break;
        }

        return $value;
    }

    /** @param array<string, string> $settings */
    private function encodeSettings(array $settings): string
    {
        return (string) json_encode($settings, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /** Image values stored by a block (its image settings and its items). @return list<string> */
    private function blockImages(array $block): array
    {
        $out = [];
        if (BlockTypes::has((string) $block['type'])) {
            $settings = BlockTypes::settings($block['type'], $block['settings']);
            foreach (BlockTypes::get($block['type'])['settings'] as $k => $f) {
                if (($f['type'] ?? '') === 'image' && ($settings[$k] ?? '') !== '') {
                    $out[] = $settings[$k];
                }
            }
        }
        foreach (db_connect()->table('cms_block_items')->select('image')->where('block_id', (int) $block['id'])->where('image IS NOT NULL')->get()->getResultArray() as $r) {
            $out[] = (string) $r['image'];
        }

        return $out;
    }

    // ------------------------------------------------------------------
    // Slugs
    // ------------------------------------------------------------------

    private function slugTaken(string $slug, ?int $exceptId): bool
    {
        $b = (new CmsPagesModel())->where('slug', $slug);
        if ($exceptId !== null) {
            $b->where('id !=', $exceptId);
        }

        return $b->countAllResults() > 0;
    }

    private function uniqueSlug(string $title, ?int $exceptId): string
    {
        $base = BlogContent::slugify($title, 180);
        if ($base === '') {
            return '';
        }
        $slug = $base;
        for ($i = 2; $this->isReservedSlug($slug) || $this->slugTaken($slug, $exceptId); $i++) {
            $slug = $base . '-' . $i;
        }

        return $slug;
    }

    private function isReservedSlug(string $slug): bool
    {
        return in_array($slug, self::reservedSlugs(), true);
    }

    /**
     * Reserved first segments: the fixed list plus the first segment of every GET route declared
     * in app/Config/Routes/public_*.php (those routes are registered before the CMS catch-all, so a
     * page with such a slug could never be reached).
     *
     * @return list<string>
     */
    public static function reservedSlugs(): array
    {
        static $list = null;
        if ($list !== null) {
            return $list;
        }
        $list = self::RESERVED;
        foreach (glob(APPPATH . 'Config/Routes/public_*.php') ?: [] as $file) {
            $src = (string) file_get_contents($file);
            preg_match_all('/\$routes->(?:get|match|add)\(\s*(?:\[[^\]]*\]\s*,\s*)?[\'"]\/?([^\'"\/]*)/i', $src, $m);
            foreach ($m[1] as $seg) {
                $seg = strtolower($seg);
                if ($seg !== '' && preg_match('/^[a-z0-9_-]+$/', $seg)) {
                    $list[] = $seg;
                    $list[] = str_replace('_', '-', $seg);
                }
            }
        }

        return $list = array_values(array_unique($list));
    }

    // ------------------------------------------------------------------
    // Uploads (same rules as Admin\Blog::storeUploadedImage, into uploads/cms/)
    // ------------------------------------------------------------------

    /** @param array<string, string> $errors */
    private function storeUploadedImage(string $field, array &$errors): ?string
    {
        $file = $this->request->getFile($field);
        if (! $file instanceof UploadedFile || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if (! $file->isValid() || $file->hasMoved()) {
            $errors[$field] = 'The image could not be uploaded: ' . $file->getErrorString();

            return null;
        }
        if ($file->getSize() >= self::MAX_BYTES) {
            $errors[$field] = 'The image must be smaller than 5 MB.';

            return null;
        }
        $mime  = strtolower((string) $file->getMimeType());
        $ext   = strtolower((string) $file->getClientExtension());
        $image = @getimagesize($file->getTempName());
        if (! isset(self::IMAGE_MIMES[$mime]) || ! in_array($ext, self::IMAGE_EXTS, true) || $image === false || $image[0] > 8000 || $image[1] > 8000 || ($image[0] * $image[1]) > 20_000_000) {
            $errors[$field] = 'Only JPG, PNG or WEBP images are allowed.';

            return null;
        }
        $base = BlogContent::slugify(pathinfo($file->getClientName(), PATHINFO_FILENAME), 60) ?: 'image';
        $name = $base . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . self::IMAGE_MIMES[$mime];
        $dir  = self::uploadDir();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (! $file->move($dir, $name)) {
            $errors[$field] = 'The image could not be saved.';

            return null;
        }

        return $name;
    }

    public static function uploadDir(): string
    {
        return rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . CmsUrl::UPLOAD_DIR;
    }

    /**
     * Deletes an uploaded file once nothing references it any more. Seeded defaults (values
     * with a "/") are never deleted, nor is a file another CMS row still uses.
     */
    private function releaseImage(?string $name): void
    {
        $name = trim((string) $name);
        if ($name === '' || ! preg_match('/^[A-Za-z0-9_.-]+$/', $name) || str_contains($name, '..')) {
            return;
        }
        $db = db_connect();
        if ($db->table('cms_block_items')->where('image', $name)->countAllResults() > 0
            || $db->table('cms_pages')->groupStart()->where('featured_image', $name)->orWhere('og_image', $name)->groupEnd()->countAllResults() > 0
            || $db->table('cms_blocks')->like('settings', '"' . $name . '"')->countAllResults() > 0
            || $db->table('cms_settings')->where('value', $name)->countAllResults() > 0) {
            return;
        }
        $path = self::uploadDir() . DIRECTORY_SEPARATOR . $name;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /** @param array<string, mixed> $data */
    private function renderAdmin(string $subview, array $data): string
    {
        $data['subview'] = view($subview, $data);

        return view('admin/_layout_main', $data);
    }
}
