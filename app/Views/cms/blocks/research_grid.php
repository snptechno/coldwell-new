<?php
/**
 * Block "research_grid" (Figma 135:637 "Section/Reports"): 3-column grid of research
 * report cards with AJAX Load More. Controlled by research_tabs.
 *
 * @var array<string, string> $s   block settings
 * @var string                $sfx id suffix for repeated blocks
 */
use App\Models\ResearchModel;

helper('cb');
$model        = model(ResearchModel::class);
$perPage      = max(1, min(24, (int) ($s['per_page'] ?? 6) ?: 6));
$ctaLabel     = trim($s['cta_label'] ?? '') ?: 'Download';
$loadLabel    = trim($s['load_label'] ?? '') ?: 'Load More';
$sectionTitle = trim($s['title'] ?? '') ?: 'All Project Reports';
$areaTitle    = trim($s['area_title'] ?? '') ?: 'All Area Insights';
$active       = ($s['active'] ?? 'project') === 'area' ? 'area' : 'project';
$headingId    = 'cb-reports-title' . $sfx;

if (! function_exists('cb_render_research_card')) {
    /** @param array<string, mixed> $item */
    function cb_render_research_card(array $item, string $type, string $ctaLabel): string
    {
        $imageUrl = ! empty($item['image_name'])
            ? base_url('uploads/research/' . rawurlencode((string) $item['image_name']))
            : base_url('assets/images/home-v2/news-research.jpg');
        $hasPdf   = ! empty($item['pdf_name']);
        $pdfUrl   = $hasPdf ? base_url('uploads/research/' . rawurlencode((string) $item['pdf_name'])) : '';
        $date     = ! empty($item['created_date']) ? date('M Y', strtotime((string) $item['created_date'])) : '';
        $badge    = $type === 'Area Insights' ? 'Insight' : 'Report';
        $meta     = $date !== '' ? $date . ' · ' . $type : $type;
        $title    = esc((string) ($item['title'] ?? ''));

        $btnHtml = '';
        if ($hasPdf) {
            $btnHtml = '<a href="' . esc($pdfUrl, 'attr') . '" target="_blank" rel="noopener noreferrer" class="cb-pill cb-pill--light cb-pill--on-section">'
                . '<span>' . esc($ctaLabel) . '</span><span class="cb-pill__icon">' . cb_icon('arrow') . '</span>'
                . '<span class="cb-sr"> (opens PDF in a new tab)</span></a>';
        }

        return '<article class="cb-rcard">'
            . '<div class="cb-rcard__img">'
            . '<img src="' . esc($imageUrl, 'attr') . '" alt="' . esc((string) ($item['title'] ?? ''), 'attr') . '" width="384" height="250" loading="lazy" decoding="async">'
            . '</div>'
            . '<p class="cb-research-meta"><span class="cb-chip">' . esc($badge) . '</span><span>' . esc($meta) . '</span></p>'
            . '<h3 class="cb-rcard__title">' . $title . '</h3>'
            . '<div class="cb-rcard__spacer" aria-hidden="true"></div>'
            . $btnHtml
            . '</article>';
    }
}

if (! function_exists('cb_render_research_grid')) {
    function cb_render_research_grid(ResearchModel $model, string $type, int $perPage, string $ctaLabel, string $loadLabel): string
    {
        $items = $model->activeByType($type);
        $total = count($items);
        if ($total === 0) {
            return '<p class="cb-p cb-research-empty">No ' . esc(strtolower($type)) . ' available right now.</p>';
        }
        $display   = array_slice($items, 0, $perPage);
        $cardsHtml = '';
        foreach ($display as $item) {
            $cardsHtml .= cb_render_research_card($item, $type, $ctaLabel);
        }

        $gridId      = $type === 'Project Reports' ? 'grid-project' : 'grid-area';
        $loadMoreBtn = '';
        if ($total > $perPage) {
            $loadMoreBtn = '<div class="cb-research-more">'
                . '<button type="button" class="cb-pill cb-pill--light cb-pill--on-section" data-research-more'
                . ' data-type="' . esc($type, 'attr') . '"'
                . ' data-offset="' . $perPage . '"'
                . ' data-per-page="' . $perPage . '"'
                . ' data-cta-label="' . esc($ctaLabel, 'attr') . '"'
                . ' data-target-grid="' . esc($gridId, 'attr') . '">'
                . '<span>' . esc($loadLabel) . '</span><span class="cb-pill__icon">' . cb_icon('arrow') . '</span>'
                . '</button></div>';
        }

        return '<div class="cb-rgrid">' . $cardsHtml . '</div>' . $loadMoreBtn;
    }
}
?>
<section class="cb-research-list" aria-labelledby="<?= esc($headingId, 'attr') ?>">
  <div class="cb-wrap">
    <h2 class="cb-h2 cb-research-list__head" id="<?= esc($headingId, 'attr') ?>" data-research-grid-heading data-project-title="<?= esc($sectionTitle, 'attr') ?>" data-area-title="<?= esc($areaTitle, 'attr') ?>"><?= esc($active === 'area' ? $areaTitle : $sectionTitle) ?></h2>

    <div id="grid-project" role="tabpanel" aria-labelledby="tab-project"<?= $active === 'project' ? '' : ' hidden' ?>>
      <?= cb_render_research_grid($model, 'Project Reports', $perPage, $ctaLabel, $loadLabel) ?>
    </div>
    <div id="grid-area" role="tabpanel" aria-labelledby="tab-area"<?= $active === 'area' ? '' : ' hidden' ?>>
      <?= cb_render_research_grid($model, 'Area Insights', $perPage, $ctaLabel, $loadLabel) ?>
    </div>
  </div>
</section>
<script>
(function () {
  document.addEventListener('click', async function (e) {
    var btn = e.target.closest('[data-research-more]');
    if (!btn || btn.disabled) return;
    var type = btn.dataset.type || '';
    var offset = parseInt(btn.dataset.offset || '0', 10);
    var perPage = parseInt(btn.dataset.perPage || '6', 10);
    var ctaLabel = btn.dataset.ctaLabel || 'Download';
    var gridWrap = document.getElementById(btn.dataset.targetGrid || '');
    if (!gridWrap) return;

    var labelEl = btn.querySelector('span');
    var origLabel = labelEl ? labelEl.textContent : 'Load More';
    btn.disabled = true;
    if (labelEl) labelEl.textContent = 'Loading...';

    try {
      var res = await fetch('<?= esc(site_url('research/load-more'), 'js') ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
          type: type,
          offset: String(offset),
          limit: String(perPage),
          cta_label: ctaLabel
        })
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      var html = await res.text();
      if (html.trim() !== '') {
        var grid = gridWrap.querySelector('.cb-rgrid');
        if (grid) grid.insertAdjacentHTML('beforeend', html);
        offset += perPage;
        btn.dataset.offset = String(offset);
        if (labelEl) labelEl.textContent = origLabel;
        btn.disabled = false;
      } else {
        btn.parentElement ? btn.parentElement.remove() : btn.remove();
      }
    } catch (err) {
      if (labelEl) labelEl.textContent = 'Try Again';
      btn.disabled = false;
    }
  });
})();
</script>
