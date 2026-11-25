<?php
/**
 * Block "research_tabs" (Figma 135:618 "Tabs"): centered pill filter tabs for the
 * Research page. Switches both the featured_report panels (#panel-project / #panel-area)
 * and the research_grid panels (#grid-project / #grid-area).
 *
 * @var array<string, string> $s block settings
 */
$active = ($s['active'] ?? 'project') === 'area' ? 'area' : 'project';
$projectLabel = trim($s['project_label'] ?? '') ?: 'Project Reports';
$areaLabel    = trim($s['area_label'] ?? '') ?: 'Area Insights';
?>
<section class="cb-research-tabs" aria-label="Research category">
  <div class="cb-wrap">
    <div class="cb-tabs" role="tablist" aria-label="Research type filter" data-research-tabs>
      <button type="button" id="tab-project" role="tab" class="cb-tab" data-tab="project" aria-selected="<?= $active === 'project' ? 'true' : 'false' ?>" aria-controls="panel-project grid-project"<?= $active === 'project' ? '' : ' tabindex="-1"' ?>><?= esc($projectLabel) ?></button>
      <button type="button" id="tab-area" role="tab" class="cb-tab" data-tab="area" aria-selected="<?= $active === 'area' ? 'true' : 'false' ?>" aria-controls="panel-area grid-area"<?= $active === 'area' ? '' : ' tabindex="-1"' ?>><?= esc($areaLabel) ?></button>
    </div>
  </div>
</section>
<script>
(function () {
  var tablist = document.querySelector('[data-research-tabs]');
  if (!tablist) return;
  var tabs = tablist.querySelectorAll('[role="tab"]');

  function activate(tab) {
    tabs.forEach(function (b) {
      var on = b.dataset.tab === tab;
      b.setAttribute('aria-selected', on ? 'true' : 'false');
      if (on) b.removeAttribute('tabindex');
      else b.setAttribute('tabindex', '-1');
    });
    var panels = {
      project: [document.getElementById('panel-project'), document.getElementById('grid-project')],
      area: [document.getElementById('panel-area'), document.getElementById('grid-area')]
    };
    Object.keys(panels).forEach(function (key) {
      var show = key === tab;
      panels[key].forEach(function (el) {
        if (!el) return;
        el.hidden = !show;
      });
    });
    var gridHeading = document.querySelector('[data-research-grid-heading]');
    if (gridHeading) {
      var nextTitle = tab === 'area'
        ? (gridHeading.dataset.areaTitle || 'All Area Insights')
        : (gridHeading.dataset.projectTitle || 'All Project Reports');
      gridHeading.textContent = nextTitle;
    }
  }

  tabs.forEach(function (btn) {
    btn.addEventListener('click', function () {
      activate(btn.dataset.tab);
    });
  });
})();
</script>
