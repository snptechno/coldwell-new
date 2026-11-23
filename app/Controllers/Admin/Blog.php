<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\BlogContent;
use App\Models\BlogCategoriesModel;
use App\Models\BlogPostsModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Admin > Blog (posts and categories). Replaces the Drupal admin under /blogs.
 *
 * Every route sits in the 'adminauth' group (app/Config/Routes.php). State changes
 * (save, status toggle, delete) are POST only and carry the CSRF token.
 */
class Blog extends BaseController
{
    private const UPLOAD_DIR  = 'blog';
    private const IMAGE_MIMES = ['image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const IMAGE_EXTS  = ['jpg', 'jpeg', 'png', 'webp'];
    private const MAX_BYTES   = 5_000_000;

    // ------------------------------------------------------------------
    // Posts
    // ------------------------------------------------------------------

    public function posts(): string
    {
        $model   = new BlogPostsModel();
        $filters = [
            'q'        => (string) $this->request->getGet('q'),
            'category' => (string) $this->request->getGet('category'),
            'status'   => (string) $this->request->getGet('status'),
        ];

        return $this->renderAdmin('admin/blog/posts_list', [
            'title'      => 'Blog | Posts',
            'form_title' => 'Blog Posts',
            'info'       => $model->adminPage($filters, 20),
            'pager'      => $model->pager,
            'filters'    => $filters,
            'categories' => (new BlogCategoriesModel())->options(),
        ]);
    }

    public function post_form($id = null): string|RedirectResponse
    {
        $model    = new BlogPostsModel();
        $editId   = ($id !== null && $id !== '') ? (int) $id : null;
        $existing = $editId !== null ? $model->postById($editId) : null;
        if ($editId !== null && $existing === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $errors = [];
        if ($this->request->is('post')) {
            $rules = [
                'title'            => ['label' => 'Title', 'rules' => 'required|trim|max_length[255]'],
                'slug'             => ['label' => 'URL slug', 'rules' => 'permit_empty|trim|max_length[190]'],
                'content'          => ['label' => 'Content', 'rules' => 'required'],
                'status'           => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
                'published_at'     => ['label' => 'Publish date', 'rules' => 'permit_empty|valid_date[Y-m-d\TH:i]'],
                'category_id'      => ['label' => 'Category', 'rules' => 'permit_empty|is_natural_no_zero'],
                'meta_title'       => ['label' => 'Meta title', 'rules' => 'permit_empty|trim|max_length[255]'],
                'meta_description' => ['label' => 'Meta description', 'rules' => 'permit_empty|trim|max_length[320]'],
                'meta_keywords'    => ['label' => 'Meta keywords', 'rules' => 'permit_empty|trim|max_length[255]'],
                'canonical_url'    => ['label' => 'Canonical URL', 'rules' => 'permit_empty|trim|valid_url_strict[http,https]|max_length[255]'],
                'image_alt'        => ['label' => 'Image alt text', 'rules' => 'permit_empty|trim|max_length[255]'],
                'author'           => ['label' => 'Author', 'rules' => 'permit_empty|trim|max_length[150]'],
            ];
            $valid = $this->validate($rules);
            $post  = $this->request->getPost();

            $slugInput = trim((string) ($post['slug'] ?? ''));
            $slug      = $slugInput !== '' ? BlogContent::slugify($slugInput) : $model->uniqueSlug((string) $post['title'], $editId);
            if ($slugInput !== '' && ($slug === '' || $model->slugExists($slug, $editId))) {
                $errors['slug'] = $slug === '' ? 'The URL slug must contain letters or numbers.' : 'This URL slug is already used by another post.';
            }

            $image = $this->storeUploadedImage('featured_image', $errors);
            $og    = $this->storeUploadedImage('og_image', $errors);

            if ($valid && $errors === []) {
                $content = BlogContent::sanitize((string) $post['content']);
                $data    = [
                    'title'            => trim((string) $post['title']),
                    'slug'             => $slug,
                    'excerpt'          => trim((string) ($post['excerpt'] ?? '')) ?: BlogContent::excerpt($content, 300),
                    'content'          => $content,
                    'image_alt'        => trim((string) ($post['image_alt'] ?? '')) ?: null,
                    'category_id'      => ($post['category_id'] ?? '') !== '' ? (int) $post['category_id'] : null,
                    'author'           => trim((string) ($post['author'] ?? '')) ?: null,
                    'status'           => (int) $post['status'] === 1 ? 1 : 0,
                    'published_at'     => ($post['published_at'] ?? '') !== ''
                        ? date('Y-m-d H:i:s', strtotime((string) $post['published_at']))
                        : ($existing['published_at'] ?? date('Y-m-d H:i:s')),
                    'meta_title'       => trim((string) ($post['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($post['meta_description'] ?? '')) ?: null,
                    'meta_keywords'    => trim((string) ($post['meta_keywords'] ?? '')) ?: null,
                    'canonical_url'    => trim((string) ($post['canonical_url'] ?? '')) ?: null,
                    'noindex'          => ! empty($post['noindex']) ? 1 : 0,
                ];
                if ($image !== null) {
                    $data['featured_image'] = $image;
                } elseif (! empty($post['remove_featured_image'])) {
                    $data['featured_image'] = null;
                }
                if ($og !== null) {
                    $data['og_image'] = $og;
                } elseif (! empty($post['remove_og_image'])) {
                    $data['og_image'] = null;
                }

                if ($existing !== null) {
                    $model->updatePost($editId, $data);
                    foreach (['featured_image', 'og_image'] as $f) {
                        if (array_key_exists($f, $data) && $existing[$f] && $existing[$f] !== $data[$f]) {
                            $this->unlinkUploadFile($existing[$f]);
                        }
                    }
                    $newId = $editId;
                } else {
                    $newId = (int) $model->insert($data);
                }
                session()->setFlashdata('success_msg', 'Post saved.');

                return redirect()->to(site_url('admin/blog/posts/edit/' . $newId));
            }
            // Keep the uploaded files from being orphaned on a failed save.
            foreach ([$image, $og] as $f) {
                $this->unlinkUploadFile($f);
            }
        }

        return $this->renderAdmin('admin/blog/post_form', [
            'title'       => 'Blog | ' . ($existing ? 'Edit post' : 'Add post'),
            'form_title'  => $existing ? 'Edit post' : 'Add post',
            'post_info'   => $existing,
            'categories'  => (new BlogCategoriesModel())->options(),
            'form_errors' => $errors,
        ]);
    }

    /** AJAX: GET admin/blog/slug-check?slug=..&title=..&id=.. -> {slug, available}. */
    public function slug_check(): ResponseInterface
    {
        $model  = new BlogPostsModel();
        $id     = (int) $this->request->getGet('id') ?: null;
        $type   = (string) $this->request->getGet('type');
        $source = trim((string) $this->request->getGet('slug')) ?: (string) $this->request->getGet('title');
        $slug   = BlogContent::slugify($source);
        if ($type === 'category') {
            $available = $slug !== '' && ! (new BlogCategoriesModel())->slugExists($slug, $id);
        } else {
            $available = $slug !== '' && ! $model->slugExists($slug, $id);
        }

        return $this->response->setJSON([
            'slug'       => $slug,
            'available'  => $available,
            'suggestion' => $type === 'category' ? $slug : ($slug !== '' ? $model->uniqueSlug($slug, $id) : ''),
        ]);
    }

    public function post_status($id, $status): RedirectResponse
    {
        (new BlogPostsModel())->setStatus((int) $id, (int) $status);
        session()->setFlashdata('success_msg', (int) $status === 1 ? 'Post published.' : 'Post moved to draft.');

        return redirect()->back(303);
    }

    public function post_delete($id): RedirectResponse
    {
        $model = new BlogPostsModel();
        $row   = $model->postById((int) $id);
        if ($row !== null) {
            $model->deletePost((int) $id);
            $this->unlinkUploadFile($row['featured_image']);
            $this->unlinkUploadFile($row['og_image']);
        }
        session()->setFlashdata('success_msg', 'Post deleted.');

        return redirect()->to(site_url('admin/blog/posts'), 303);
    }

    /**
     * CKEditor 4.5 image dialog upload (filebrowserImageUploadUrl). CKEditor posts the file from
     * a hidden iframe and cannot add the CSRF field, so the route is excluded from the global CSRF
     * filter and the token travels in the query string and is checked here instead.
     */
    public function upload_image(): ResponseInterface
    {
        $funcNum = (int) $this->request->getGet('CKEditorFuncNum');
        $token   = (string) $this->request->getGet(csrf_token());
        $errors  = [];
        $name    = null;
        if ($token === '' || ! hash_equals(csrf_hash(), $token)) {
            $errors['upload'] = 'Security token expired. Reload the page and try again.';
        } else {
            $name = $this->storeUploadedImage('upload', $errors);
        }
        $url = $name !== null ? base_url('uploads/blog/' . $name) : '';
        $msg = $name !== null ? '' : ($errors['upload'] ?? 'No image received.');

        return $this->response->setContentType('text/html')->setBody(
            '<script>window.parent.CKEDITOR.tools.callFunction(' . $funcNum . ', '
            . json_encode($url, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ', '
            . json_encode($msg, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) . ');</script>'
        );
    }

    // ------------------------------------------------------------------
    // Categories
    // ------------------------------------------------------------------

    public function categories(): string
    {
        return $this->renderAdmin('admin/blog/categories_list', [
            'title'      => 'Blog | Categories',
            'form_title' => 'Blog Categories',
            'info'       => (new BlogCategoriesModel())->listForAdmin(),
        ]);
    }

    public function category_form($id = null): string|RedirectResponse
    {
        $model    = new BlogCategoriesModel();
        $editId   = ($id !== null && $id !== '') ? (int) $id : null;
        $existing = $editId !== null ? $model->categoryById($editId) : null;
        if ($editId !== null && $existing === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        $errors = [];
        if ($this->request->is('post')) {
            $rules = [
                'name'             => ['label' => 'Name', 'rules' => 'required|trim|max_length[150]'],
                'slug'             => ['label' => 'URL slug', 'rules' => 'permit_empty|trim|max_length[190]'],
                'meta_title'       => ['label' => 'Meta title', 'rules' => 'permit_empty|trim|max_length[255]'],
                'meta_description' => ['label' => 'Meta description', 'rules' => 'permit_empty|trim|max_length[320]'],
                'sort_order'       => ['label' => 'Sort order', 'rules' => 'permit_empty|integer'],
                'status'           => ['label' => 'Status', 'rules' => 'required|in_list[0,1]'],
            ];
            $valid = $this->validate($rules);
            $post  = $this->request->getPost();
            $slug  = BlogContent::slugify(trim((string) ($post['slug'] ?? '')) ?: (string) $post['name']);
            if ($slug === '') {
                $errors['slug'] = 'The URL slug must contain letters or numbers.';
            } elseif ($model->slugExists($slug, $editId)) {
                $errors['slug'] = 'This URL slug is already used by another category.';
            }
            if ($valid && $errors === []) {
                $data = [
                    'name'             => trim((string) $post['name']),
                    'slug'             => $slug,
                    'description'      => trim((string) ($post['description'] ?? '')) ?: null,
                    'meta_title'       => trim((string) ($post['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($post['meta_description'] ?? '')) ?: null,
                    'sort_order'       => (int) ($post['sort_order'] ?? 0),
                    'status'           => (int) $post['status'] === 1 ? 1 : 0,
                ];
                $existing ? $model->update($editId, $data) : $model->insert($data);
                session()->setFlashdata('success_msg', 'Category saved.');

                return redirect()->to(site_url('admin/blog/categories'));
            }
        }

        return $this->renderAdmin('admin/blog/category_form', [
            'title'       => 'Blog | ' . ($existing ? 'Edit category' : 'Add category'),
            'form_title'  => $existing ? 'Edit category' : 'Add category',
            'cat_info'    => $existing,
            'form_errors' => $errors,
        ]);
    }

    public function category_status($id, $status): RedirectResponse
    {
        (new BlogCategoriesModel())->setStatus((int) $id, (int) $status);
        session()->setFlashdata('success_msg', 'Updated successfully');

        return redirect()->to(site_url('admin/blog/categories'), 303);
    }

    public function category_delete($id): RedirectResponse
    {
        (new BlogCategoriesModel())->deleteCategory((int) $id);
        session()->setFlashdata('success_msg', 'Category deleted. Its posts are now uncategorised.');

        return redirect()->to(site_url('admin/blog/categories'), 303);
    }

    // ------------------------------------------------------------------
    // Helpers (same rules as Admin\Section::storeUploadedImage, plus webp)
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
        $mime = strtolower((string) $file->getMimeType());
        $ext  = strtolower((string) $file->getClientExtension());
        if (! isset(self::IMAGE_MIMES[$mime]) || ! in_array($ext, self::IMAGE_EXTS, true) || @getimagesize($file->getTempName()) === false) {
            $errors[$field] = 'Only JPG, PNG or WEBP images are allowed.';

            return null;
        }
        $base = BlogContent::slugify(pathinfo($file->getClientName(), PATHINFO_FILENAME), 60) ?: 'image';
        $name = $base . '-' . date('YmdHis') . '-' . bin2hex(random_bytes(3)) . '.' . self::IMAGE_MIMES[$mime];
        $dir  = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . self::UPLOAD_DIR;
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        if (! $file->move($dir, $name)) {
            $errors[$field] = 'The image could not be saved.';

            return null;
        }

        return $name;
    }

    private function unlinkUploadFile(?string $filename): void
    {
        $filename = trim((string) $filename);
        if ($filename === '') {
            return;
        }
        $path = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . self::UPLOAD_DIR . DIRECTORY_SEPARATOR . basename($filename);
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
