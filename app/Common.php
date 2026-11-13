<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

use CodeIgniter\HTTP\SiteURI;

if (! function_exists('base_url')) {
    /**
     * CI3-compatible base_url().
     *
     * The ported views build ~110 URLs as `base_url() . 'assets/...'` (and
     * `<?= base_url() ?>contact-us`), relying on CI3 returning the base URL WITH
     * a trailing slash. CI4's base_url() returns it without one. Only the
     * no-argument call differs; with a path the result is CI4's own.
     */
    function base_url($relativePath = '', ?string $scheme = null): string
    {
        $uri = service('request')->getUri();
        assert($uri instanceof SiteURI);
        $url = $uri->baseUrl($relativePath, $scheme);

        return ($relativePath === '' || $relativePath === []) ? rtrim($url, '/') . '/' : $url;
    }
}
