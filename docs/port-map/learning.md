# Port map: learning

Domain tables: `learning`, `gallery`, `gallery_thumbs`, `events`, `life_at_coldwell_banker`, `life_at_coldwell_banker_images`.

New code: `app/Models/LearningModel.php`, `GalleryModel.php`, `GalleryThumbsModel.php`, `EventsModel.php`,
`LifeAtColdwellBankerModel.php`, `LifeAtColdwellBankerImagesModel.php`.
Parity suite: `tests/_support/Parity/LearningParity.php` (`php spark parity:check learning`: 20 checks, all pass).

Conventions used below:

- Old paths are relative to the CI3 `application/` folder. `Section` means `controllers/admin/Section.php`, `Home` means `controllers/Home.php`.
- All ids are typed `int` in the new methods, and every value goes through the Query Builder or a `?` binding.
- CI3 `result_array()` maps to `findAll()`/`getResultArray()` (a list of arrays). CI3 `row_array()` maps to `getRowArray()` (one array, or null).
- The write methods take plain data. They do not touch `$_FILES`, the filesystem, the session or redirects. Write methods were reviewed but not executed against the database.

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:15 `show_overview_list` | Section.php:84 (`learning_overview`) | `LearningModel::overviewList()` | ported | `ORDER BY id DESC`, all rows. |
| models/home_model.php:21 `get_overview_data` | Section.php:99 (`update_overview`) | `LearningModel::findOverview(int $id)` | ported | Returns a row or null. |
| models/home_model.php:27 `update_overview` | Section.php:116 | `LearningModel::updateOverview(int $id, array $data): int` | ported-with-change | DB part only: updates `overview`, `university` and, when a new logo was stored, `university_logo`. Returns affected rows. The file handling moves to the upload step (see Upload rules U1). |
| models/home_model.php:50 `delete_overview` | none | none | dropped | Dead code. Running `grep -rn "delete_overview" application/` finds only the model function and a JS redirect in `views/admin/manage_overview/list.php:68` to `admin/section/delete_overview/<id>`. Neither canonical controller defines `delete_overview`, so that link has no action. |
| models/home_model.php:56 `show_gallery_list` | Section.php:127 (`learning_gallery`) | `GalleryModel::galleryList()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:62 `add_learning_gallery` | Section.php:157 | `GalleryModel::addImage(array $data): int` | ported-with-change | DB part only (title, image_name). The add form does not post `group_id`, so it is now written as `0` explicitly; `image_name` likewise defaults to `''` when no file is uploaded (CI3 only set it when a file was posted). That is the implicit default a non-strict server stored; MySQL 8 with STRICT_TRANS_TABLES rejects the insert without it (change b). Returns affected rows. See also the `date_time` item under Open points. Upload: U2. |
| models/home_model.php:86 `update_learning_gallery` | Section.php:155 | `GalleryModel::updateImage(int $id, array $data): int` | ported-with-change | DB part only. Upload: U2. |
| models/home_model.php:109 `show_image_by_id` | Section.php:141. Section.php:1491 is inside an unreachable `if($id)` within the `$id == NULL` branch. | `GalleryModel::findImage(int $id)` | ported | Returns a row or null. |
| models/home_model.php:116 `delete_learning_gallery_image` | Section.php:169 | `GalleryModel::deleteImage(int $id): array` | ported-with-change | CI3 reads the rows, unlinks `$row['image']` and deletes the row. `$row` is undefined (the rows are in `$query`), and the column is `image_name`, so on PHP 8 no file is removed and warnings are raised (change c). The new method reads the rows, deletes the row, and returns the rows it read (same `result_array()` shape). The file removal moves to the upload step (U3). |
| models/home_model.php:126 `change_status_learning_gallery` | Section.php:174 | `GalleryModel::setStatus(int $id, int $status)` (inherited from BaseModel) | ported-with-change | Uses the shared BaseModel toggle. The status is normalised to 0/1, which are the only values the list view links send (old views/admin/learning_gallery/list.php:34 links `/1` or `/0` only). It returns bool instead of affected rows; the caller ignores the value. |
| models/home_model.php:134 `show_events_list` | Section.php:184 (`learning_events`) | `EventsModel::eventList()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:140 `add_events` | Section.php:227 | `EventsModel::addEvent(array $data): int` | ported-with-change | The controller never sets `seats` (NOT NULL, no default), so it is now written as `''` explicitly. That is the implicit default a non-strict server stored; MySQL 8 strict mode rejects the insert without it (change b). Returns affected rows. |
| models/home_model.php:149 `update_events` | Section.php:225 | `EventsModel::updateEvent(int $id, array $data): int` | ported | Returns affected rows. |
| models/home_model.php:157 `change_event_status` | Section.php:238 | `EventsModel::setStatus(int $id, int $status)` (inherited) | ported-with-change | Same as `change_status_learning_gallery`: normalised to 0/1 (old views/admin/manage_events/list.php:35 sends only `/1` or `/0`), returns bool, and the caller ignores it. |
| models/home_model.php:165 `show_events_by_id` | Section.php:203 | `EventsModel::findEvent(int $id)` | ported | Returns a row or null. |
| models/home_model.php:171 `delete_event` | Section.php:246 | `EventsModel::deleteEvent(int $id): bool` | ported | Returns bool, like CI3 `delete()`. |
| models/home_model.php:1305 `show_life_at_coldwell_banker` | Section.php:1427 | `LifeAtColdwellBankerModel::entryList()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:1311 `show_life_at_coldwell_banker_by_id` | Section.php:1445 | `LifeAtColdwellBankerModel::findEntry(int $id)` | ported | Returns a row or null. |
| models/home_model.php:1317 `update_life_at_coldwell_banker` | Section.php:1462 | `LifeAtColdwellBankerModel::updateEntry(int $id, array $data): int` | ported | Updates `title` and `description`. Returns affected rows. |
| models/home_model.php:1324 `add_life_at_coldwell_banker_image` | Section.php:1494, Section.php:1514 | `LifeAtColdwellBankerImagesModel::addImage(string $filename): bool` | ported-with-change | DB part only: inserts `image` once the file is stored. CI3 always returns true. The upload rules move to the upload step (U4), and the `echo` moves to the controller step. |
| models/home_model.php:1347 `show_life_at_coldwell_banker_images` | Section.php:1476 | `LifeAtColdwellBankerImagesModel::imageList()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:1353 `show_life_at_coldwell_banker_image_by_id` | Section.php:1505 | `LifeAtColdwellBankerImagesModel::findImage(int $id)` | ported | Returns a row or null. |
| models/home_model.php:1359 `update_life_at_coldwell_banker_image` | Section.php:1512 | `LifeAtColdwellBankerImagesModel::updateImage(int $id, string $filename): int` | ported-with-change | DB part only: sets `image` after a new file is stored. CI3 returns affected rows, or null when no file was stored or the check failed; the caller ignores the value. Upload: U4. |
| models/home_model.php:1916 `delete_image_life_at_coldwell` | Section.php:1936 | `LifeAtColdwellBankerImagesModel::deleteImage(int $id): ?array` | ported-with-change | Reads the row, deletes it, and returns the row (`row_array()` shape). The file removal moves to the upload step (U5). |
| models/home_model.php:1926 `update_group_id_life_at_coldwell_gallery` | Section.php:1943 (AJAX from `views/admin/learning_gallery/list.php:85`) | `GalleryModel::updateGroupId(int $id, int $groupId): int` | ported-with-change | `id` and `group_id` come from POST and are now typed int. CI3 passed the raw strings to the Query Builder (change a). Returns affected rows. |
| models/home_model.php:1947 `show_gallery_thumbnails_list` | Section.php:1962 | `GalleryThumbsModel::thumbnailList()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:1953 `add_gallery_thumbnail` | Section.php:1995 | `GalleryThumbsModel::addThumbnail(array $data): int` | ported-with-change | DB part only (title, date_time, image_name). `group_id` is written as `0` and `image_name` defaults to `''` (no file uploaded) explicitly for MySQL 8 strict mode, as in `add_learning_gallery` (change b). Upload: U6. |
| models/home_model.php:1977 `update_gallery_thumbnail` | Section.php:1993 | `GalleryThumbsModel::updateThumbnail(int $id, array $data): int` | ported-with-change | DB part only. Upload: U6. |
| models/home_model.php:2000 `show_gallery_thumbnail_by_id` | Section.php:1977 | `GalleryThumbsModel::findThumbnail(int $id)` | ported | Returns a row or null. |
| models/home_model.php:2007 `delete_gallery_thumbnail_image` | Section.php:2007 | `GalleryThumbsModel::deleteThumbnail(int $id): array` | ported-with-change | Same undefined `$row` / wrong column as `delete_learning_gallery_image` (change c). Returns the rows read before the delete. File removal: U7. |
| models/home_model.php:2017 `change_status_learning_gallery_thumbnail` | Section.php:2012 | `GalleryThumbsModel::setStatus(int $id, int $status)` (inherited) | ported-with-change | Normalised to 0/1 (old views/admin/learning_thumb_gallery/list.php:34 sends only `/1` or `/0`) and returns bool; the caller ignores the value. |
| models/home_model.php:2025 `update_group_id_life_at_coldwell_gallery_thumb` | Section.php:2021 (AJAX from `views/admin/learning_thumb_gallery/list.php:85`) | `GalleryThumbsModel::updateGroupId(int $id, int $groupId): int` | ported-with-change | POST values are typed int (change a). |
| models/ui/Ui_model.php:428 `load_group_images_gallery` | Home.php:755 | `GalleryModel::imagesByGroup(int $groupId)` | ported-with-change | CI3 concatenated `group_id` into the SQL. It is now a typed int through the Query Builder (change a). Same filter (any status) and `ORDER BY id DESC`. |
| views/career-old.php:43 | view (not loaded by any controller) | `LifeAtColdwellBankerModel::firstEntry()` | ported | `select * from life_at_coldwell_banker` + `row_array()`: the first row, with no ORDER BY and no LIMIT, the same as CI3. |
| views/career-old.php:159 | view (not loaded by any controller) | `LifeAtColdwellBankerImagesModel::allImages()` | ported | `select * from life_at_coldwell_banker_images`, with no ORDER BY. |
| views/career.php:43 | Home.php:150 (`career`) | `LifeAtColdwellBankerModel::firstEntry()` | ported | As above. |
| views/career.php:159 | Home.php:150 | `LifeAtColdwellBankerImagesModel::allImages()` | ported | As above. |
| views/franchisees_openings.php:45 | Home.php:585 (`franchisees_openings`) | `LifeAtColdwellBankerModel::firstEntry()` | ported | As above. |
| views/franchisees_openings.php:153 | Home.php:585 | `LifeAtColdwellBankerImagesModel::allImages()` | ported | As above. |
| views/learning_development.php:53 | Home.php:93 (`learning_development`) | `LearningModel::findOverview(2)` | ported | The view hard-codes id 2; the view step passes `2`. |
| views/learning_development.php:111 | Home.php:93 | `EventsModel::activeByStartDate()` | ported | `status = '1' ORDER BY start_date ASC` (`start_date` is varchar `Y-m-d`). |
| views/learning_development.php:162 | Home.php:93 | `GalleryThumbsModel::firstActivePerGroup()` | ported-with-change | Original query: `SELECT * FROM gallery_thumbs WHERE status='1' GROUP BY group_id ORDER BY date_time DESC`. MySQL 8 rejects it (ONLY_FULL_GROUP_BY) and the row it returned per group was undefined. Chosen interpretation: the lowest-id active row of each group, ordered by `date_time DESC` (change b); this matches what MyISAM usually returned, but it is a decision, not proven parity. The parity check proves consistency with this rewrite only. Status is bound as `?`. |
| views/learning_development.php:198 | Home.php:93 | `GalleryModel::activeLightboxImages()` | ported | `status = '1' ORDER BY group_id, date_time`. |

## Upload rules

None of these handlers checked whether `move_uploaded_file()` succeeded, except U4. The upload step rebuilds them with CI4 `UploadedFile`, then calls the model methods above with the stored file name. Files under `uploads/` are not changed by this step.

- **U1, `update_overview`** (learning university logo)
  - Field: `image`. It is processed only when `$_FILES['image']['name'] != ''`.
  - Target directory: `uploads/university_logo/`.
  - Checks: no type or MIME check and no size limit in CI3.
  - File name: `rand() . time() . '.' . <extension of the client file name>`.
  - Stored in: `learning.university_logo`.
  - Old file: not unlinked.
- **U2, `add_learning_gallery` / `update_learning_gallery`** (gallery image)
  - Field: `image`, only when its name is non-empty.
  - Target directory: `uploads/learning_gallery/`.
  - Checks: no type, MIME or size check.
  - File name: `rand() . time() . '.' . <client extension>`.
  - Stored in: `gallery.image_name`.
  - Old file on update: not unlinked.
- **U3, `delete_learning_gallery_image`**
  - Intended: remove `uploads/learning_gallery/<image_name>` of the deleted row.
  - In CI3 the unlink used an undefined variable and a non-existent `image` key, so no file was removed in practice.
  - The admin step decides whether to remove the file. Take the name from `GalleryModel::deleteImage()`'s returned rows (`[0]['image_name']`).
- **U4, `add_life_at_coldwell_banker_image` / `update_life_at_coldwell_banker_image`**
  - Field: `image`, read from `$_FILES` directly (the `$files` argument is ignored). The update version runs only when the name is non-empty.
  - MIME: detected with `finfo` (FILEINFO_MIME_TYPE).
  - Acceptance condition as written: `(mime == 'image/png' && mime == 'image/jpeg' && mime == 'image/jpg') || size < 2000000`. The first part can never be true, so the effective rule is only a size below 2,000,000 bytes, with no type restriction. The apparent intent was png/jpeg/jpg and below 2,000,000 bytes.
  - Target directory: `uploads/life_at_coldwell_banker/`.
  - File name: `time() . rand() . '.' . <MIME subtype>`, for example `.jpeg` or `.png`. The extension comes from the detected MIME type, not from the client name.
  - DB write: the row is inserted or updated only if `move_uploaded_file()` succeeded.
  - Failure: on add, CI3 echoes `some error occured` (see the controller notes). On update it returns nothing.
  - Old file on update: not unlinked.
- **U5, `delete_image_life_at_coldwell`**: unlinks `uploads/life_at_coldwell_banker/<image>` of the row before deleting it. Use the row returned by `LifeAtColdwellBankerImagesModel::deleteImage()`.
- **U6, `add_gallery_thumbnail` / `update_gallery_thumbnail`**
  - Field: `image`, only when its name is non-empty.
  - Target directory: `uploads/learning_gallery_thumb/`.
  - Checks: none.
  - File name: `rand() . time() . '.' . <client extension>`.
  - Stored in: `gallery_thumbs.image_name`.
  - Old file on update: not unlinked.
- **U7, `delete_gallery_thumbnail_image`**: intended to remove `uploads/learning_gallery_thumb/<image_name>`. It has the same undefined-variable issue as U3, so no file was removed in practice. Take the name from `GalleryThumbsModel::deleteThumbnail()`'s returned rows.

## Notes for the controller step

- **Unused message variables:** every admin write action redirects afterwards. The local `$type` / `$data['message'] = "Updated sccessfully"` values are never displayed.
- **`Section::update_overview` (Section.php:90):** builds `overview` and `university` from POST and redirects to `admin/section/learning_overview/`. It needs to add `university_logo` once the upload step has stored the file.
- **`Section::add_learning_gallery` (Section.php:132):**
  - Saves only `title`. The form (`views/admin/learning_gallery/image_add.php`) also posts `datepicker`, which is not saved.
  - `gallery.date_time` is NOT NULL with no default, so an insert without it is rejected under strict mode. Map `datepicker` to `date_time`, as `add_gallery_thumbnail` already does (see Open points).
  - Redirects to `admin/section/learning_gallery/`.
- **`Section::add_events` (Section.php:191):** reads `start_date` twice. The form's `seats` input is not read; the model writes `''` for it. Redirects to `admin/section/learning_events/`.
- **`Section::add_life_at_coldwell_banker` (Section.php:1432):** when no `$id` is given, it calls `home_model::add_life_at_coldwell_banker()`, which does not exist. The admin list only links to the edit form with an id (`views/admin/life_at_coldwell_banker/list.php:45`), so only the update path is used.
- **`Section::add_life_at_coldwell_banker_image` (Section.php:1482):**
  - In the `$id == NULL` branch, the `show_image_by_id($id)` call (a gallery lookup) sits inside `if($id)`, so it never runs.
  - That branch handles the POST and then renders the form again instead of redirecting.
  - The model's `echo "some error occured"` on a rejected file belongs in the controller, for example as a flash or error message.
- **`Section::update_group_id_life_at_coldwell_gallery` / `..._thumb` (Section.php:1940, 2018):** AJAX POST of `id` and `group_id` with no response body. Cast both to int before calling `updateGroupId()`.
- **`Section::change_status_*` / `change_event_status`:** `$status` comes from the URL. Call the inherited `setStatus($id, $status)`, which normalises it to 0/1.
- **`Home::load_group_images_gallery` (Home.php:753):**
  - Reads `$_POST['group_id']` directly, builds an HTML string of fancybox links, then echoes it and calls `exit`. It should return a response instead.
  - Its only client-side caller (`views/learning_development.php:221`) is commented out.
  - Pass `(int) group_id` to `GalleryModel::imagesByGroup()`.
- **`views/admin/manage_overview/list.php:68`:** links to `admin/section/delete_overview/<id>`, but no action exists for it. `delete_overview` was dropped (see table).

## Open points

- `GalleryModel::addImage()` cannot be fully equivalent on MySQL 8 strict mode while the controller omits `date_time`. The non-strict implicit default `0000-00-00` is rejected by NO_ZERO_DATE. The controller step should pass the posted `datepicker` value as `date_time`.
