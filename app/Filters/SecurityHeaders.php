<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Adds a Content-Security-Policy to the blog's public and admin responses.
 *
 * The site-wide layout loads a lot of third-party analytics that emit INLINE
 * <script> (Google Analytics/GTM, Facebook Pixel, Matomo, Hotjar). A strict
 * script-src would break them, so this policy deliberately restricts only the
 * sinks that do NOT touch inline scripts. That still removes real attack
 * surface, as defence in depth behind App\Libraries\BlogContent::sanitize():
 *
 *   base-uri 'self'        a sanitiser-bypass <base> tag can't rewrite relative URLs
 *   object-src 'none'      no <object>/<embed> plugin content
 *   form-action 'self'     an injected <form> can't post credentials off-site
 *   frame-ancestors 'self' clickjacking (the CSP form of X-Frame-Options)
 *
 * Locking down script-src (the net that would make any future sanitiser bypass
 * inert) is a follow-up: it needs the shared layout's inline analytics to carry
 * a per-response nonce. Until then the sanitiser is the primary XSS control.
 *
 * X-Content-Type-Options, X-Frame-Options and Referrer-Policy are already set
 * site-wide by the global `secureheaders` filter, so they are not repeated here.
 */
class SecurityHeaders implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if ($response->hasHeader('Content-Security-Policy')) {
            return $response; // don't override a stricter policy set elsewhere
        }

        $response->setHeader('Content-Security-Policy', implode('; ', [
            "base-uri 'self'",
            "object-src 'none'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]));

        return $response;
    }
}
