<?php
/** @var array|null $post_info @var array<int,string> $categories @var array<string,string> $form_errors */
$p   = $post_info ?? [];
$val = static fn (string $k, $default = '') => old($k, $p[$k] ?? $default);
$err = static fn (string $k) => ($form_errors[$k] ?? '') !== '' ? esc($form_errors[$k]) : validation_show_error($k);
$pub = $val('published_at', '');
$pubLocal = $pub !== '' && $pub !== null ? date('Y-m-d\TH:i', strtotime((string) $pub)) : date('Y-m-d\TH:i');
if (old('published_at') !== null) {
    $pubLocal = (string) old('published_at');
}
$img = static fn (?string $f) => $f ? base_url('uploads/blog/' . rawurlencode($f)) : '';
$siteHost = parse_url(base_url(), PHP_URL_HOST);
?>
<style>
  .blog-form .help-block { margin-bottom: 0; }
  .blog-form .counter { float: right; font-size: 12px; color: #777; }
  .blog-form .counter.warn { color: #d58512; } .blog-form .counter.bad { color: #d33724; }
  .serp { font-family: Arial, sans-serif; max-width: 600px; padding: 12px 14px; border: 1px solid #e3e3e3; border-radius: 6px; background: #fff; }
  .serp .serp-url { color: #202124; font-size: 13px; line-height: 1.3; word-break: break-all; }
  .serp .serp-title { color: #1a0dab; font-size: 19px; line-height: 1.3; margin: 3px 0; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
  .serp .serp-desc { color: #4d5156; font-size: 13px; line-height: 1.55; }
  .slug-prefix { background: #f4f4f4; }
  .img-preview { max-width: 220px; max-height: 140px; display: block; margin: 6px 0; border: 1px solid #ddd; }
</style>
<div class="row">
  <div class="col-md-12">
    <?= $this->include('admin/blog/_flash') ?>
    <?php if ($form_errors !== [] || validation_errors() !== []): ?>
      <div class="alert alert-danger">Please fix the highlighted fields below.</div>
    <?php endif; ?>
  </div>
  <form class="blog-form" autocomplete="off" enctype="multipart/form-data" id="blog-post-form" action="" method="post">
    <?= csrf_field() ?>
    <div class="col-md-8">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title"><?= esc($form_title) ?></h3>
          <?php if ($p !== [] && (int) $p['status'] === 1): ?>
            <a href="<?= base_url('blog/' . $p['slug']) ?>" target="_blank" rel="noopener" class="pull-right"><i class="fa fa-external-link"></i> View post</a>
          <?php endif; ?>
        </div>
        <div class="box-body">
          <div class="form-group">
            <label for="title">Title <span class="text-red">*</span></label>
            <input type="text" class="form-control input-lg" id="title" name="title" maxlength="255" value="<?= esc($val('title'), 'attr') ?>" required>
            <span class="text-red" style="display:block;"><?= $err('title') ?></span>
          </div>
          <div class="form-group">
            <label for="slug">URL</label>
            <div class="input-group">
              <span class="input-group-addon slug-prefix"><?= esc(rtrim(base_url('blog'), '/')) ?>/</span>
              <input type="text" class="form-control" id="slug" name="slug" maxlength="190" value="<?= esc($val('slug'), 'attr') ?>" placeholder="generated-from-the-title">
              <span class="input-group-btn"><button type="button" class="btn btn-default" id="slug-regen" title="Generate from title"><i class="fa fa-refresh"></i></button></span>
            </div>
            <span class="help-block" id="slug-status">Lowercase words separated by dashes. Leave empty to generate it from the title.<?= $p !== [] ? ' Changing the URL of a published post breaks existing links to it.' : '' ?></span>
            <span class="text-red" style="display:block;"><?= $err('slug') ?></span>
          </div>
          <div class="form-group">
            <label for="content">Content <span class="text-red">*</span></label>
            <textarea id="content" name="content" rows="18" class="form-control"><?= esc($val('content')) ?></textarea>
            <span class="text-red" style="display:block;"><?= $err('content') ?></span>
          </div>
          <div class="form-group">
            <label for="excerpt">Excerpt</label>
            <textarea id="excerpt" name="excerpt" rows="3" class="form-control" placeholder="Short summary shown on the blog listing. Leave empty to use the start of the content."><?= esc($val('excerpt')) ?></textarea>
          </div>
        </div>
      </div>

      <div class="box box-info">
        <div class="box-header with-border"><h3 class="box-title"><i class="fa fa-search"></i> SEO</h3></div>
        <div class="box-body">
          <label>Google preview</label>
          <div class="serp" id="serp">
            <div class="serp-url"><?= esc($siteHost) ?> &rsaquo; blog &rsaquo; <span id="serp-slug"></span></div>
            <div class="serp-title" id="serp-title"></div>
            <div class="serp-desc" id="serp-desc"></div>
          </div>
          <br>
          <div class="form-group">
            <label for="meta_title">Meta title <span class="counter" data-for="meta_title" data-ideal="60"></span></label>
            <input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?= esc($val('meta_title'), 'attr') ?>" placeholder="Defaults to the post title">
            <span class="help-block">Aim for 50-60 characters. " | Coldwell Banker India" is added automatically.</span>
            <span class="text-red" style="display:block;"><?= $err('meta_title') ?></span>
          </div>
          <div class="form-group">
            <label for="meta_description">Meta description <span class="counter" data-for="meta_description" data-ideal="160"></span></label>
            <textarea class="form-control" id="meta_description" name="meta_description" rows="3" maxlength="320" placeholder="Defaults to the excerpt"><?= esc($val('meta_description')) ?></textarea>
            <span class="help-block">Aim for 120-160 characters.</span>
            <span class="text-red" style="display:block;"><?= $err('meta_description') ?></span>
          </div>
          <div class="form-group">
            <label for="meta_keywords">Meta keywords</label>
            <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" maxlength="255" value="<?= esc($val('meta_keywords'), 'attr') ?>" placeholder="comma, separated, keywords">
          </div>
          <div class="form-group">
            <label for="canonical_url">Canonical URL</label>
            <input type="url" class="form-control" id="canonical_url" name="canonical_url" maxlength="255" value="<?= esc($val('canonical_url'), 'attr') ?>" placeholder="Leave empty to use this post's own URL">
            <span class="text-red" style="display:block;"><?= $err('canonical_url') ?></span>
          </div>
          <div class="checkbox">
            <label><input type="hidden" name="noindex" value="0"><input type="checkbox" name="noindex" value="1" <?= (string) $val('noindex', '0') === '1' ? 'checked' : '' ?>> Hide from search engines (noindex) and leave out of the sitemap</label>
          </div>
          <div class="form-group">
            <label for="og_image">Social share image (Open Graph)</label>
            <?php if (! empty($p['og_image'])): ?>
              <img src="<?= $img($p['og_image']) ?>" class="img-preview" alt="">
              <label class="checkbox-inline"><input type="checkbox" name="remove_og_image" value="1"> Remove</label>
            <?php endif; ?>
            <input type="file" id="og_image" name="og_image" accept="image/jpeg,image/png,image/webp">
            <span class="help-block">Optional, 1200 x 630 px recommended. Defaults to the featured image.</span>
            <span class="text-red" style="display:block;"><?= $err('og_image') ?></span>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="box box-success">
        <div class="box-header with-border"><h3 class="box-title">Publish</h3></div>
        <div class="box-body">
          <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status" class="form-control">
              <option value="0" <?= (string) $val('status', '0') === '0' ? 'selected' : '' ?>>Draft</option>
              <option value="1" <?= (string) $val('status', '0') === '1' ? 'selected' : '' ?>>Published</option>
            </select>
          </div>
          <div class="form-group">
            <label for="published_at">Publish date</label>
            <input type="datetime-local" id="published_at" name="published_at" class="form-control" value="<?= esc($pubLocal, 'attr') ?>">
            <span class="help-block">A future date schedules the post.</span>
            <span class="text-red" style="display:block;"><?= $err('published_at') ?></span>
          </div>
          <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-control">
              <option value="">- None -</option>
              <?php foreach ($categories as $cid => $cname): ?>
                <option value="<?= (int) $cid ?>" <?= (string) $val('category_id') === (string) $cid ? 'selected' : '' ?>><?= esc($cname) ?></option>
              <?php endforeach; ?>
            </select>
            <a href="<?= base_url('admin/blog/categories/add') ?>" target="_blank" class="help-block">+ Add a category</a>
          </div>
          <div class="form-group">
            <label for="author">Author</label>
            <input type="text" id="author" name="author" class="form-control" maxlength="150" value="<?= esc($val('author', 'Coldwell Banker India'), 'attr') ?>">
          </div>
          <?php if ($p !== []): ?>
            <p class="text-muted small">Created <?= esc($p['created_at'] ?? '-') ?> &middot; Updated <?= esc($p['updated_at'] ?? '-') ?> &middot; <?= (int) $p['views'] ?> views</p>
          <?php endif; ?>
        </div>
        <div class="box-footer">
          <a href="<?= base_url('admin/blog/posts') ?>" class="btn btn-default">Back</a>
          <button type="submit" class="btn btn-info pull-right"><i class="fa fa-save"></i> Save</button>
        </div>
      </div>

      <div class="box box-default">
        <div class="box-header with-border"><h3 class="box-title">Featured image</h3></div>
        <div class="box-body">
          <?php if (! empty($p['featured_image'])): ?>
            <img src="<?= $img($p['featured_image']) ?>" class="img-preview" alt="" id="featured-current">
            <label class="checkbox-inline"><input type="checkbox" name="remove_featured_image" value="1"> Remove</label>
          <?php endif; ?>
          <img src="" class="img-preview" alt="" id="featured-new" style="display:none">
          <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp">
          <span class="help-block">JPG, PNG or WEBP, under 5 MB.</span>
          <span class="text-red" style="display:block;"><?= $err('featured_image') ?></span>
          <div class="form-group" style="margin-top:10px">
            <label for="image_alt">Alt text</label>
            <input type="text" id="image_alt" name="image_alt" class="form-control" maxlength="255" value="<?= esc($val('image_alt'), 'attr') ?>" placeholder="Describe the image (helps SEO and accessibility)">
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script src="<?= base_url('assets/admin/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>
(function ($) {
  var base = <?= json_encode(rtrim(base_url(), '/') . '/', JSON_HEX_TAG | JSON_UNESCAPED_SLASHES) ?>;
  var postId = <?= json_encode($p['id'] ?? '') ?>;
  var csrfName = <?= json_encode(csrf_token()) ?>, csrfHash = <?= json_encode(csrf_hash()) ?>;

  var editor = CKEDITOR.replace('content', {
    height: 420,
    filebrowserImageUploadUrl: base + 'admin/blog/upload-image?' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash),
    toolbar: [
      ['Format', 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'],
      ['NumberedList', 'BulletedList', 'Blockquote', 'Outdent', 'Indent'],
      ['Link', 'Unlink', 'Image', 'Table', 'HorizontalRule', 'SpecialChar'],
      ['PasteText', 'PasteFromWord', 'Undo', 'Redo'], ['Source', 'Maximize']
    ],
    format_tags: 'p;h2;h3;h4;pre'
  });

  var slugTouched = $('#slug').val() !== '';
  function slugify(s) {
    return (s || '').toString().normalize('NFKD').replace(/[̀-ͯ]/g, '').toLowerCase()
      .replace(/&/g, ' and ').replace(/['’]/g, '').replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').substring(0, 120).replace(/-+$/, '');
  }
  var checkTimer;
  function checkSlug() {
    clearTimeout(checkTimer);
    checkTimer = setTimeout(function () {
      var s = $('#slug').val() || slugify($('#title').val());
      if (!s) { return; }
      $.getJSON(base + 'admin/blog/slug-check', { slug: s, id: postId }, function (r) {
        var $st = $('#slug-status');
        if (r.available) { $st.html('<span class="text-green"><i class="fa fa-check"></i> ' + base + 'blog/' + r.slug + ' is available</span>'); }
        else { $st.html('<span class="text-red"><i class="fa fa-times"></i> Already used. Suggestion: <a href="#" id="use-suggestion">' + r.suggestion + '</a></span>'); }
      });
    }, 350);
  }
  $(document).on('click', '#use-suggestion', function (e) { e.preventDefault(); $('#slug').val($(this).text()); slugTouched = true; refresh(); checkSlug(); });
  $('#title').on('input', function () { if (!slugTouched) { $('#slug').val(slugify(this.value)); } refresh(); checkSlug(); });
  $('#slug').on('input', function () { slugTouched = this.value !== ''; refresh(); checkSlug(); })
            .on('blur', function () { this.value = slugify(this.value); refresh(); });
  $('#slug-regen').on('click', function () { $('#slug').val(slugify($('#title').val())); slugTouched = false; refresh(); checkSlug(); });

  function plain(html) { return $('<div>').html(html).text().replace(/\s+/g, ' ').trim(); }
  function cut(s, n) { return s.length > n ? s.substring(0, n - 1).replace(/\s+\S*$/, '') + '…' : s; }
  function refresh() {
    var t = $('#meta_title').val() || $('#title').val() || 'Post title';
    var d = $('#meta_description').val() || $('#excerpt').val() || plain(editor.getData()) || 'Meta description';
    $('#serp-title').text(cut(t + ' | Coldwell Banker India', 65));
    $('#serp-desc').text(cut(d, 160));
    $('#serp-slug').text($('#slug').val() || slugify($('#title').val()));
    $('.counter').each(function () {
      var $c = $(this), len = ($('#' + $c.data('for')).val() || '').length, ideal = +$c.data('ideal');
      $c.text(len + ' / ' + ideal).removeClass('warn bad').addClass(len > ideal ? 'bad' : (len > 0 && len < ideal * 0.6 ? 'warn' : ''));
    });
  }
  $('#meta_title, #meta_description, #excerpt').on('input', refresh);
  editor.on('change', refresh);
  editor.on('instanceReady', refresh);
  refresh();

  $('#featured_image').on('change', function () {
    var f = this.files && this.files[0];
    if (f) { $('#featured-new').attr('src', URL.createObjectURL(f)).show(); }
  });
})(jQuery);
</script>
