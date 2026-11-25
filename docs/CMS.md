# CMS (pages, articles, blocks, menus, settings)

The redesigned public site (Figma file `IWmxK2teJMJes15GjWkGBK`, client-approved home `41:41`, responsive frames on page `100:57`) is driven by a small CMS. The admin manages **menus**, **pages / articles** made of **blocks**, and **site settings**. The home page is the page with `is_home = 1`.

## Data (migration `2026-11-24-000001_CreateCmsTables`, seeder `CmsSeeder`)

| Table | Purpose |
|---|---|
| `cms_pages` | `type` page \| article, `title`, unique `slug`, `is_home`, `nav_key` (menu highlight key), `excerpt`, `body` (sanitised HTML, articles), `featured_image`, `image_alt`, `status` (0 draft / 1 published), `published_at`, SEO: `meta_title`, `meta_description`, `meta_keywords`, `og_image`, `noindex` |
| `cms_blocks` | `page_id` (FK, cascade), `type` (key of `BlockTypes::TYPES`), `label` (admin only), `settings` (JSON of the type's `settings` fields), `sort_order`, `status` |
| `cms_block_items` | `block_id` (FK, cascade), `item_group` (key of the type's `groups`), columns `kicker badge title subtitle body cta_label cta_url image image_alt`, `sort_order`, `status` |
| `cms_menus` | one row per `location` (keys of `SiteSchema::MENUS`) |
| `cms_menu_items` | `menu_id` (FK), `parent_id` (one level, only where the location's `depth` is 2), `label`, `page_id` (link to a CMS page, wins over url) or `url`, `new_tab`, `active_keys` (comma list of `page_active` / `nav_key` values that highlight it), `sort_order`, `status` |
| `cms_settings` | key/value (`SiteSchema::SETTINGS`) |

## Code contract (`app/Libraries/Cms/`)

- `BlockTypes::TYPES`: every block type, with its `settings` fields, item `groups` (each with `max` and `fields`) and `hero` (when true, the block renders the site header inside itself and must be the first block). `BlockTypes::settings($type, $json)` returns the settings array with defaults. **Field types:** `text`, `textarea`, `url`, `image`, `html`, `number` and `select` (`options`). `required` and `max` are enforced server-side.
- `SiteSchema::MENUS` covers menu locations (`depth`, `max`), `SiteSchema::SETTINGS` lists the site settings, grouped, and `SiteSchema::socialIcon($url)` picks a social icon.
- `CmsUrl` is the only way to turn admin input into URLs:
  - `href($url)`: site paths become `site_url()`; http(s), `#`, `mailto:` and `tel:` are kept; `javascript:`, `data:` and `//host` return `''`.
  - `isExternal($href)`.
  - `imageUrl($image)`: a bare file name maps to `uploads/cms/<name>`, `assets/...` maps to a seeded default, anything else returns `''`.
  - `webpUrl($image)`.
- `Cms` is the read side for public pages and is cached:
  - `Cms::page(?string $slug, bool $preview=false)` returns `['page'=>row,'blocks'=>[row + settings[] + items[group][]]]` or null. Pass null for the home page.
  - `Cms::menu($location)` returns a tree of `['label','href','new_tab','active_keys','children']`.
  - `Cms::settings()` and `Cms::setting($key, $default)` read settings.
  - **Every admin write must call `Cms::clearCache()`.**

## Security rules (every CMS screen and view)

1. **Admin routes** live in an `admin` group with `['filter' => 'adminauth']`. Every state change is a POST carrying the CSRF token (`csrf_field()`). GET routes never write.
2. **Escape all output** with `esc()`, using the right context (`'attr'` for attributes). Links must go through `CmsUrl::href()` (render nothing when it returns `''`) and images through `CmsUrl::imageUrl()`.
3. **HTML fields** (`type => html`, article `body`) are sanitised with `App\Libraries\BlogContent::sanitize()` **on save**, and only that sanitised value is echoed raw.
4. **Uploads** follow the same rules as `Admin\Blog::storeUploadedImage`:
   - JPG/PNG/WEBP only, checked by finfo MIME **and** extension **and** `getimagesize`, under 5 MB.
   - The server generates the file name, and files go to `public/uploads/cms/`.
   - Never delete a file whose value contains `/` (seeded defaults), and never delete one another row still references.
5. **Ownership:** item, block and menu-item ids are always checked against their parent (page/block/menu) from the URL. A wrong parent gives a 404, or a no-op redirect for POSTs.
6. **Slugs** are slugified with `BlogContent::slugify()` and unique. They can't collide with reserved first segments: `admin`, `blog`, `blogs`, `assets`, `uploads`, `index`, `home` (the home page's slug is fixed), or any existing public route.

## Public rendering

`Site\CmsPage` renders a page: `GET /` serves the home page and `GET /<slug>` any other published page. The catch-all is registered **last** in `app/Config/Routes.php`, so every existing route still wins.
- The view `cms/page.php` holds the `<head>` (SEO from the page row), loops the blocks into `cms/blocks/<type>.php`, then renders the footer.
- When the first block is not a `hero` type, `cms/partials/header_bar.php` renders the nav on a navy bar.
- An article is a page with `type = article`. Its `body` and `featured_image` render as an article header plus the rich-text column, followed by its blocks.
- Assets: `public/assets/css/cb-site.css`, `public/assets/js/cb-site.js` and `helper('cb')`.

## Admin

The sidebar entry **CMS** contains:
- **Pages** at `admin/cms/pages`
- **Articles** at `admin/cms/pages?type=article`
- **Menus** at `admin/cms/menus`
- **Site settings** at `admin/cms/settings`

A page's edit screen covers its page fields and SEO, plus its blocks (add a block of any type, reorder, show/hide, delete). Each block opens its settings form and lists one table per item group (add, edit, reorder, show/hide, delete).
