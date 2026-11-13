# Port map: franchise domain

Tables: `franchises`, `cities`, `franchises_owners`, `franchises_testimonials`, `real_estate_specialist`.

Models: `app/Models/FranchisesModel.php`, `CitiesModel.php`, `FranchisesOwnersModel.php`,
`FranchisesTestimonialsModel.php`, `RealEstateSpecialistModel.php`.

Parity: `php spark parity:check franchise` (tests/_support/Parity/FranchiseParity.php), 42 checks, all passing.

Conventions used below:

- Paths are relative to the CI3 `application/` folder.
- "(a)" = values passed as Query Builder values / `?` bindings instead of concatenated into the SQL; ids typed `int`.
- "(c)" = PHP 8 undefined-variable path fixed so the intended behaviour happens.
- Single-row lookups by primary key use the built-in `Model::find(int)` (returns one array or null).
  CI3 `row_array()` returned `[]` when nothing matched; CI4 returns `null`. All callers test with `!empty()`, so this is transparent.
- Write methods are implemented and reviewed, not executed (no writes against the live-data copy).
- Inserts/updates go through `Model::insert()` / `Model::update()` (`protectFields` on). Every key the CI3 controllers send is in `$allowedFields`.

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:9-14 `get_cities_list` | controllers/admin/Section.php:273, 998, 1052, 1119, 1180, 1241, 1303, 1437 | `CitiesModel::listNewestFirst()` | ported | Same query as `show_cities_list`; both map to one method. |
| models/home_model.php:177-190 `show_franchises_list` | controllers/admin/Section.php:254 | `FranchisesModel::listWithCity()` | ported | Inner join kept (franchises without a city row are not listed). |
| models/home_model.php:191-224 `add_franchise` | controllers/admin/Section.php:331 | `FranchisesModel::addFranchise(array $data): int` | ported-with-change | (c) Signature was `add_franchise($data)` while the controller passed `$_FILES` as a 2nd argument, so `$files` was undefined and the logo was never stored on add. DB part ported; logo upload moved to the upload step (see Upload rules, F1), where it is applied on add as on update. `languages` / `office_specialities`: imploded with `,` when set, key dropped when not set (`isset`). Returns affected rows. |
| models/home_model.php:225-263 `update_franchise` | controllers/admin/Section.php:329 | `FranchisesModel::updateFranchise(int $id, array $data): int` | ported | DB part ported (same multi-value handling as add). File part: Upload rules F1. Returns affected rows. |
| models/home_model.php:264-273 `show_franchise_by_id` | controllers/admin/Section.php:281 | `FranchisesModel::findWithCity(int $id): ?array` | ported | |
| models/home_model.php:274-287 `show_specialist_by_id` | controllers/admin/Section.php:378 | `RealEstateSpecialistModel::find(int $id)` | ported | Built-in find. |
| models/home_model.php:288-298 `delete_franchise` | controllers/admin/Section.php:341 | `FranchisesModel::deleteFranchise(int $id): array` | ported-with-change | (c) The unlink used an undefined `$row`, a column that does not exist on `franchises` (`franchise_owner_image`) and the `uploads/learning_gallery/` folder, so no file was ever removed. DB part ported: fetches the row(s), deletes, returns the fetched rows (same return as CI3). File clean-up: Upload rules F3. Related owners/specialists/testimonials are not deleted (as before). |
| models/home_model.php:299-311 `show_specialist_list` | controllers/admin/Section.php:350 | `RealEstateSpecialistModel::listWithFranchiseTitle(int $franchiseId)` | ported | No ORDER BY (as before). |
| models/home_model.php:312-333 `add_specialist` | controllers/admin/Section.php:401 | `RealEstateSpecialistModel::addSpecialist(array $data): int` | ported | DB part ported; file part: Upload rules S1. Returns affected rows. |
| models/home_model.php:334-356 `update_specialist` | controllers/admin/Section.php:399 | `RealEstateSpecialistModel::updateSpecialist(int $id, array $data): int` | ported | DB part ported; file part: Upload rules S1. Returns affected rows. |
| models/home_model.php:357-366 `delete_specialist` | controllers/admin/Section.php:359 | `RealEstateSpecialistModel::deleteSpecialist(int $id): array` | ported-with-change | (c) The unlink used an undefined `$row`, so the image was never removed. DB part ported: fetches the row(s), deletes, returns the fetched rows. File clean-up: Upload rules S2. |
| models/home_model.php:1689-1694 `show_cities_list` | controllers/admin/Section.php:1746 | `CitiesModel::listNewestFirst()` | ported | Identical to `get_cities_list`. |
| models/home_model.php:1695-1701 `add_city` | controllers/admin/Section.php:1777 | `CitiesModel::addCity(array $data): int` | ported | Returns affected rows. |
| models/home_model.php:1702-1707 `show_city_by_id` | controllers/admin/Section.php:1760 | `CitiesModel::find(int $id)` | ported | Built-in find. |
| models/home_model.php:1708-1714 `update_city` | controllers/admin/Section.php:1775 | `CitiesModel::updateCity(int $id, array $data): int` | ported | Returns affected rows. |
| models/home_model.php:1715-1722 `delete_city` | controllers/admin/Section.php:1785 | `CitiesModel::deleteCity(int $id): bool` | ported | Franchises pointing at the city are not touched (as before). |
| models/home_model.php:1833-1845 `show_franchise_owners_list` | controllers/admin/Section.php:1873 | `FranchisesOwnersModel::listWithFranchiseTitle(int $franchiseId)` | ported | ORDER BY fo.id DESC. |
| models/home_model.php:1846-1873 `add_franchise_owner` | controllers/admin/Section.php:1928 | `FranchisesOwnersModel::addOwner(array $data): int` | ported | DB part ported; file part: Upload rules O1. Returns affected rows. |
| models/home_model.php:1874-1899 `update_franchise_owner` | controllers/admin/Section.php:1926 | `FranchisesOwnersModel::updateOwner(int $id, array $data): int` | ported | DB part ported; file part: Upload rules O1. Returns affected rows. |
| models/home_model.php:1900-1909 `delete_franchise_owner` | controllers/admin/Section.php:1883 | `FranchisesOwnersModel::deleteOwner(int $id): array` | ported-with-change | (c) The unlink used an undefined `$row`, so the image was never removed. DB part ported: fetches the row(s), deletes, returns the fetched rows. File clean-up: Upload rules O2. |
| models/home_model.php:1910-1915 `show_franchise_owner_by_id` | controllers/admin/Section.php:1901 | `FranchisesOwnersModel::find(int $id)` | ported | Built-in find. |
| models/home_model.php:2033-2042 `change_status_franchises` | controllers/admin/Section.php:2025 | `FranchisesModel::setStatus($id, $status, 'is_active')` (BaseModel) | ported | Status is normalised to 0/1 by BaseModel; the admin list only links `/1` and `/0` (views/admin/manage_franchises/list.php:34). Returns bool instead of affected rows; the caller ignores it. |
| models/home_model.php:2081-2087 `show_franchiese_testimonials` | controllers/admin/Section.php:2542 | `FranchisesTestimonialsModel::byFranchiseNewestFirst(int $franchiseId)` | ported | ORDER BY id DESC. |
| models/home_model.php:2088-2094 `add_franchise_testimonials` | controllers/admin/Section.php:2584 | `FranchisesTestimonialsModel::addTestimonial(array $data): int` | ported | Returns affected rows. |
| models/home_model.php:2095-2100 `delete_franchise_testimonials` | controllers/admin/Section.php:2592 | `FranchisesTestimonialsModel::deleteTestimonial(int $id): bool` | ported-with-change | (c) Returned an undefined `$query` (null); now returns the delete result. The caller ignores it. |
| models/home_model.php:2101-2108 `change_status_franchise_testimonials` | controllers/admin/Section.php:2598 | `FranchisesTestimonialsModel::setStatus($id, $status, 'is_active')` (BaseModel) | ported | Status normalised to 0/1; the list only links `/1` and `/0` (views/admin/manage_franchises/testimonials_list.php:62). Returns bool; caller ignores it. |
| models/home_model.php:2109-2114 `show_franchise_testimonials_by_id` | controllers/admin/Section.php:2561 | `FranchisesTestimonialsModel::find(int $id)` | ported | Built-in find. |
| models/home_model.php:2115-2121 `update_franchise_testimonials` | controllers/admin/Section.php:2582 | `FranchisesTestimonialsModel::updateTestimonial(int $id, array $data): int` | ported | Returns affected rows. |
| models/ui/Ui_model.php:123-128 `check_franchisee` | controllers/Home.php:77 | `FranchisesModel::findIdOnly(int $id): ?array` | ported-with-change | (a) |
| models/ui/Ui_model.php:285-290 `get_owner_info` | controllers/Home.php:550 | `FranchisesOwnersModel::find(int $id)` | ported | Built-in find. |
| models/ui/Ui_model.php:291-296 `get_specialist_info` | controllers/Home.php:567 | `RealEstateSpecialistModel::find(int $id)` | ported | Built-in find. |
| models/ui/Ui_model.php:434-439 `get_franchisee_details` | controllers/Home.php:469 | `FranchisesModel::find(int $id)` | ported | Built-in find. |
| models/ui/Ui_model.php:440-445 `get_franchisee_location_by_city` | controllers/Home.php:791 | `FranchisesModel::activeLocationsByCity(int $cityId)` | ported-with-change | (a) GROUP BY location selects only the grouped column (valid under ONLY_FULL_GROUP_BY). No ORDER BY (as before). |
| models/ui/Ui_model.php:446-474 `search_franchisee_by_location` | controllers/Home.php:828 | search_id 1: `FranchisesModel::searchByCity(int $cityId, ?string $location)`; search_id 2: `FranchisesModel::searchByTitleOrLocation(string $search)` | ported-with-change | (a) Split into the two branches. Branch 1: comma join written as an inner join (same rows); location filter only when not null/''. Branch 2: original precedence kept, `(is_active = 1 AND title LIKE '%p0%') OR location LIKE 'p1'`, values bound unescaped for LIKE so wildcards behave as before; (c) a missing part 1 (no comma) was an undefined array key and became `''`, now explicitly `''`. Any other search_id built no query and failed; see controller notes. |
| models/ui/Ui_model.php:475-480 `get_franchisee_name` | controllers/Home.php:906 | `FranchisesModel::activeTitlesLike(string $title)` | ported-with-change | (a) LIKE value bound as `%title%` (wildcards behave as before). |
| models/ui/Ui_model.php:481-486 `get_all_franchisee_location` | controllers/Home.php:919 | `FranchisesModel::allActive()` | ported | No ORDER BY (as before). |
| views/our_franchisee.php:39 | controllers/Home.php:66 (view `our_franchisee`) | `CitiesModel::withActiveFranchises()` | ported | Same rows/order as :144; this copy selected `city_name, id` and the shared method returns `id, city_name`. The view reads both by key. |
| views/our_franchisee.php:108 | none | (same as :39) | dropped | (d) Inside a PHP block comment (`/* ... */`, lines 107-114, within an HTML comment). Identical SQL to :39, which is ported. |
| views/our_franchisee.php:144 | controllers/Home.php:66 | `CitiesModel::withActiveFranchises()` | ported | Unqualified `is_active` written as `f.is_active` (`cities` has no such column). |
| views/our_franchisee.php:154 | controllers/Home.php:66 | `FranchisesModel::activeByCity(int $cityId)` | ported-with-change | (a) ORDER BY title. |
| views/our_franchisee_details-24-05-2017.php:4 | none (file not loaded by any controller: `grep -rn "our_franchisee_details-24-05-2017" application` finds nothing) | `FranchisesModel::detailsWithOwner(int $id)` | ported-with-change | (a) Same SQL as our_franchisee_details.php:3; shares the method. |
| views/our_franchisee_details-24-05-2017.php:223 | none (see above) | `FranchisesTestimonialsModel::activeByFranchise(int $franchiseId)` | ported-with-change | (a) Same SQL as our_franchisee_details.php:281. |
| views/our_franchisee_details-24-05-2017.php:250 | none (see above) | `FranchisesOwnersModel::byFranchise(int $franchiseId)` | ported-with-change | (a) Same SQL as our_franchisee_details.php:319. |
| views/our_franchisee_details-24-05-2017.php:277 | none (see above) | `RealEstateSpecialistModel::byFranchiseOrderedByName(int $franchiseId)` | ported-with-change | (a) Same SQL as our_franchisee_details.php:348. |
| views/our_franchisee_details-24-05-2017.php:307 | none (see above) | `CitiesModel::findName(int $id)` | ported-with-change | (a) Same SQL as our_franchisee_details.php:378. |
| views/our_franchisee_details.php:3 | controllers/Home.php:82 (view `our_franchisee_details`, `$id` from `check_franchisee`) | `FranchisesModel::detailsWithOwner(int $id): ?array` | ported-with-change | (a) LEFT JOIN owners, LIMIT 1, no ORDER BY (as before); `franchises.*` selected last so franchise values win on shared column names. |
| views/our_franchisee_details.php:281 | controllers/Home.php:82 | `FranchisesTestimonialsModel::activeByFranchise(int $franchiseId)` | ported-with-change | (a) No ORDER BY (as before). |
| views/our_franchisee_details.php:319 | controllers/Home.php:82 | `FranchisesOwnersModel::byFranchise(int $franchiseId)` | ported-with-change | (a) ORDER BY id. |
| views/our_franchisee_details.php:348 | controllers/Home.php:82 | `RealEstateSpecialistModel::byFranchiseOrderedByName(int $franchiseId)` | ported-with-change | (a) Columns id, name, title, description, image; ORDER BY name. |
| views/our_franchisee_details.php:378 | controllers/Home.php:82 | `CitiesModel::findName(int $id): ?array` | ported-with-change | (a) Returns `['city_name' => ...]` or null. |

## Upload rules

None of these are in the models any more. The model methods take plain data; the admin step sets the file name key
(`logo` / `image`) in `$data` after moving the file with CI4 `UploadedFile`. Existing files under `uploads/` stay as they are.
In CI3, `$_SERVER['DOCUMENT_ROOT'] . FOLDER_NAME . '/uploads/...'` and `FCPATH . '/uploads/...'` both point at the
public `uploads/` folder.

**F1. Franchise logo** (`add_franchise` home_model.php:191-224, `update_franchise` home_model.php:225-263)

- Form field: `image`. Processed when the client file name is not empty.
- Target directory: `uploads/franchises/`.
- Type / MIME check: none. Size limit: none in code.
- File name: `rand() . time() . '.' . <extension of the client file name>`.
- Stored in: `franchises.logo`.
- Old file: not unlinked on update.
- On add, the CI3 function never received the files array (undefined variable), so no logo was saved on add. The intended behaviour, same rules as update, should apply on add.

**F3. Franchise delete** (`delete_franchise` home_model.php:288-298)

- CI3 tried to unlink `uploads/learning_gallery/` + `$row['franchise_owner_image']`, with `$row` undefined and a column that `franchises` does not have, so nothing was removed.
- `FranchisesModel::deleteFranchise()` returns the deleted row(s). The intended target is not clear from the code. Most likely it is the logo in `uploads/franchises/{logo}`. This needs a decision before the admin step implements it (see open issues). Until then: remove nothing, which matches current behaviour.

**S1. Specialist image** (`add_specialist` home_model.php:312-333, `update_specialist` home_model.php:334-356)

- Form field: `image`. Processed when the client file name is not empty.
- Target directory: `uploads/specialist/`.
- Type / MIME check: none. Size limit: none in code.
- File name: `rand() . time() . '.' . <extension of the client file name>`.
- Stored in: `real_estate_specialist.image`.
- Old file: not unlinked on update.

**S2. Specialist delete** (`delete_specialist` home_model.php:357-366)

- Intended: unlink `uploads/specialist/{image}` of the deleted row. CI3 used an undefined `$row`, so nothing was removed.
- Use the rows returned by `RealEstateSpecialistModel::deleteSpecialist()`. Unlink only when `image` is non-empty and the file exists.

**O1. Franchise owner image** (`add_franchise_owner` home_model.php:1846-1873, `update_franchise_owner` home_model.php:1874-1899)

- Form field: `image` (read from `$_FILES` directly). Processed when `tmp_name` is not empty.
- MIME detected with `finfo` (FILEINFO_MIME_TYPE). The condition was
  `(mime == 'image/png' && mime == 'image/jpeg' && mime == 'image/jpg') || size < 2000000`. The first part can never be true, so in practice only the size rule applied: file accepted when smaller than 2,000,000 bytes, otherwise silently skipped (row still saved without `image`).
  The intended rule appears to be "png/jpeg AND smaller than 2,000,000 bytes" (see open issues).
- Target directory: `uploads/franchise_owners/`.
- File name: `rand() . time() . '.' . <extension of the client file name>`.
- Stored in: `franchises_owners.image`.
- Old file: not unlinked on update.

**O2. Franchise owner delete** (`delete_franchise_owner` home_model.php:1900-1909)

- Intended: unlink `uploads/franchise_owners/{image}` of the deleted row. CI3 used an undefined `$row`, so nothing was removed.
- Use the rows returned by `FranchisesOwnersModel::deleteOwner()`. Unlink only when `image` is non-empty and the file exists.

## Notes for the controller step

Admin (`controllers/admin/Section.php`):

- All ids from URL segments are passed as `(int)`. `Model::find()` must get an int: `find(null)` returns every row.
- `manage_franchises` (:251) → `FranchisesModel::listWithCity()`.
- `add_franchise($id)` (:261) → city dropdown from `CitiesModel::listNewestFirst()`; edit form from `findWithCity($id)`.
  On submit → `updateFranchise()` / `addFranchise()` after the logo upload (F1), then redirect to `admin/section/manage_franchises/`.
  The controller builds `speciality_markets` from the city value; keep this.
- `delete_franchise` (:340) → `deleteFranchise()`, then file clean-up per F3, then redirect.
- `specialist_list` (:346), `update_specialist` (:364), `delete_specialist` (:358) → RealEstateSpecialistModel methods.
  Redirects to `admin/section/specialist_list/{fran_id}`. Flash message `success_msg` on delete.
- `manage_cities` (:1743), `add_city` (:1751), `delete_city` (:1784) → CitiesModel methods.
  `add_city` sets `created_date` to now on update too. Its redirect appends an undefined `$fran_id`; the intended target is `admin/section/manage_cities/`.
- `franchise_owners_list` (:1869), `update_franchise_owner` (:1888), `delete_franchise_owner` (:1882) → FranchisesOwnersModel methods.
  `update_franchise_owner` sets `created_date` to now on update too. Flash message `success_msg` on delete.
- `change_status_franchises` (:2024) → `FranchisesModel::setStatus($id, $status, 'is_active')`, then redirect.
- `manage_franchiese_testimonials` (:2538), `add_franchise_testimonials` (:2551), `delete_franchise_testimonials` (:2591), `change_status_franchise_testimonials` (:2597)
  → FranchisesTestimonialsModel methods (`setStatus(..., 'is_active')` for the status toggle). Flash message `success_msg` on delete/status; redirect to `admin/section/manage_franchiese_testimonials/{franchise_id}`.

Public (`controllers/Home.php`):

- `our_franchise_details($name, $id)` (:75) → `FranchisesModel::findIdOnly((int) $id)`; `show_404()` when empty; pass `id` to the view.
- `franchisee_details_enquiry($id)` (:445, lookup at :469) → `FranchisesModel::find((int) $id)` for the title used in the mail.
- `get_owner_info` (:548) / `get_specialist_info` (:565): read `user_id` from POST (trim, int) → `find()`. They echo an HTML fragment and `exit`; handle a null row.
- `get_franchisee_location_by_city` (:789): POST `city_id` → `activeLocationsByCity((int) ...)`. Echoes `<option>` list.
- `search_franchisee_by_location` (:809): POST `search_id`. 1 → `searchByCity((int) city_id, location_name ?? null)`; 2 → `searchByTitleOrLocation(search_by)`.
  Any other value built no query in CI3 and failed; return the empty-result JSON (`[]`) instead. Output is JSON (HTML fragment + map array).
- `get_franchisee_name` (:904): POST `search_by` → `activeTitlesLike()`. Echoes `<ul>` list.
- `get_all_franchisee_location` (:918) → `allActive()`. Builds the map array.

Views (ported in a later step):

- `our_franchisee.php`: `$rz1` (:39) and `$rz2` (:144) → `CitiesModel::withActiveFranchises()`; per-city list (:154) → `FranchisesModel::activeByCity((int) $rz['id'])`.
- `our_franchisee_details.php`: `$rz1` (:3) → `detailsWithOwner($id)`; testimonials (:281) → `activeByFranchise($rz1['id'])`; owners (:319) → `FranchisesOwnersModel::byFranchise()`;
  specialists (:348) → `byFranchiseOrderedByName()`; city (:378) → `CitiesModel::findName((int) $rz1['city'])`. Pass the results in from the controller.
- `our_franchisee_details-24-05-2017.php` is an unused copy of the details view and does not need to be ported as a view.
