# Port map: cms

Domain tables: `pages`, `corporate_teams`, `user` (and `footer_menu`, see below).
Models: `app/Models/PagesModel.php`, `app/Models/CorporateTeamsModel.php`, `app/Models/UserModel.php`.
Parity suite: `tests/_support/Parity/CmsParity.php` (`php spark parity:check cms`, 32 checks).

Old paths are relative to the CI3 `application/` folder. "Section" is `controllers/admin/Section.php`,
"Home" is `controllers/Home.php`, "Login" is `controllers/admin/Login.php`.

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:367 `show_corporate_teams` | Section.php:414 (`manage_corporate_teams`) | `CorporateTeamsModel::listForAdmin()` | ported | All rows, `ORDER BY order_no ASC`. |
| models/home_model.php:374 `add_corporate_team` | Section.php:454 (`update_corporate_team`, no id) | `CorporateTeamsModel::addMember(array $data): int` | ported-with-change | Returns affected rows as before. File handling moved out (see Upload rules); the caller passes the stored file name as `image`. `order_no` and `image` are NOT NULL without a default; when absent they are filled with `0` / `''`, the values a non-strict server stored implicitly (a strict-mode server rejects the CI3 insert). Fields filtered by `$allowedFields`. |
| models/home_model.php:397 `update_corporate_team` | Section.php:452 (`update_corporate_team`, with id) | `CorporateTeamsModel::updateMember(int $id, array $data): int` | ported | Returns affected rows. File handling moved out (see Upload rules); pass `image` only when a new file was stored. Fields filtered by `$allowedFields`. |
| models/home_model.php:420 `show_corporate_team_by_id` | Section.php:433 | `CorporateTeamsModel::memberById(int $id): ?array` | ported | `row_array()` -> array or null. |
| models/home_model.php:426 `delete_corporate_team` | Section.php:464 (`delete_corporate_team`) | `CorporateTeamsModel::deleteMember(int $id): ?array` | ported-with-change | CI3 read the row into `$query` but unlinked `$row['image']` (undefined variable), so the row was deleted and the image file was left behind. Now returns the deleted row (null if missing) so the caller can remove the image (see Upload rules). CI3 returned the `result_array()`; the only caller ignores the return value. The work-list also lists `pages` for this item; the function only touches `corporate_teams`. |
| models/home_model.php:437 `show_pages_list` | Section.php:482 (`list_pages`) | `PagesModel::listForAdmin()` | ported | `ORDER BY id DESC`. |
| models/home_model.php:443 `getSingle_page` | Section.php:490 (`update_page`) | `PagesModel::pageById(int $id): ?array` | ported | |
| models/home_model.php:449 `update_page` | Section.php:509 (`save_page`) | `PagesModel::updatePage(int $id, array $data): int` | ported | Returns affected rows. Fields filtered by `$allowedFields`. |
| models/home_model.php:456 `change_status_page` | Section.php:516 (`change_status_page`) | `PagesModel::setStatus(int $id, int $status)` (overrides `BaseModel::setStatus`) | ported-with-change | `pages.status` is `ENUM('0','1')`; CI3 wrote the URL segment as a quoted string. The override writes `'1'`/`'0'` as strings (an unquoted integer would be taken as the ENUM index). Any value other than 1 now stores `'0'` (CI3 passed the raw value through). Returns bool instead of affected rows; the caller ignores it. |
| models/home_model.php:464 `delete_page` | Section.php:523 (`delete_page`, linked from views/admin/manage_pages/list.php:67) | `PagesModel::deletePage(int $id): bool` | ported | |
| models/home_model.php:472 `show_footer_menu_list` | Section.php:534 | - | dropped | Table `footer_menu` does not exist in the live schema (`information_schema.TABLES` has no `%menu%` table), so every query fails. The screens are not linked from `views/admin/components/navigation.php` (grep `footer_menu` in `views/` finds only `views/admin/footer_menu/*`). No model file was generated for the table. |
| models/home_model.php:478 `getSingle_footer_menu` | Section.php:545 | - | dropped | Same as above (table absent). |
| models/home_model.php:484 `update_footer_menu` | Section.php:562 | - | dropped | Same as above (table absent). |
| models/home_model.php:491 `checkDuplicate_footer_menu` | Section.php:567 | - | dropped | Same as above (table absent). Raw concatenated query, not carried over. |
| models/home_model.php:496 `add_footer_menu` | Section.php:575 | - | dropped | Same as above (table absent). |
| models/home_model.php:500 `change_status_footer_menu` | Section.php:587 | - | dropped | Same as above (table absent). |
| models/home_model.php:508 `delete_footer_menu` | Section.php:597 | - | dropped | Same as above (table absent). |
| models/home_model.php:1816 `change_status_corporate_team` | Section.php:1854 | `CorporateTeamsModel::setStatus(int $id, int $status)` (inherited from `BaseModel`) | ported-with-change | Values other than 1 are stored as 0 (CI3 stored the raw URL value; the admin list only links 0 and 1). Returns bool instead of affected rows; the caller ignores it. |
| models/home_model.php:1824 `update_order_no_corporate_team` | Section.php:1861 (AJAX from views/admin/manage_corporate_team/list.php:87) | `CorporateTeamsModel::updateOrderNo(int $id, int $orderNo): int` | ported | Returns affected rows. Caller casts both POST values to int. |
| models/ui/Ui_model.php:9 `page_info` | Home.php:22 | `PagesModel::pageById(PagesModel::PAGE_HOME)` (id 1) | ported | All Ui_model page methods used `row()` (object); the new method returns an array or null. |
| models/ui/Ui_model.php:15 `finding_a_home` | Home.php:29 | `pageById(PagesModel::PAGE_FINDING_A_HOME)` (id 24) | ported | |
| models/ui/Ui_model.php:33 `selling_a_home` | Home.php:36 | `pageById(PagesModel::PAGE_VIDEOS)` (id 2) | ported | Uses the "Videos" row, as in CI3. |
| models/ui/Ui_model.php:39 `testimonials` | Home.php:43 | `pageById(PagesModel::PAGE_VIDEOS)` (id 2) | ported | Uses the "Videos" row, as in CI3. |
| models/ui/Ui_model.php:45 `our_franchisee` | Home.php:64, Home.php:70 | `pageById(PagesModel::PAGE_OUR_FRANCHISEE)` (id 3) | ported | |
| models/ui/Ui_model.php:51 `our_franchisee_details` | Home.php:79 | `pageById(PagesModel::PAGE_OUR_FRANCHISEE_DETAILS)` (id 4) | ported | |
| models/ui/Ui_model.php:57 `learning_development` | Home.php:91 | `pageById(PagesModel::PAGE_LEARNING_DEVELOPMENT)` (id 5) | ported | |
| models/ui/Ui_model.php:63 `media_center` | Home.php:98 | `pageById(PagesModel::PAGE_MEDIA_CENTER)` (id 6) | ported | |
| models/ui/Ui_model.php:69 `industry_news` | Home.php:106 | `pageById(PagesModel::PAGE_MEDIA_CENTER)` (id 6) | ported | Same row as media_center, as in CI3. |
| models/ui/Ui_model.php:75 `knowledge_series` | Home.php:113 | `pageById(PagesModel::PAGE_MEDIA_CENTER)` (id 6) | ported | Same row as media_center, as in CI3. |
| models/ui/Ui_model.php:81 `newsletter` | Home.php:120 | `pageById(PagesModel::PAGE_MEDIA_CENTER)` (id 6) | ported | Same row as media_center, as in CI3. |
| models/ui/Ui_model.php:93 `corporate_team` | Home.php:134 | `pageById(PagesModel::PAGE_CORPORATE_TEAM)` (id 7) | ported | |
| models/ui/Ui_model.php:105 `career` | Home.php:148, Home.php:583 | `pageById(PagesModel::PAGE_CAREER)` (id 9) | ported | |
| models/ui/Ui_model.php:111 `area_insights` | Home.php:155 | `pageById(PagesModel::PAGE_RESEARCH)` (id 8) | ported | Uses the "Research" row, as in CI3. |
| models/ui/Ui_model.php:117 `coldwell_banker_usa` | Home.php:167 | `pageById(PagesModel::PAGE_ABOUT_US)` (id 10) | ported | |
| models/ui/Ui_model.php:129 `nri` | Home.php:175, Home.php:772, Home.php:778, Home.php:784 | `pageById(PagesModel::PAGE_NRI)` (id 11) | ported | |
| models/ui/Ui_model.php:141 `contact_us` | Home.php:242 | `pageById(PagesModel::PAGE_CONTACT_US)` (id 12) | ported | |
| models/ui/Ui_model.php:279 `get_team_information` | Home.php:533 (`get_team_information`, AJAX) | `CorporateTeamsModel::memberById(int $id): ?array` | ported | Same query as `show_corporate_team_by_id`. |
| models/ui/Ui_model.php:487 `privacy_policy` | Home.php:945, Home.php:957 | `pageById(PagesModel::PAGE_PRIVACY_POLICY)` (id 19) | ported | |
| models/ui/Ui_model.php:492 `terms_and_conditions` | Home.php:951, Home.php:963 | `pageById(PagesModel::PAGE_TERMS_AND_CONDITIONS)` (id 20) | ported | |
| models/ui/Ui_model.php:497 `disclaimer` | Home.php:969 | `pageById(PagesModel::PAGE_DISCLAIMER)` (id 21) | ported | |
| models/ui/Ui_model.php:502 `faq` | Home.php:976 | `pageById(PagesModel::PAGE_FAQ)` (id 22) | ported | |
| models/ui/Ui_model.php:507 `refund` | Home.php:983 | `pageById(PagesModel::PAGE_REFUND)` (id 23) | ported | |
| models/ui/Ui_model.php:512 `quick_pay` | Home.php:989 | `pageById(PagesModel::PAGE_FINDING_A_HOME)` (id 24) | ported | CI3 reads id 24 ("Finding a home") here; kept as is. |
| models/login_model.php:6 `login` | Login.php:31 | `UserModel::findByCredentials(string $username, string $password): ?array` | ported | Same query (`username` and md5 of the password, now via Query Builder). Returns the user row or null; the session part moves to the controller (see below). |
| models/login_model.php:21 `logout` | Login.php:45 | - | moved-to-controller-step | Session only (`sess_destroy()`), no query. |
| views/corporate_team.php:47 (inline query) | views/corporate_team.php (rendered by Home.php:136) | `CorporateTeamsModel::activeOrdered()` | ported | `status = 1 ORDER BY order_no ASC`. The view splits the result into groups of 4 (`array_chunk`), which stays in the view. |

Page id constants on `PagesModel`: `PAGE_HOME` 1, `PAGE_VIDEOS` 2, `PAGE_OUR_FRANCHISEE` 3,
`PAGE_OUR_FRANCHISEE_DETAILS` 4, `PAGE_LEARNING_DEVELOPMENT` 5, `PAGE_MEDIA_CENTER` 6, `PAGE_CORPORATE_TEAM` 7,
`PAGE_RESEARCH` 8, `PAGE_CAREER` 9, `PAGE_ABOUT_US` 10, `PAGE_NRI` 11, `PAGE_CONTACT_US` 12,
`PAGE_PRIVACY_POLICY` 19, `PAGE_TERMS_AND_CONDITIONS` 20, `PAGE_DISCLAIMER` 21, `PAGE_FAQ` 22, `PAGE_REFUND` 23,
`PAGE_FINDING_A_HOME` 24.

Not in this work-list: Ui_model `cb_india_videos`, `featured_videos` (videos work-list, page id 2) and
`awards` (id 6), `research` (id 8) (media work-list) also read `pages` by fixed id; `PagesModel::pageById()` with
the constants above returns the same row for them.

## Upload rules

### Corporate team image (CI3 `home_model::add_corporate_team` / `update_corporate_team`, controller Section.php:420)

- Form field: `image` (`views/admin/manage_corporate_team/update.php:62`, `<input type="file" name="image">`, no `accept`).
- Only processed when a file name was submitted (`$_FILES['image']['name'] != ''`); otherwise `image` is not set
  (add: stored as `''`; update: column left unchanged).
- Target directory: `uploads/corporate_team/` (CI3: `$_SERVER['DOCUMENT_ROOT'] . FOLDER_NAME . '/uploads/corporate_team/'`).
- Allowed types / MIME checks: none in CI3 (any extension accepted).
- Size limit: none in CI3 (only PHP's `upload_max_filesize` / `post_max_size`).
- File naming: `rand() . time() . '.' . <original extension>` (extension from `pathinfo($name, PATHINFO_EXTENSION)`).
- Move: `move_uploaded_file()`; the result is not checked.
- Old file on update: NOT unlinked (the previous image stays on disk).
- The stored file name is then saved in `corporate_teams.image` via `addMember()` / `updateMember()`.

### Corporate team delete (CI3 `home_model::delete_corporate_team`, controller Section.php:463)

- Intended: unlink `uploads/corporate_team/<image>` of the deleted row. In CI3 this used an undefined variable, so
  the file was never removed. `deleteMember()` returns the deleted row; the controller removes
  `uploads/corporate_team/<row['image']>` only when `image` is non-empty and the file exists.
- Note: `views/about_coldwell_banker.php:76` hard-codes one corporate team image file (the file of row id 19),
  so deleting that member would also remove the image shown on the About page.

## Notes for the controller step

- Login (Login.php:31): `findByCredentials($username, $password)`; when a row is returned and `id > 0`, set
  session `username` to the row's `username` and redirect to `admin/dashboard/index`; otherwise set flashdata
  `error` (" Incorrect Username Or Password") and redirect to `admin/login/`. Login.php:23 redirects already
  logged-in users (session `username` not empty).
- Logout (Login.php:45): `login_model::logout()` only destroyed the session, then redirect to `admin/login/index`.
- Ui_model page methods returned `row()` objects; views that read `$page_info->title`, `->meta_description`,
  etc. must read array keys (`$page_info['title']`) with `pageById()`.
- `Home::get_team_information` (Home.php:531): reads `$_POST['user_id']` (trim), cast to int; echoes an HTML
  fragment built from `image`, `name`, `title`, `description`, then `exit`. Rendering belongs in a view.
- `Section::update_order_no_corporate_team` (Section.php:1858): reads POST `order_no`, `member_id`; echoes
  `success` and exits whenever the return value `!= ""` (in PHP 8 that is always true for an int, including 0).
- `Section::delete_corporate_team($project_id, $image_id)` (Section.php:463): the first URL segment is unused; the
  member id is the second. Sets flashdata `success_msg` and redirects to `admin/section/manage_corporate_teams/`.
- `Section::update_corporate_team` (Section.php:420): validation rules `title`, `name`, `description`
  required|trim; data array `title`, `name`, `description` (+ `image` from the upload); redirect to
  `admin/section/manage_corporate_teams/`.
- `Section::save_page` (Section.php:496): always writes `content => ''` together with `title`,
  `meta_description`, `meta_keywords`, then redirects to `admin/section/list_pages/`.
- `Section::change_status_page`, `change_status_corporate_team`, `delete_page`: values come from URL segments;
  cast to int before calling the model; each redirects to its list page.
- Footer menu (Section.php:531-600, `views/admin/footer_menu/*`): the `footer_menu` table is not in the live
  schema and the screens are not in the admin navigation; the model functions were dropped, so these
  controller actions and views should not be ported (to be confirmed by the lead).
