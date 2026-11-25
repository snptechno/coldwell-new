<?php
/**
 * Block "featured_report" (Figma 135:623 "Featured Story"): large 2-column featured
 * research card (640x420 rounded photo on left, Latest Report chip, title, summary,
 * and Download PDF pill button on right). Data comes from ResearchModel.
 *
 * @var array<string, string> $s block settings
 */
use App\Models\ResearchModel;

helper('cb');
$model    = model(ResearchModel::class);
$ctaLabel = trim($s['cta_label'] ?? '') ?: 'Download PDF';
$active   = ($s['active'] ?? 'project') === 'area' ? 'area' : 'project';

if (! function_exists('cb_render_featured_report')) {
    function cb_render_featured_report(ResearchModel $model, string $type, string $ctaLabel): string
    {
        $items = $model->activeByType($type);
        if ($items === []) {
            return '<p class="cb-p cb-research-empty">No featured ' . esc(strtolower($type)) . ' available right now.</p>';
        }
        $item     = $items[0];
        $imageUrl = ! empty($item['image_name'])
            ? base_url('uploads/research/' . rawurlencode((string) $item['image_name']))
            : base_url('assets/images/home-v2/news-research.jpg');
        $hasPdf   = ! empty($item['pdf_name']);
        $pdfUrl   = $hasPdf ? base_url('uploads/research/' . rawurlencode((string) $item['pdf_name'])) : '';
        $date     = ! empty($item['created_date']) ? date('M Y', strtotime((string) $item['created_date'])) : '';
        $badge    = $type === 'Area Insights' ? 'Latest Insight' : 'Latest Report';
        $meta     = $date !== '' ? $date . ' · ' . $type : $type;
        $title    = esc((string) ($item['title'] ?? ''));
        $summary  = esc((string) ($item['description'] ?? ''));

        $btnHtml = '';
        if ($hasPdf) {
            $btnHtml = '<a href="' . esc($pdfUrl, 'attr') . '" target="_blank" rel="noopener noreferrer" class="cb-pill cb-pill--light">'
                . '<span>' . esc($ctaLabel) . '</span><span class="cb-pill__icon">' . cb_icon('arrow') . '</span>'
                . '<span class="cb-sr"> (opens PDF in a new tab)</span></a>';
        }

        return '<article class="cb-featured-report">'
            . '<div class="cb-featured-report__img">'
            . '<img src="' . esc($imageUrl, 'attr') . '" alt="' . esc((string) ($item['title'] ?? ''), 'attr') . '" width="640" height="420" loading="lazy" decoding="async">'
            . '</div>'
            . '<div class="cb-featured-report__body">'
            . '<p class="cb-research-meta"><span class="cb-chip">' . esc($badge) . '</span><span>' . esc($meta) . '</span></p>'
            . '<h2 class="cb-featured-report__title">' . $title . '</h2>'
            . ($summary !== '' ? '<p class="cb-p cb-featured-report__summary">' . $summary . '</p>' : '')
            . $btnHtml
            . '</div>'
            . '</article>';
    }
}
?>
<section class="cb-research-featured" aria-label="Featured research report">
  <div class="cb-wrap">
    <div id="panel-project" role="tabpanel" aria-labelledby="tab-project"<?= $active === 'project' ? '' : ' hidden' ?>>
      <?= cb_render_featured_report($model, 'Project Reports', $ctaLabel) ?>
    </div>
    <div id="panel-area" role="tabpanel" aria-labelledby="tab-area"<?= $active === 'area' ? '' : ' hidden' ?>>
      <?= cb_render_featured_report($model, 'Area Insights', $ctaLabel) ?>
    </div>
  </div>
</section>
