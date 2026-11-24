<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;
use App\Libraries\Cms\SiteSchema;
use App\Models\CmsMenuItemsModel;
use App\Models\CmsMenusModel;
use App\Models\CmsPagesModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Admin > CMS > Menus: the links of every menu location in SiteSchema::MENUS (docs/CMS.md).
 *
 * Routes: app/Config/Routes/admin_cms_menus.php (adminauth group). GET routes only read; the
 * cms_menus row of a location is created on demand by the first write. Every menu-item id is
 * checked against the menu of the {location} in the URL (wrong menu => 404), and every write
 * calls Cms::clearCache().
 */
class CmsMenus extends BaseController
{
    public function index(): string
    {
        $menus  = [];
        $rows   = (new CmsMenusModel())->builder()->get()->getResultArray();
        $byLoc  = array_column($rows, null, 'location');
        $counts = [];
        if ($rows !== []) {
            $q = (new CmsMenuItemsModel())->builder()->select('menu_id, COUNT(*) AS n, SUM(status = 1) AS live')
                ->whereIn('menu_id', array_column($rows, 'id'))->groupBy('menu_id')->get()->getResultArray();
            $counts = array_column($q, null, 'menu_id');
        }
        foreach (SiteSchema::MENUS as $location => $def) {
            $id               = isset($byLoc[$location]) ? (int) $byLoc[$location]['id'] : null;
            $menus[$location] = $def + [
                'location' => $location,
                'items'    => $id !== null ? (int) ($counts[$id]['n'] ?? 0) : 0,
                'live'     => $id !== null ? (int) ($counts[$id]['live'] ?? 0) : 0,
            ];
        }

        return $this->renderAdmin('admin/cms/menus/index', [
            'title'      => 'CMS | Menus',
            'form_title' => 'Menus',
            'menus'      => $menus,
        ]);
    }

    public function show($location): string
    {
        $def  = $this->location((string) $location);
        $menu = $this->menu((string) $location, false);

        return $this->renderAdmin('admin/cms/menus/items', [
            'title'      => 'CMS | Menu: ' . $def['name'],
            'form_title' => $def['name'],
            'location'   => (string) $location,
            'def'        => $def,
            'tree'       => $menu !== null ? $this->tree((int) $menu['id']) : [],
            'count'      => $menu !== null ? $this->countItems((int) $menu['id']) : 0,
            'pages'      => $this->pageTitles(),
        ]);
    }

    public function form($location, $id = null): string|RedirectResponse
    {
        $location = (string) $location;
        $def      = $this->location($location);
        $menu     = $this->menu($location, false);
        $model    = new CmsMenuItemsModel();
        $editId   = ($id !== null && $id !== '') ? (int) $id : null;
        $existing = null;
        if ($editId !== null) {
            $existing = $menu !== null ? $this->item((int) $menu['id'], $editId) : null;
            if ($existing === null) {
                throw PageNotFoundException::forPageNotFound();
            }
        }
        $menuId      = $menu !== null ? (int) $menu['id'] : null;
        $hasChildren = $existing !== null && $model->where('menu_id', $menuId)->where('parent_id', $editId)->countAllResults() > 0;
        $errors      = [];
        $input       = null;

        if ($this->request->is('post')) {
            $post  = $this->request->getPost();
            $input = [
                'label'       => trim((string) ($post['label'] ?? '')),
                'link_type'   => (string) ($post['link_type'] ?? 'url'),
                'page_id'     => trim((string) ($post['page_id'] ?? '')),
                'url'         => trim((string) ($post['url'] ?? '')),
                'parent_id'   => trim((string) ($post['parent_id'] ?? '')),
                'new_tab'     => ! empty($post['new_tab']) ? 1 : 0,
                'active_keys' => $this->normaliseKeys((string) ($post['active_keys'] ?? '')),
                'sort_order'  => trim((string) ($post['sort_order'] ?? '')),
                'status'      => (string) ($post['status'] ?? '1'),
            ];

            $validation = service('validation');
            $validation->setRules([
                'label'       => ['label' => 'Label', 'rules' => 'required|max_length[120]'],
                'link_type'   => ['label' => 'Link type', 'rules' => 'required|in_list[page,url]'],
                'active_keys' => ['label' => 'Highlight keys', 'rules' => 'permit_empty|max_length[255]'],
                'sort_order'  => ['label' => 'Sort order', 'rules' => 'permit_empty|integer'],
                'status'      => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
            ]);
            if (! $validation->run($input)) {
                $errors = $validation->getErrors();
            }

            $pageId = null;
            if ($input['link_type'] === 'page') {
                $pageId = ctype_digit($input['page_id']) ? (int) $input['page_id'] : 0;
                if ($pageId <= 0 || (new CmsPagesModel())->find($pageId) === null) {
                    $errors['page_id'] = 'Choose a CMS page.';
                }
            } elseif ($input['link_type'] === 'url') {
                if ($input['url'] === '') {
                    $errors['url'] = 'Enter a link.';
                } elseif (mb_strlen($input['url']) > 255) {
                    $errors['url'] = 'The link must be 255 characters or fewer.';
                } elseif (CmsUrl::href($input['url']) === '') {
                    $errors['url'] = 'Use a site path (e.g. contact-us), an http(s):// URL, #anchor, mailto: or tel: link.';
                }
            }

            $parentId = null;
            if ($input['parent_id'] !== '' && $input['parent_id'] !== '0') {
                $parentId = ctype_digit($input['parent_id']) ? (int) $input['parent_id'] : -1;
                $parent   = ($parentId > 0 && $menuId !== null) ? $this->item($menuId, $parentId) : null;
                if ((int) $def['depth'] < 2) {
                    $errors['parent_id'] = 'This menu has no sub-items.';
                } elseif ($parent === null || $parent['parent_id'] !== null) {
                    $errors['parent_id'] = 'Choose a top-level item of this menu.';
                } elseif ($editId !== null && $parentId === $editId) {
                    $errors['parent_id'] = 'An item cannot be its own parent.';
                } elseif ($hasChildren) {
                    $errors['parent_id'] = 'This item has sub-items, so it must stay at the top level.';
                }
            }

            if ($existing === null && $menuId !== null && $this->countItems($menuId) >= (int) $def['max']) {
                $errors['label'] = 'This menu already has the maximum of ' . (int) $def['max'] . ' links.';
            }

            if ($errors === []) {
                $menuId ??= (int) $this->menu($location, true)['id'];
                if ($existing === null && $this->countItems($menuId) >= (int) $def['max']) {
                    $errors['label'] = 'This menu already has the maximum of ' . (int) $def['max'] . ' links.';
                }
            }

            if ($errors === []) {
                $data = [
                    'menu_id'     => $menuId,
                    'parent_id'   => $parentId,
                    'label'       => $input['label'],
                    'page_id'     => $input['link_type'] === 'page' ? $pageId : null,
                    'url'         => $input['link_type'] === 'url' ? $input['url'] : null,
                    'new_tab'     => $input['new_tab'],
                    'active_keys' => $input['active_keys'] !== '' ? $input['active_keys'] : null,
                    'sort_order'  => $input['sort_order'] !== '' ? (int) $input['sort_order'] : $this->nextSort($menuId, $parentId),
                    'status'      => $input['status'] === '1' ? 1 : 0,
                ];
                if ($existing !== null) {
                    $model->update($editId, $data);
                } else {
                    $model->insert($data);
                }
                Cms::clearCache();
                session()->setFlashdata('success_msg', 'Menu link saved.');

                return redirect()->to(site_url('admin/cms/menus/' . $location));
            }
        }

        $values = $input ?? ($existing !== null ? [
            'label'       => $existing['label'],
            'link_type'   => $existing['page_id'] !== null ? 'page' : 'url',
            'page_id'     => (string) $existing['page_id'],
            'url'         => (string) $existing['url'],
            'parent_id'   => (string) $existing['parent_id'],
            'new_tab'     => (int) $existing['new_tab'],
            'active_keys' => (string) $existing['active_keys'],
            'sort_order'  => (string) $existing['sort_order'],
            'status'      => (string) $existing['status'],
        ] : [
            'label' => '', 'link_type' => 'page', 'page_id' => '', 'url' => '', 'parent_id' => (string) $this->request->getGet('parent'),
            'new_tab' => 0, 'active_keys' => '', 'sort_order' => '', 'status' => '1',
        ]);

        $parents = [];
        if ((int) $def['depth'] >= 2 && $menuId !== null && ! $hasChildren) {
            $parents = $model->select('id, label')->where('menu_id', $menuId)->where('parent_id', null)
                ->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
            $parents = array_values(array_filter($parents, static fn ($p) => (int) $p['id'] !== $editId));
        }

        return $this->renderAdmin('admin/cms/menus/form', [
            'title'        => 'CMS | ' . ($existing ? 'Edit' : 'Add') . ' menu link',
            'form_title'   => ($existing ? 'Edit link' : 'Add link') . ' — ' . $def['name'],
            'location'     => $location,
            'def'          => $def,
            'item'         => $existing,
            'values'       => $values,
            'form_errors'  => $errors,
            'pages'        => (new CmsPagesModel())->select('id, title, type, status, is_home')->orderBy('is_home', 'DESC')->orderBy('type', 'ASC')->orderBy('title', 'ASC')->findAll(),
            'parents'      => $parents,
            'has_children' => $hasChildren,
            'full'         => $existing === null && $menuId !== null && $this->countItems($menuId) >= (int) $def['max'],
        ]);
    }

    public function sort($location): RedirectResponse
    {
        $this->location((string) $location);
        $menu  = $this->menu((string) $location, false);
        $order = $this->request->getPost('order');
        if ($menu !== null && is_array($order)) {
            $model = new CmsMenuItemsModel();
            $ids   = array_map('intval', array_column($model->select('id')->where('menu_id', (int) $menu['id'])->findAll(), 'id'));
            foreach ($order as $id => $sort) {
                if (in_array((int) $id, $ids, true) && is_scalar($sort) && preg_match('/^-?\d{1,9}$/', trim((string) $sort))) {
                    $model->builder()->where('id', (int) $id)->where('menu_id', (int) $menu['id'])->update(['sort_order' => (int) $sort]);
                }
            }
            Cms::clearCache();
            session()->setFlashdata('success_msg', 'Order saved.');
        }

        return redirect()->to(site_url('admin/cms/menus/' . $location), 303);
    }

    public function status($location, $id, $status): RedirectResponse
    {
        $this->location((string) $location);
        $menu = $this->menu((string) $location, false);
        $row  = $menu !== null ? $this->item((int) $menu['id'], (int) $id) : null;
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        (new CmsMenuItemsModel())->setStatus((int) $row['id'], (int) $status);
        Cms::clearCache();
        session()->setFlashdata('success_msg', (int) $status === 1 ? 'Link shown.' : 'Link hidden.');

        return redirect()->to(site_url('admin/cms/menus/' . $location), 303);
    }

    public function delete($location, $id): RedirectResponse
    {
        $this->location((string) $location);
        $menu = $this->menu((string) $location, false);
        $row  = $menu !== null ? $this->item((int) $menu['id'], (int) $id) : null;
        if ($row === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $b = (new CmsMenuItemsModel())->builder();
        $b->where('menu_id', (int) $menu['id'])->where('parent_id', (int) $row['id'])->delete();
        $b->where('menu_id', (int) $menu['id'])->where('id', (int) $row['id'])->delete();
        Cms::clearCache();
        session()->setFlashdata('success_msg', 'Link deleted.');

        return redirect()->to(site_url('admin/cms/menus/' . $location), 303);
    }

    // ------------------------------------------------------------------

    /** @return array<string, mixed> the SiteSchema::MENUS definition, or 404 */
    private function location(string $location): array
    {
        if (! array_key_exists($location, SiteSchema::MENUS)) {
            throw PageNotFoundException::forPageNotFound();
        }

        return SiteSchema::MENUS[$location];
    }

    /** cms_menus row of a location; with $create the row is inserted when missing (writes only). */
    private function menu(string $location, bool $create): ?array
    {
        $model = new CmsMenusModel();
        $row   = $model->where('location', $location)->first();
        if ($row === null && $create) {
            $model->insert(['location' => $location, 'name' => SiteSchema::MENUS[$location]['name']]);
            $row = $model->where('location', $location)->first();
        }

        return $row;
    }

    private function item(int $menuId, int $id): ?array
    {
        return (new CmsMenuItemsModel())->where('menu_id', $menuId)->where('id', $id)->first();
    }

    private function countItems(int $menuId): int
    {
        return (new CmsMenuItemsModel())->where('menu_id', $menuId)->countAllResults();
    }

    private function nextSort(int $menuId, ?int $parentId): int
    {
        $row = (new CmsMenuItemsModel())->builder()->selectMax('sort_order', 'm')->where('menu_id', $menuId)
            ->where('parent_id', $parentId)->get()->getRowArray();

        return (int) ($row['m'] ?? 0) + 10;
    }

    /** Items as [parent + 'children' => [...]]; children whose parent is missing are listed at the top level. */
    private function tree(int $menuId): array
    {
        $rows = (new CmsMenuItemsModel())->where('menu_id', $menuId)->orderBy('sort_order', 'ASC')->orderBy('id', 'ASC')->findAll();
        $ids  = array_flip(array_map('intval', array_column($rows, 'id')));
        $tree = [];
        foreach ($rows as $r) {
            if ($r['parent_id'] === null || ! isset($ids[(int) $r['parent_id']])) {
                $tree[(int) $r['id']] = $r + ['children' => []];
            }
        }
        foreach ($rows as $r) {
            if ($r['parent_id'] !== null && isset($tree[(int) $r['parent_id']])) {
                $tree[(int) $r['parent_id']]['children'][] = $r;
            }
        }

        return array_values($tree);
    }

    /** @return array<int, array{title: string, type: string, status: int, is_home: int, slug: string}> */
    private function pageTitles(): array
    {
        $out = [];
        foreach ((new CmsPagesModel())->select('id, title, type, status, is_home, slug')->findAll() as $p) {
            $out[(int) $p['id']] = $p;
        }

        return $out;
    }

    /** "a, b ,,c" -> "a,b,c" (keys are matched case-sensitively against page_active / nav_key). */
    private function normaliseKeys(string $keys): string
    {
        $parts = array_values(array_unique(array_filter(array_map('trim', explode(',', $keys)), 'strlen')));

        return implode(',', $parts);
    }

    private function renderAdmin(string $subview, array $data): string
    {
        $data['subview'] = view($subview, $data);

        return view('admin/_layout_main', $data);
    }
}
