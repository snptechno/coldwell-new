# Port map: careers

Tables: `jobs`, `job_applications`.
New code: `app/Models/JobsModel.php`, `app/Models/JobApplicationsModel.php`.
Parity: `php spark parity:check careers` (tests/_support/Parity/CareersParity.php): 15 checks, all passing.

Every query uses the Query Builder. Id parameters are typed `int` in the method signatures.

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:814 `show_job_openings()` | controllers/admin/Section.php:804 `job_openings_list` | `JobsModel::listAll(): array` | ported | `SELECT * FROM jobs ORDER BY id DESC`, array of rows. |
| models/home_model.php:820 `getSingle_job($id)` | controllers/admin/Section.php:815 `update_job_opening` | `JobsModel::findJob(int $id): ?array` | ported-with-change | (a) id cast to int and bound. Returns one row or null. |
| models/home_model.php:826 `checkDuplicate_job($position_name)` | controllers/admin/Section.php:848 `save_job` | `JobsModel::findIdByPosition(string $position): ?int` | ported-with-change | Keeps `strtolower()` before `WHERE position = ?` (the column collation is case-insensitive, so matching stays case-insensitive). Returns the first matching id as int, or null. CI3 read `['id']` from a row that could be null, which raises a PHP 8 warning (c). The caller's `== 0 \|\| == ""` test still treats null as "not a duplicate". |
| models/home_model.php:833 `add_job($data)` | controllers/admin/Section.php:862 `save_job` | `JobsModel::createJob(array $data): int` | ported-with-change | Insert via the Model (`allowedFields` filtering). CI3 returned nothing. The new method returns the insert id, which the caller does not use. |
| models/home_model.php:838 `delete_job_opening($id)` | controllers/admin/Section.php:876 `delete_job_opening` | `JobsModel::deleteJob(int $id): bool` | ported-with-change | (c) CI3 returned the undefined variable `$query`. It now returns the delete result. (a) id bound. The caller ignores the return value. |
| models/home_model.php:844 `update_job($id, $data)` | controllers/admin/Section.php:842 `save_job` | `JobsModel::updateJob(int $id, array $data): int` | ported | Update via the Model (`allowedFields` filtering). Returns affected rows, as before. |
| models/home_model.php:851 `change_status_job_opening($id, $status)` | controllers/admin/Section.php:881 `change_status_job_opening` | `BaseModel::setStatus(int $id, int $status)` (inherited by `JobsModel`) | ported-with-change | Shared status toggle. Status is normalised to 0/1, which are the only values the admin list links send (views/admin/manage_job_openings/list.php:90). Returns bool instead of affected rows; the caller ignores it. |
| models/home_model.php:860 `show_job_application()` | controllers/admin/Section.php:891 `job_application_list` | `JobApplicationsModel::listWithPosition(): array` | ported | Inner join on `jobs`, selecting `job_applications.*, jobs.position, jobs.type AS position_type`, `ORDER BY job_applications.id DESC`. Applications whose job no longer exists stay excluded (inner join). |
| models/home_model.php:867 `show_job_candidate_details($id)` | controllers/admin/Section.php:900 `check_details_candidate` | `JobApplicationsModel::detailsWithPosition(int $id): ?array` | ported-with-change | (a) id was concatenated into raw SQL. It is now cast to int and bound. Same columns (`position_name` alias), one row or null. |
| models/home_model.php:873 `get_candidate_resume($id)` | controllers/admin/Section.php:906 `get_candidate_resume` | `JobApplicationsModel::findApplication(int $id): ?array` | ported | `WHERE id = ? ORDER BY id DESC`, one row or null. |
| models/ui/Ui_model.php:204 `current_openings_list()` | none | - | dropped | (d) No caller: `grep -rn "current_openings_list" application/` matches only the definition (Ui_model.php:204). |
| models/ui/Ui_model.php:209 `current_openings_description($id)` | controllers/Home.php:510 `apply_job_opening` | `JobsModel::findJob(int $id): ?array` | ported-with-change | Same query as `getSingle_job`. (a) id bound. |
| models/ui/Ui_model.php:227 `save_job_application($data, $files)` | controllers/Home.php:611 `job_applications` | `JobApplicationsModel::createApplication(array $data): int` + `JobApplicationsModel::setResume(int $id, string $fileName): bool` | moved-to-upload-step | The DB part is ported as two methods: insert the row, then store the resume file name. File validation and the move go to the controller with CI4 UploadedFile (see "Upload rules"). |
| models/ui/Ui_model.php:253 `get_candidate_resume($id)` | controllers/Home.php:615 `job_applications` | `JobApplicationsModel::findWithJob(int $id): ?array` | ported-with-change | (a) id was concatenated into raw SQL. It is now cast to int and bound. Keeps `job_applications.*, jobs.*`, so the duplicated names `id` and `created_date` carry the `jobs` values, as in CI3. The caller only reads `resume`. |
| models/ui/Ui_model.php:297 `get_position_name($id)` | controllers/Home.php:614 `job_applications` | `JobsModel::findJob(int $id): ?array` | ported-with-change | Same query as `getSingle_job`. (a) id bound. The caller reads `position` and `type`. |
| views/career-old.php:56 (inline SQL) | no controller loads `career-old` (in-view query, ported per the rules) | `JobsModel::activeByType(JobsModel::TYPE_COLDWELL)` | ported | `type = 'COLDWELL BANKER INDIA OPENINGS' AND status = '1' ORDER BY id DESC`. The view itself may be dropped in the view step. |
| views/career.php:56 (inline SQL) | controllers/Home.php:150 `career` | `JobsModel::activeByType(JobsModel::TYPE_COLDWELL)` | ported | As above. |
| views/franchisees_openings.php:57 (inline SQL) | controllers/Home.php:585 `franchisees_openings` | `JobsModel::activeByType(JobsModel::TYPE_FRANCHISEE)` | ported | `type = 'FRANCHISEES OPENINGS' AND status = '1' ORDER BY id DESC`. |

The callers listed are the canonical controllers. `controllers/admin/Home.php` and `controllers/Section.php` hold identical copies of the admin job actions and are not counted.

## Upload rules

### Ui_model::save_job_application (models/ui/Ui_model.php:227): public resume upload

The CI3 flow below is to be rebuilt in the controller with CI4 `UploadedFile`:

1. The application row is inserted first (`createApplication($data)`), unconditionally.
2. Form field: `resume_file`.
3. Size: the file is accepted only if `size < 2000000` bytes (strictly less than).
4. Type check: the MIME type is detected from the temp file with `finfo` (`FILEINFO_MIME_TYPE`). The part after `/` must be one of
   `vnd.openxmlformats-officedocument.wordprocessingml.document`, `rtf`, `msword` or `pdf`
   (.docx, .rtf, .doc, .pdf). In CI4, use `$file->getMimeType()` (also based on finfo) with the same list.
5. Target directory: `uploads/resume/` under the document root (with the `FOLDER_NAME` prefix).
6. File name: `time() . rand() . '.' . <ext>`, where `<ext>` is the second dot-separated segment of the original client file name (`explode('.', $name)[1]`), which is not always the last extension. If the CI4 rebuild uses `$file->getRandomName()` or the guessed extension instead, record that as a deliberate change in that step.
7. On a successful move: `setResume($insertId, $newFileName)`, then return the insert id.
8. On failure (size or type rejected, or the move failed), nothing is unlinked or rolled back. The application row stays with an empty `resume`, and the function returns null, which the controller treats as a failure.
9. No old file is unlinked, because only new rows are written.

### Admin resume download (controllers/admin/Section.php:905)

This is not a model concern; it is listed here for the controller step. The controller reads `uploads/resume/<resume>` and sends it with `force_download('Resume.' . explode('.', resume)[1])`. CI3 does not check for an empty `resume` or a missing row.

## Notes for the controller step

- `admin/Section::save_job` (Section.php:822) reads POST and calls `findIdByPosition()` before an insert. On a duplicate it sets the flash class `alert-danger` and redirects to `admin/section/update_job_opening/`. On success it sets `alert-success` and redirects to `admin/section/job_openings_list/`. The update branch sets a `$data['message']` that is never shown, then redirects. On insert, the controller sets `created_date` (`date('Y-m-d H:i:s')`).
- `admin/Section::delete_job_opening` and `change_status_job_opening` redirect to `admin/section/job_openings_list/` after the write. Cast both URL segments to int before calling `deleteJob()` or `setStatus()`.
- views/admin/manage_job_applications/list.php:66-74 holds a copied delete script that points to `admin/section/delete_job_opening/`. No element in that view has the `remove_levels` class, so it never runs. There is no application delete in CI3. Drop the script in the view step.
- `admin/Section::get_candidate_resume` (Section.php:905) does a file download followed by `exit` (see "Upload rules").
- `Home::apply_job_opening` (Home.php:507) checks `isset($id)` and otherwise redirects to `current-openings`.
- `Home::job_applications` (Home.php:587) sanitises POST with `FILTER_SANITIZE_STRING`, which is deprecated since PHP 8.1; rebuild it with CI4 validation and output escaping. It then runs the upload (see "Upload rules"), calls `findJob($job_id)` and `findWithJob($insertId)` to build a notification e-mail with the resume attached, and echoes a result string for an AJAX caller. The mail transport settings are hard-coded in CI3 and must move to configuration (`.env`), not into code.
- `Home::career` and `Home::franchisees_openings`: the controller should pass `activeByType(...)` results to the views instead of the views querying the database.
