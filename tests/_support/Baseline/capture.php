<?php
/**
 * Public-site fingerprint capture / compare for the CI3 -> CI4 template port (step 5).
 *
 *   php capture.php capture <baseURL> <urls.txt> <out.json>    e.g. base http://localhost/coldwell-staging/
 *   php capture.php compare <baseline.json> <baseURL> [filter]  compare the CI4 site against the baseline
 *
 * A fingerprint is everything a visitor or search engine sees, made independent of
 * the base URL and of per-request noise (CSRF tokens, session ids, timestamps):
 * status, redirect, title, meta description, h1-h3, normalised visible text,
 * referenced assets (img/css/js) and forms (action + field names).
 */

function fetch(string $url): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => false, CURLOPT_TIMEOUT => 60,
        CURLOPT_HTTPHEADER => ['Accept: text/html'], CURLOPT_HEADER => false]);
    $body = (string) curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    return [$info['http_code'], $info['redirect_url'] ?? '', $body];
}

function fingerprint(string $base, string $path): array
{
    [$code, $redirect, $html] = fetch($base . $path);
    $rel = static fn (string $u): string => str_replace([$base, rtrim($base, '/')], ['{BASE}/', '{BASE}'], $u);
    $fp  = ['path' => $path, 'status' => $code, 'redirect' => $rel($redirect)];
    if ($code >= 300 && $code < 400) {
        return $fp;
    }
    // Strip the CI4 debug toolbar so it never counts as a difference.
    $html = preg_replace('~<script[^>]*id="debugbar_loader".*?</script>~s', '', $html);
    $html = preg_replace('~<!-- DEBUG-VIEW (START|ENDED).*?-->~', '', $html);

    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8"?>' . $html);
    libxml_clear_errors();
    $x = new DOMXPath($dom);
    $text = static fn ($n) => trim(preg_replace('/\s+/u', ' ', $n->textContent));

    $fp['title'] = ($t = $x->query('//title')->item(0)) ? $text($t) : '';
    $fp['meta_description'] = ($m = $x->query('//meta[@name="description"]/@content')->item(0)) ? trim($m->nodeValue) : '';
    $fp['headings'] = array_map($text, iterator_to_array($x->query('//h1|//h2|//h3')));

    foreach (['//script', '//style', '//noscript'] as $q) {
        foreach (iterator_to_array($x->query($q)) as $n) {
            $n->parentNode->removeChild($n);
        }
    }
    $body = $x->query('//body')->item(0);
    $fp['text'] = $body ? $text($body) : '';
    $fp['text_hash'] = md5($fp['text']);

    [, , $raw] = [null, null, $html];
    $assets = [];
    foreach (['img' => 'src', 'link' => 'href', 'script' => 'src', 'iframe' => 'src', 'source' => 'src'] as $tag => $attr) {
        if (preg_match_all('~<' . $tag . '\b[^>]*\b' . $attr . '\s*=\s*["\']([^"\']+)["\']~i', $raw, $mm)) {
            foreach ($mm[1] as $u) {
                $assets[] = $tag . ':' . preg_replace('~\?v=\w+$|\?\d+$~', '', $rel(html_entity_decode($u)));
            }
        }
    }
    sort($assets);
    $fp['assets'] = array_values(array_unique($assets));

    $forms = [];
    foreach (iterator_to_array($x->query('//form')) as $f) {
        $fields = [];
        foreach (iterator_to_array($x->query('.//input|.//select|.//textarea', $f)) as $in) {
            $name = $in->getAttribute('name');
            if ($name !== '' && ! preg_match('/^csrf/i', $name)) {
                $fields[] = $name;
            }
        }
        sort($fields);
        $forms[] = ['action' => $rel($f->getAttribute('action')), 'method' => strtolower($f->getAttribute('method') ?: 'get'), 'fields' => array_values(array_unique($fields))];
    }
    $fp['forms'] = $forms;
    $fp['links'] = count($x->query('//a[@href]'));

    return $fp;
}

$cmd = $argv[1] ?? '';
if ($cmd === 'capture') {
    [$base, $list, $out] = [rtrim($argv[2], '/') . '/', $argv[3], $argv[4]];
    $res = [];
    foreach (array_filter(array_map('trim', file($list))) as $path) {
        $path = $path === '/' ? '' : $path; // "/" in the list = home page
        $res[$path] = fingerprint($base, $path);
        printf("%-60s %s %s\n", "/$path", $res[$path]['status'], $res[$path]['title'] ?? $res[$path]['redirect']);
    }
    file_put_contents($out, json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    echo count($res) . " pages captured\n";
} elseif ($cmd === 'compare') {
    [$baseline, $base, $filter] = [json_decode(file_get_contents($argv[2]), true), rtrim($argv[3], '/') . '/', $argv[4] ?? ''];
    $ok = $bad = 0;
    foreach ($baseline as $path => $old) {
        if ($filter !== '' && ! str_contains($path, $filter)) {
            continue;
        }
        $new  = fingerprint($base, $path);
        $diff = [];
        foreach (['status', 'redirect', 'title', 'meta_description', 'headings', 'text_hash', 'assets', 'forms'] as $k) {
            if (($old[$k] ?? null) != ($new[$k] ?? null)) {
                $diff[] = $k;
            }
        }
        if (! $diff) {
            $ok++;
            echo "  SAME  /$path\n";
            continue;
        }
        $bad++;
        echo "  DIFF  /$path  [" . implode(', ', $diff) . "]\n";
        foreach ($diff as $k) {
            if ($k === 'text_hash') {
                $a = $old['text'] ?? '';
                $b = $new['text'] ?? '';
                $i = 0;
                while ($i < min(strlen($a), strlen($b)) && $a[$i] === $b[$i]) {
                    $i++;
                }
                echo "        text differs at char $i:\n          old: …" . substr($a, max(0, $i - 40), 140) . "\n          new: …" . substr($b, max(0, $i - 40), 140) . "\n";
            } elseif (in_array($k, ['assets', 'headings'], true)) {
                $o = $old[$k] ?? [];
                $n = $new[$k] ?? [];
                echo '        only old: ' . json_encode(array_values(array_diff($o, $n)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
                echo '        only new: ' . json_encode(array_values(array_diff($n, $o)), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                echo "        $k old: " . json_encode($old[$k] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
                echo "        $k new: " . json_encode($new[$k] ?? null, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
            }
        }
    }
    echo "\ncompare: " . ($ok + $bad) . " pages, $ok same, $bad different\n";
    exit($bad ? 1 : 0);
} else {
    fwrite(STDERR, "usage: capture <base> <urls> <out> | compare <baseline> <base> [filter]\n");
    exit(2);
}
