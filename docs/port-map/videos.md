# Port map: videos

Step 4 (models, business logic, database queries). Domain tables: `finding_home_videos`,
`selling_home_videos`, `testimonial_videos`, `cb_india_videos`, `featured_videos`,
`success_story`, `careers_testimonials` (plus the `pages` meta row read by two video pages).

Owned models: `FindingHomeVideosModel`, `SellingHomeVideosModel`, `TestimonialVideosModel`,
`CbIndiaVideosModel`, `FeaturedVideosModel`, `SuccessStoryModel`, `CareersTestimonialsModel`
(all in `app/Models/`). Parity suite: `tests/_support/Parity/VideosParity.php`
(`php spark parity:check videos`: 28 checks, all passing).

Old paths are relative to the CI3 `application/` folder. "Section" means
`controllers/admin/Section.php`, "Home" means `controllers/Home.php`.

All five plain video tables share the same column set and the same admin flow, so their
models expose the same method names: `allLatestFirst()`, `byId(int)`, `addVideo(array)`,
`updateVideo(int, array)`, `deleteVideo(int)`, `changeStatus(int, int)`.

## Mapping

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:898 `show_finding_home_videos()` | Section:473 | `FindingHomeVideosModel::allLatestFirst()` | ported | `ORDER BY id DESC`, array of arrays. |
| models/home_model.php:904 `show_finding_home_video_by_id($id)` | Section:1006 | `FindingHomeVideosModel::byId(int $id)` | ported | row array or null. |
| models/home_model.php:910 `add_finding_home_video($data)` | Section:1027 | `FindingHomeVideosModel::addVideo(array $data): bool` | ported | Returns the insert boolean like CI3. |
| models/home_model.php:914 `update_finding_home_video($id, $data)` | Section:1025 | `FindingHomeVideosModel::updateVideo(int $id, array $data): int` | ported | Returns affected rows. |
| models/home_model.php:921 `delete_home_videos($id)` | Section:1035 | `FindingHomeVideosModel::deleteVideo(int $id): bool` | ported-with-change | (c) CI3 returned an undefined variable (`$query`); now returns the delete result. Caller ignores the value. |
| models/home_model.php:927 `show_selling_home_videos()` | Section:1042 | `SellingHomeVideosModel::allLatestFirst()` | ported | |
| models/home_model.php:933 `show_selling_home_video_by_id($id)` | Section:1060 | `SellingHomeVideosModel::byId(int $id)` | ported | |
| models/home_model.php:939 `update_selling_home_video($id, $data)` | Section:1079 | `SellingHomeVideosModel::updateVideo(int $id, array $data): int` | ported | |
| models/home_model.php:946 `add_selling_home_video($data)` | Section:1081 | `SellingHomeVideosModel::addVideo(array $data): bool` | ported | |
| models/home_model.php:950 `delete_selling_videos($id)` | Section:1088 | `SellingHomeVideosModel::deleteVideo(int $id): bool` | ported-with-change | (c) undefined `$query` return fixed. |
| models/home_model.php:956 `change_status_selling_videos($id, $status)` | Section:1093 | `SellingHomeVideosModel::changeStatus(int $id, int $status): int` | ported | Via `BaseModel::setStatus(..., 'is_active')`; returns affected rows. Admin links only send 0/1. |
| models/home_model.php:964 `change_status_finding_home_videos($id, $status)` | Section:1100 | `FindingHomeVideosModel::changeStatus(int $id, int $status): int` | ported | As above. |
| models/home_model.php:972 `show_testimonial_videos()` | Section:1109 | `TestimonialVideosModel::allLatestFirst()` | ported | |
| models/home_model.php:978 `show_testimonial_video_by_id($id)` | Section:1127 | `TestimonialVideosModel::byId(int $id)` | ported | |
| models/home_model.php:984 `update_testimonial_video($id, $data)` | Section:1146 | `TestimonialVideosModel::updateVideo(int $id, array $data): int` | ported | |
| models/home_model.php:991 `add_testimonial_video($data)` | Section:1148 | `TestimonialVideosModel::addVideo(array $data): bool` | ported | |
| models/home_model.php:995 `change_status_testimonial_videos($id, $status)` | Section:1156 | `TestimonialVideosModel::changeStatus(int $id, int $status): int` | ported | |
| models/home_model.php:1003 `delete_testimonial_videos($id)` | Section:1163 | `TestimonialVideosModel::deleteVideo(int $id): bool` | ported-with-change | (c) undefined `$query` return fixed. |
| models/home_model.php:1009 `show_cb_india_videos()` | Section:1170 | `CbIndiaVideosModel::allLatestFirst()` | ported | |
| models/home_model.php:1015 `show_cb_india_video_by_id($id)` | Section:1188 | `CbIndiaVideosModel::byId(int $id)` | ported | |
| models/home_model.php:1021 `update_cb_india_video($id, $data)` | Section:1207 | `CbIndiaVideosModel::updateVideo(int $id, array $data): int` | ported | |
| models/home_model.php:1028 `add_cb_india_video($data)` | Section:1209 | `CbIndiaVideosModel::addVideo(array $data): bool` | ported | |
| models/home_model.php:1032 `change_status_cb_india_videos($id, $status)` | Section:1217 | `CbIndiaVideosModel::changeStatus(int $id, int $status): int` | ported | |
| models/home_model.php:1040 `delete_cb_india_videos($id)` | Section:1224 | `CbIndiaVideosModel::deleteVideo(int $id): bool` | ported-with-change | (c) undefined `$query` return fixed. |
| models/home_model.php:1046 `show_featured_videos()` | Section:1231 | `FeaturedVideosModel::allLatestFirst()` | ported | |
| models/home_model.php:1052 `show_featured_video_by_id($id)` | Section:1249 | `FeaturedVideosModel::byId(int $id)` | ported | |
| models/home_model.php:1058 `update_featured_video($id, $data)` | Section:1268 | `FeaturedVideosModel::updateVideo(int $id, array $data): int` | ported | |
| models/home_model.php:1065 `add_featured_video($data)` | Section:1270 | `FeaturedVideosModel::addVideo(array $data): bool` | ported | |
| models/home_model.php:1069 `change_status_featured_videos($id, $status)` | Section:1278 | `FeaturedVideosModel::changeStatus(int $id, int $status): int` | ported | |
| models/home_model.php:1077 `delete_featured_videos($id)` | Section:1285 | `FeaturedVideosModel::deleteVideo(int $id): bool` | ported-with-change | (c) undefined `$query` return fixed. |
| models/home_model.php:1226 `show_careers_testimonials()` | Section:1364 | `CareersTestimonialsModel::allLatestFirst()` | ported | |
| models/home_model.php:1232 `show_careers_testimonials_by_id($id)` | Section:1380 | `CareersTestimonialsModel::byId(int $id)` | ported | |
| models/home_model.php:1238 `update_carrer_testimonial($id, $data, $files)` | Section:1400 | `CareersTestimonialsModel::updateTestimonial(int $id, array $data): int` | ported-with-change | Rule 5: `$_FILES` handling removed from the model. Controller stores the image and adds `image_name` to `$data`. See Upload rules. |
| models/home_model.php:1261 `add_carrer_testimonial($data, $files)` | Section:1402 | `CareersTestimonialsModel::addTestimonial(array $data): int` (+ `updateTestimonial($id, ['image_name' => ...])`) | ported-with-change | Rule 5: insert returns the new id like CI3 `insert_id()`; the image move and the follow-up `image_name` update are moved to the controller. The `echo` on a rejected file is moved to the controller step. |
| models/home_model.php:1286 `change_status_carrers_testimonial($id, $status)` | Section:1413 | `CareersTestimonialsModel::changeStatus(int $id, int $status): int` | ported | Column `status` (not `is_active`). |
| models/home_model.php:1294 `delete_carrer_testimonial($id)` | Section:1420 | `CareersTestimonialsModel::deleteTestimonial(int $id): ?array` | ported-with-change | Rule 5: returns the deleted row (as CI3 did) so the controller can remove the image file; `unlink()` calls moved out of the model. |
| models/home_model.php:2043 `success_stories_videos()` | Section:2032 | `SuccessStoryModel::allLatestFirst()` | ported | |
| models/home_model.php:2050 `show_success_story_video_by_id($id)` | Section:2049 | `SuccessStoryModel::byId(int $id)` | ported | |
| models/home_model.php:2056 `update_success_story_video($id, $data)` | Section:2069 | `SuccessStoryModel::updateVideo(int $id, array $data): int` | ported | |
| models/home_model.php:2063 `add_success_story_video($data)` | Section:2071 | `SuccessStoryModel::addVideo(array $data): bool` | ported | |
| models/home_model.php:2067 `delete_success_story_videos($id)` | Section:2078 | `SuccessStoryModel::deleteVideo(int $id): bool` | ported-with-change | (c) undefined `$query` return fixed. |
| models/home_model.php:2073 `change_status_success_story($id, $status)` | Section:2083 | `SuccessStoryModel::changeStatus(int $id, int $status): int` | ported | See controller note about the list view links. |
| models/ui/Ui_model.php:21 `cb_india_videos()` | Home:50 | `CbIndiaVideosModel::pageInfo(): ?array` | ported | `pages` row id 2. CI3 `row()` object -> array or null (rule 4); the view reads `$page_info->title` etc. and must switch to array access. Same row as the cms domain's `pages` id 2 lookups. |
| models/ui/Ui_model.php:27 `featured_videos()` | Home:57 | `FeaturedVideosModel::pageInfo(): ?array` | ported | As above. |
| views/career-old.php:107 (careers_testimonials, status=1) | - | - | dropped | (d) The query is commented out in the view (`// $q1= "select * from careers_testimonials where status='1' order by id desc";`, line 106) together with its loop; it never runs. `career-old.php` is also not loaded by any controller. |
| views/career-old.php:130 (success_story) | view not loaded by any controller (grep `career-old` in controllers: no hit) | `SuccessStoryModel::activeLatestFirst()` | ported | Same query as career.php:130; in-view queries are always ported. |
| views/career.php:107 (careers_testimonials, status=1) | - | - | dropped | (d) Commented out in the view (line 106, `// $q1= ...` and the whole loop); it never runs. |
| views/career.php:130 (success_story) | Home:150 `career()` | `SuccessStoryModel::activeLatestFirst()` | ported | `is_active=1 ORDER BY id DESC`, all columns. |
| views/cb_india_videos.php:60 | Home:52 | `CbIndiaVideosModel::activeVideos()` | ported | `id, title, description, video_url`, `is_active=1`, no ORDER BY (kept). |
| views/featured_videos.php:60 | Home:59 | `FeaturedVideosModel::activeVideos()` | ported | As above. |
| views/finding_a_home.php:59 | Home:31 | `FindingHomeVideosModel::activeVideos()` | ported | As above. |
| views/franchisees_openings.php:103 (careers_testimonials) | Home:585 | `CareersTestimonialsModel::allTestimonials()` | ported | All rows, no status filter, no ORDER BY (kept as in CI3). |
| views/franchisees_openings.php:124 (success_story) | Home:585 | `SuccessStoryModel::activeLatestFirst()` | ported | |
| views/selling_a_home.php:61 | Home:38 | `SellingHomeVideosModel::activeVideos()` | ported | No ORDER BY (kept). |
| views/testimonials.php:60 | Home:45 | `TestimonialVideosModel::activeVideosLatestFirst()` | ported | `ORDER BY id DESC`. |
| views/videos.php:65 | view not loaded by any controller (grep `'videos'` view loads in controllers: no hit) | `FindingHomeVideosModel::activeVideos()` | ported | Same query as finding_a_home.php:59. |

All new queries use the Query Builder with bound values; ids are typed `int`.

Inserts: the CI3 controllers do not pass `is_active` (video tables, `NOT NULL`, no default)
or `image_name`/`status` (careers_testimonials). Both the CI3 config (`stricton => FALSE`) and
the CI4 config (`strictOn => false`) run without strict SQL mode, so MySQL stores the implicit
defaults (0 / empty string), as before. `success_story.is_active` has `DEFAULT 1`.

## Upload rules

Only `careers_testimonials` handles files in this domain. Target directory:
`uploads/carrers_testimonial/` (CI3 path `$_SERVER['DOCUMENT_ROOT'] . FOLDER_NAME . '/uploads/carrers_testimonial/'`;
note the spelling `carrers`). Public view `franchisees_openings.php` shows
`base_url() . 'uploads/carrers_testimonial/' . image_name`.

| CI3 function | Field | Checks in CI3 | File name | DB write | Old file removed |
|---|---|---|---|---|---|
| `add_carrer_testimonial` (home_model.php:1261) | `image` (form `admin/careers_testimonials/image_add.php`, single file) | Size `< 2000000` bytes; extension = second segment of `explode('.', name)` (index 1), must be one of `gif`, `jpeg`, `jpg`, `png` (case-sensitive). No MIME check. | `time() . rand() . '.' . ext` | Row inserted first (`name`, `position`, `title`); after a successful move, `image_name` is updated on the new id. If the check fails, CI3 echoes `some error occured` and keeps the row without an image. | none |
| `update_carrer_testimonial` (home_model.php:1238) | `image` | Only runs when `$files['image']['name'] != ''`. No size, extension or MIME check in CI3. | `rand() . time() . '.' . pathinfo(name, PATHINFO_EXTENSION)` | `image_name` added to the update data. | none (the previous image stays on disk) |
| `delete_carrer_testimonial` (home_model.php:1294) | - | - | - | Row deleted after the file removal. | `unlink(dir . image_name)`; CI3 also calls `unlink(dir . pdf_name)` but the table has no `pdf_name` column, so that call only raised a notice/warning. The admin step should remove `image_name` only. |

New flow for the admin step: validate and move the `UploadedFile` in the controller, then call
`addTestimonial()` + `updateTestimonial($id, ['image_name' => $name])` (or pass `image_name` in
the insert data), `updateTestimonial($id, $data + ['image_name' => $name])`, and
`deleteTestimonial($id)` which returns the row whose `image_name` is to be removed.
Files under `uploads/` were not touched in this step.

## Notes for the controller step

- Every admin write handler in Section (`add_*`/`update_*`/`delete_*`/`change_status_*` for
  these tables) redirects back to its list page; the model return values are ignored. The
  `change_status_*` handlers also set unused `$type` / `$data['message']` variables.
- Admin add/edit forms validate `title`, `description`, `url` (videos) or `title`, `name`,
  `position_name` (careers testimonials) with `required|trim`, and map `url` -> `video_url`,
  `position_name` -> `position`.
- `success_story_videos()` add/update (Section:2037-2071, `created_date` at 2065) sets `created_date = date('Y-m-d H:i:s')`
  in the controller, on both insert and update.
- `add_selling_videos` (Section:1047-1085) also loads `get_cities_list()` (another domain) that
  the form does not appear to need.
- `add_carrer_testimonial`: the `echo "some error occured"` on a rejected image (home_model.php:1280)
  is output, not model logic; the controller step decides how to report it (e.g. flash message).
- `views/admin/success_story_videos/list.php:33` builds its Enable/Disable links to
  `admin/section/change_status_testimonial_videos/...` rather than
  `change_status_success_story/...`; the view/controller step should confirm the intended target.
- `Home::cb_india_videos()` / `Home::featured_videos()` pass `page_info` to the view, which uses
  object syntax (`$page_info->title`, `->meta_description`, `->meta_keywords`); `pageInfo()` now
  returns an array. The cms domain's `pages` lookups return the same row (id 2), so the controller
  step may use either.
- Public video views build the YouTube thumbnail URL from the last path segment of `video_url`;
  that stays in the view.
