<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Cms\Cms;
use App\Libraries\Cms\CmsUrl;
use App\Libraries\Cms\SiteSchema;
use App\Models\CmsSettingsModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Admin > CMS > Site settings: the form is generated from SiteSchema::SETTINGS (docs/CMS.md).
 *
 * Only allow-listed keys are saved; `url` fields must pass CmsUrl::href(), `*_email` keys must
 * be valid e-mail addresses and every value respects its `max`. Saving calls Cms::clearCache().
 */
class CmsSettings extends BaseController
{
    public function index(): string|RedirectResponse
    {
        $fields = SiteSchema::settingFields();
        $model  = new CmsSettingsModel();
        $errors = [];
        $values = $model->map();

        if ($this->request->is('post')) {
            $input = [];
            foreach ($fields as $key => $f) {
                $raw = $this->request->getPost($key);
                $val = is_string($raw) ? trim(str_replace("\r\n", "\n", $raw)) : '';
                $input[$key] = $val;

                if (mb_strlen($val) > (int) $f['max']) {
                    $errors[$key] = $f['label'] . ' must be ' . (int) $f['max'] . ' characters or fewer.';
                } elseif ($f['type'] !== 'textarea' && preg_match('/[\r\n]/', $val)) {
                    $errors[$key] = $f['label'] . ' must be a single line.';
                } elseif ($val !== '' && $f['type'] === 'url' && CmsUrl::href($val) === '') {
                    $errors[$key] = $f['label'] . ': use a site path (e.g. contact-us), an http(s):// URL, #anchor, mailto: or tel: link.';
                } elseif ($val !== '' && str_ends_with($key, '_email') && filter_var($val, FILTER_VALIDATE_EMAIL) === false) {
                    $errors[$key] = $f['label'] . ' must be a valid e-mail address.';
                }
            }

            if ($errors === []) {
                $model->saveMany($input);
                Cms::clearCache();
                session()->setFlashdata('success_msg', 'Site settings saved.');

                return redirect()->to(site_url('admin/cms/settings'), 303);
            }
            $values = $input + $values; // keep what was typed
        }

        $data = [
            'title'       => 'CMS | Site settings',
            'form_title'  => 'Site settings',
            'groups'      => SiteSchema::SETTINGS,
            'values'      => $values,
            'form_errors' => $errors,
        ];
        $data['subview'] = view('admin/cms/settings', $data);

        return view('admin/_layout_main', $data);
    }
}
