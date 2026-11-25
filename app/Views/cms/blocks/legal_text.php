<?php
/**
 * Block "legal_text" (Figma Privacy / Terms / Disclaimer / Refund – v2, 143:3 …): "On this page"
 * list on the left (anchors), numbered clauses in an 800px reading column, then a help card.
 * Clause text: a blank line starts a new paragraph; lines starting with "- " become bullets.
 *
 * @var array<string, string>      $s      block settings
 * @var array<string, list<array>> $items  clause
 * @var string                     $sfx    id suffix for repeated blocks
 */
helper('cb');
$clauses = $items['clause'] ?? [];
$anchor  = static fn (int $i): string => 'clause-' . ($i + 1);

/** @return list<array{type:string, text?:string, items?:list<string>}> */
$parse = static function (string $body): array {
    $out = [];
    foreach (preg_split('/\R{2,}/u', trim($body)) as $chunk) {
        $lines = array_values(array_filter(array_map('trim', preg_split('/\R/u', $chunk)), 'strlen'));
        $para  = [];
        $list  = [];
        foreach ($lines as $l) {
            if (str_starts_with($l, '- ')) {
                if ($para !== []) { $out[] = ['type' => 'p', 'text' => implode(' ', $para)]; $para = []; }
                $list[] = trim(substr($l, 2));
            } else {
                if ($list !== []) { $out[] = ['type' => 'ul', 'items' => $list]; $list = []; }
                $para[] = $l;
            }
        }
        if ($para !== []) { $out[] = ['type' => 'p', 'text' => implode(' ', $para)]; }
        if ($list !== []) { $out[] = ['type' => 'ul', 'items' => $list]; }
    }

    return $out;
};
?>
<section class="cb-legal" aria-label="Page content">
  <div class="cb-legal__in">
    <?php if (count($clauses) > 1): ?>
    <nav class="cb-legal__toc" aria-label="On this page">
      <p class="cb-eyebrow cb-eyebrow--plain">On this page</p>
      <ol>
        <?php foreach ($clauses as $i => $c): ?><li><a href="#<?= esc($anchor($i), 'attr') ?>"><?= esc((string) $c['title']) ?></a></li><?php endforeach ?>
      </ol>
    </nav>
    <?php endif ?>
    <div class="cb-legal__body">
      <?php if (trim($s['updated'] ?? '') !== ''): ?><p class="cb-legal__updated">Last updated: <?= esc(trim($s['updated'])) ?></p><?php endif ?>
      <?php foreach ($clauses as $i => $c): ?>
      <section class="cb-legal__clause" id="<?= esc($anchor($i), 'attr') ?>" aria-labelledby="<?= esc($anchor($i) . '-h', 'attr') ?>">
        <h2 id="<?= esc($anchor($i) . '-h', 'attr') ?>"><span><?= $i + 1 ?>.</span> <?= esc((string) $c['title']) ?></h2>
        <?php foreach ($parse((string) $c['body']) as $part): ?>
          <?php if ($part['type'] === 'p'): ?><p><?= esc($part['text']) ?></p>
          <?php else: ?><ul><?php foreach ($part['items'] as $li): ?><li><?= esc($li) ?></li><?php endforeach ?></ul><?php endif ?>
        <?php endforeach ?>
      </section>
      <?php endforeach ?>
      <?php if (trim($s['help_title'] ?? '') !== ''): ?>
      <aside class="cb-legal__help">
        <span class="cb-legal__help-icon" aria-hidden="true"><?= cb_icon('mail') ?></span>
        <div><b><?= esc(trim($s['help_title'])) ?></b><?php if (trim($s['help_text'] ?? '') !== ''): ?><p><?= esc(trim($s['help_text'])) ?></p><?php endif ?></div>
        <?= cb_pill($s['cta_label'] ?? '', $s['cta_url'] ?? '', 'navy') ?>
      </aside>
      <?php endif ?>
    </div>
  </div>
</section>
