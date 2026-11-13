# Port map: projects

Step 4 (models, business logic, database queries). Domain tables: `projects`, `projects_banners`,
`projects_brochures`, `projects_description`, `projects_floor_plan`, `projects_gallery`,
`projects_highlights`, `project_amenities`, `amenities`, `project_information`, `home_page_banners`.

Canonical callers searched: `controllers/Home.php`, `controllers/admin/Section.php`,
`controllers/admin/Dashboard.php`, `controllers/admin/Login.php` and `views/**`
(`controllers/admin/Home.php` and `controllers/Section.php` are stale duplicates and are not counted).
Evidence for every "dropped" row comes from `grep -rn "<function name>" application/` in the CI3 tree.

Parity: `php spark parity:check projects` (tests/_support/Parity/ProjectsParity.php) - 7 checks, all pass.

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:587 `show_projects_gallery_list($project_id)` | admin/Section.php:735 `list_project_gallery` | `ProjectsGalleryModel::listByProject(int $projectId): array` | ported | Same filter, `ORDER BY id DESC`, array of rows. Bound value, id cast to int (a). |
| models/home_model.php:594 `checkDuplicate_project_gallery_image($image_name, $project_id)` | none | - | dropped | No call anywhere in application/ (only the definition matches). |
| models/home_model.php:602 `add_project_gallery_image($data, $files)` | admin/Section.php:781 `save_project_gallery_image` | `ProjectsGalleryModel::addImage(array $data): int` + `setImageFile(int $id, string $image): bool` | moved-to-upload-step | DB part ported: insert row, return insert id; after the upload the file name is stored with `setImageFile()`. File handling: see Upload rules. The CI3 second UPDATE also wrote a `thumbnail_image` column that does not exist in `projects_gallery`, so that UPDATE failed; `setImageFile()` writes only `image` (c). Fields filtered by `$allowedFields`. |
| models/home_model.php:650 `getSingle_project_gallery_image($project_id, $id)` | admin/Section.php:754 `update_project_gallery_image` | `ProjectsGalleryModel::findForProject(int $projectId, int $id): ?array` | ported | One row or null (`first()`). |
| models/home_model.php:657 `update_project_gallery_image($id, $data, $files)` | admin/Section.php:772 `save_project_gallery_image` | `ProjectsGalleryModel::updateImage(int $id, array $data): int` + `setImageFile()` | moved-to-upload-step | DB part ported: `UPDATE ... WHERE id`, returns affected rows. Data keys limited to `$allowedFields`. File handling: see Upload rules; same `thumbnail_image` note as add (c). |
| models/home_model.php:706 `delete_project_gallery_image($image_id)` | admin/Section.php:789 `delete_project_gallery_image` | `ProjectsGalleryModel::deleteImage(int $id): ?array` | moved-to-upload-step | Reads the row, deletes it, returns the old row (null if missing) so the controller can remove the stored file. Unlink: see Upload rules. |
| models/home_model.php:716 `change_status_project_gallery($id, $status)` | admin/Section.php:794 `change_status_project_gallery` | `ProjectsGalleryModel::setStatus(int $id, int $status)` (inherited from BaseModel) | ported-with-change | Shared status toggle. CI3 stored the raw URL value; the shared method stores 1 for 1 and 0 for anything else. The only links in views pass 0 or 1 (views/admin/manage_gallery/list.php:86), so normal use is unchanged. |
| models/home_model.php:724 `show_projects_banners_list($project_id)` | none | - | dropped | No call in any controller or view. |
| models/home_model.php:731 `checkDuplicate_project_banner_name(...)` | none | - | dropped | No call anywhere. |
| models/home_model.php:739 `add_project_banner($data, $files)` | none | - | dropped | No call anywhere (its upload code goes with it). |
| models/home_model.php:765 `update_project_banner($id, $data, $files)` | none | - | dropped | No call anywhere (its upload code goes with it). |
| models/home_model.php:791 `getSingle_project_banner($project_id, $id)` | none | - | dropped | No call anywhere. |
| models/home_model.php:798 `delete_project_banner($image_id)` | none | - | dropped | No model call anywhere. Several admin views link to `admin/section/delete_project_banner/...`, but `admin/Section.php` has no such method, so the model function is unreachable. |
| models/home_model.php:808 `show_home_banners_list()` | none | - | dropped | No call anywhere. |
| models/home_model.php:880 `getSingle_projects_information($project_id)` | admin/Section.php:925 `update_project_information` | `ProjectInformationModel::findByProject(int $projectId): ?array` | ported | One row or null (`first()`, no ORDER BY, as in CI3). |
| models/home_model.php:886 `add_project_information($data)` | admin/Section.php:969 `save_project_information` | `ProjectInformationModel::addInformation(array $data): int` | ported | Insert. CI3 returned nothing; the new method returns the insert id (the caller ignored the value). |
| models/home_model.php:891 `update_project_information($id, $data)` | admin/Section.php:953 `save_project_information` | `ProjectInformationModel::updateInformation(int $id, array $data): int` | ported | `UPDATE ... WHERE id`, returns affected rows. Keys limited to `$allowedFields`. |
| models/ui/Ui_model.php:159 `project_info($id)` | none | - | dropped | No `->project_info(` call in any controller or view (`$project_info` matches are view variables filled by `home_model::getSingle_project`). |
| models/ui/Ui_model.php:165 `is_luxurious($id)` | none | - | dropped | No `->is_luxurious(` call anywhere; admin uses `home_model::getSingle_project_luxurious` (other domain). |
| models/ui/Ui_model.php:272 `download_broucher($data)` | none | - | dropped | No call anywhere (`grep -rni broucher` matches only the definition). |
| models/Amenities_model.php:9 `show_list()` | controllers/admin/Amenities.php only | - | dropped | `admin/Amenities.php` is not a canonical controller and is unreachable: the file declares `class Home`, so CI3 cannot route `admin/amenities`, and its save/delete actions call an unloaded `banner_model`. No other caller of `amenities_model`. |
| models/Amenities_model.php:15 `checkDuplicate($client_name)` | controllers/admin/Amenities.php only | - | dropped | Same as above. |
| models/Amenities_model.php:22 `add_project($data, $files)` | controllers/admin/Amenities.php only | - | dropped | Same as above (its upload code goes with it). |
| models/Amenities_model.php:64 `getSingle($id)` | controllers/admin/Amenities.php only | - | dropped | Same as above. |
| models/Amenities_model.php:70 `updated($id, $data, $files)` | none (Amenities.php calls `banner_model->updated`) | - | dropped | No caller of this model method; also writes to a `banners` table. |
| models/Amenities_model.php:116 `getSingleImage($id)` | none | - | dropped | Only `banner_model->getSingleImage` in Amenities.php; no caller of this model method. |
| models/Amenities_model.php:122 `delete($id)` | none | - | dropped | Only `banner_model->delete` in Amenities.php; no caller of this model method. |
| models/Amenities_model.php:137 `image_update($id, $files)` | none | - | dropped | Only `banner_model->image_update` in Amenities.php; no caller of this model method. |
| models/Amenities_model.php:181 `deleteSingleImage($id)` | none | - | dropped | Only `banner_model->deleteSingleImage` in Amenities.php; no caller of this model method. |
| views/home1.php:291 inline `SELECT name FROM projects where status='1' ORDER BY name ASC` | views/home1.php (contact enquiry form) | `ProjectsModel::activeProjectNames(): array` | ported | In-view query, ported for the view step. Same column, filter and order. No controller currently loads `home1` (grep for `home1` in controllers/config is empty). |

## Upload rules

Only the gallery functions survive; the banner and amenities upload code is dropped with its functions.

### Gallery image add / update (home_model.php:602 and :657, controller `save_project_gallery_image`)

- Fields: `image` (large image) and `thumb_image` (thumbnail), both single files.
- Type check: the MIME type is read with `finfo` (FILEINFO_MIME_TYPE). The CI3 condition
  `(mime == png && mime == jpeg && mime == jpg) || size < 2000000` can never satisfy its first half, so the
  effective rule was size only. Intended rule for the rebuild: image/png, image/jpeg, image/jpg; each file
  under 2,000,000 bytes.
- Extension: taken from the MIME subtype (`image/jpeg` -> `jpeg`, `image/png` -> `png`).
- File name: `time() . rand() . '.' . <ext>` (new random name per file).
- Target: large image to `uploads/gallery/`, thumbnail to `uploads/gallery/thumb/`.
- Order: the large image is moved first; the thumbnail only if the large image was moved; the DB is updated
  only if both moves succeed. Then `setImageFile($id, <large image name>)`. The thumbnail file name is not
  stored (no column for it).
- Add: the row is inserted first (`addImage`), then the files are processed; on failure the row stays with an
  empty `image`.
- Update: the row is updated first (`updateImage`), then files are processed when `$_FILES` is non-empty. In
  CI3 `$_FILES` is always non-empty for this form, so an update without new files called `finfo_file('')`,
  which throws on PHP 8. Rebuild: only process when a valid file was uploaded (`UploadedFile::isValid()`).
- No old file is unlinked on update (old images stay on disk).

### Gallery image delete (home_model.php:706, controller `delete_project_gallery_image`)

- After `deleteImage($id)` returns the old row, unlink `uploads/gallery/<row.image>` (CI3 built
  `DOCUMENT_ROOT . FOLDER_NAME . "/uploads/gallery/"`). Skip when the row is null or `image` is empty.
- The thumbnail in `uploads/gallery/thumb/` is not removed in CI3.

## Notes for the controller step

- `save_project_gallery_image`: the CI3 model echoed `some error occured` on an upload failure and the
  controller redirected anyway; replace with a flash message. Redirect to
  `admin/section/list_project_gallery/<project_id>`; the update branch sets flash `project_success_msg`.
- `delete_project_gallery_image($project_id, $image_id)` and
  `change_status_project_gallery($project_id, $id, $status)`: cast URL segments to int; redirect to the
  gallery list.
- `update_project_information($id)`: the route segment `$id` is the project id (used for every lookup,
  including `findByProject`); the second URL segment is not read by the method.
- `save_project_information($project_id, $id)`: on add, CI3 redirected to
  `.../update_project_information/<project_id>/` with an empty id; flash keys `class` = `alert-success`,
  `message` = `Added sccessfully`.
- `views/home1.php` contact form: call `ProjectsModel::activeProjectNames()` from the controller and pass the
  list to the view instead of querying in the view.
