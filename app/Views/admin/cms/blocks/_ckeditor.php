<?php /* CKEditor on every textarea.cms-html (same toolbar as the blog editor). HTML is sanitised again on save. */ ?>
<script src="<?= base_url('assets/admin/plugins/ckeditor/ckeditor.js') ?>"></script>
<script>
(function () {
  if (!window.CKEDITOR) { return; }
  var base = <?= json_encode(rtrim(base_url(), '/') . '/', JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;
  var csrfName = <?= json_encode(csrf_token(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  var csrfHash = <?= json_encode(csrf_hash(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
  var areas = document.querySelectorAll('textarea.cms-html');
  for (var i = 0; i < areas.length; i++) {
    CKEDITOR.replace(areas[i].id, {
      height: 320,
      filebrowserImageUploadUrl: base + 'admin/blog/upload-image?' + encodeURIComponent(csrfName) + '=' + encodeURIComponent(csrfHash),
      toolbar: [
        ['Format', 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat'],
        ['NumberedList', 'BulletedList', 'Blockquote', 'Outdent', 'Indent'],
        ['Link', 'Unlink', 'Image', 'Table', 'HorizontalRule', 'SpecialChar'],
        ['PasteText', 'PasteFromWord', 'Undo', 'Redo'], ['Source', 'Maximize']
      ],
      format_tags: 'p;h2;h3;h4;pre'
    });
  }
})();
</script>
