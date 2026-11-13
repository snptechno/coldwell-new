# Port map: media

Domain tables: `coldwell_banker_news`, `coldwell_banker_industry_news`,
`coldwell_banker_knowledge_series`, `coldwell_banker_newsletter`, `research`, `awards`
(plus read-only lookups of two rows in `pages`).

Models: `app/Models/ColdwellBankerNewsModel.php`, `ColdwellBankerIndustryNewsModel.php`,
`ColdwellBankerKnowledgeSeriesModel.php`, `ColdwellBankerNewsletterModel.php`,
`ResearchModel.php`, `AwardsModel.php`.

The four news-like tables (news, industry news, knowledge series, newsletter) have the
same columns and the same CI3 functions, so their models expose the same methods:
`listNewestFirst()`, `latest()`, `findById()`, `countTotal()`, `loadMore()`,
`createItem()`, `updateItem()`, `deleteItem()`.

Parity: `php spark parity:check media` (suite `tests/_support/Parity/MediaParity.php`),
52 checks, all passing.

Controller paths below are relative to the old `application/` directory;
`Section.php` = `controllers/admin/Section.php`, `Home.php` = `controllers/Home.php`.

## Research

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1083 `show_research` | Section.php:1292 (`manage_research`) | `ResearchModel::listNewestFirst()` | ported | |
| models/home_model.php:1089 `show_research_by_id` | Section.php:1311 (`add_research` edit form) | `ResearchModel::findById(int $id)` | ported | |
| models/home_model.php:1095 `add_research` | Section.php:1333 (`add_research`) | `ResearchModel::createItem(array $data)` | ported-with-change | `$_FILES` handling removed (see Upload rules); `$data` carries `image_name` / `pdf_name` when stored. Missing `image_name`, `pdf_name`, `is_active` are filled with `''`, `''`, `0`: these columns are NOT NULL without default, and the strict sql_mode of MySQL 8 rejects the insert where non-strict MySQL stored those implicit values (rule b). Returns affected rows, as before. |
| models/home_model.php:1144 `change_status_research` | Section.php:1341 (`change_status_research`) | `BaseModel::setStatus($id, $status, 'is_active')` on `ResearchModel` | ported-with-change | Shared helper. Returns bool instead of affected rows (the caller ignores the value). Any status other than 1 is stored as 0; the admin list only links to 0 and 1. |
| models/home_model.php:1152 `update_research` | Section.php:1331 (`add_research` with id) | `ResearchModel::updateItem(int $id, array $data, ?string $imageName = null, ?string $pdfName = null)` | ported-with-change | `$_FILES` handling removed. Same statement order: field update, then one update per newly stored file. Returns affected rows of the last update. |
| models/home_model.php:1208 `delete_research` | Section.php:1348 (`delete_research`) | `ResearchModel::deleteItem(int $id)` | ported-with-change | Returns the deleted row (or null), as before; file removal moved to the upload step. CI3 read `$query['image_name']` on a null row when the id did not exist (PHP 8 warning); the port returns null and still runs the (no-op) delete. |
| models/ui/Ui_model.php:99 `research` | Home.php:141 (`research`) | `ResearchModel::pageInfo()` | ported-with-change | Reads `pages` id 8. CI3 returned `row()` (object); the port returns the row as an array or null (project rule). Placed here because `PagesModel` belongs to another domain; the controller may use that model's generic page lookup instead. |
| views/research.php:53 (inline SQL) | view of Home.php:139 (`research`) | `ResearchModel::activeByType('Project Reports')` | ported | `type = ? AND is_active = 1 ORDER BY id DESC`. |
| views/area_insights.php:53 (inline SQL) | view of Home.php:153 (`area_insights`) | `ResearchModel::activeByType('Area Insights')` | ported | Same method, other type. |

## Coldwell Banker news (Media Center)

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1219 `show_coldwell_banker_news` | Section.php:1355 (`coldwell_banker_news`) | `ColdwellBankerNewsModel::listNewestFirst()` | ported | |
| models/home_model.php:1390 `show_coldwell_banker_news_id` | Section.php:1541 (`add_coldwell_banker_news` edit form) | `ColdwellBankerNewsModel::findById(int $id)` | ported-with-change | The `ORDER BY id DESC` on a primary-key lookup has no effect and is not repeated (rule d); same row. |
| models/home_model.php:1397 `add_coldwell_banker_news` | Section.php:1566 (`add_coldwell_banker_news`) | `ColdwellBankerNewsModel::createItem(array $data)` | ported-with-change | `$_FILES` handling removed; `$data['image']` holds the stored file name. CI3 inserted only after the file was stored (otherwise returned null): the controller must call `createItem()` only then. |
| models/home_model.php:1419 `update_coldwell_banker_news` | Section.php:1564 (`add_coldwell_banker_news` with id) | `ColdwellBankerNewsModel::updateItem(int $id, array $data, ?string $newImage = null)` | ported-with-change | `$_FILES` handling and the `echo` removed. Field update, then image update when a new file was stored. Returns affected rows of the last update. |
| models/home_model.php:1448 `delete_coldwell_banker_news` | Section.php:1574 (`delete_coldwell_banker_news`) | `ColdwellBankerNewsModel::deleteItem(int $id)` | ported-with-change | Returns the deleted row (or null); file removal moved to the upload step; null-row access fixed (rule c). |
| models/ui/Ui_model.php:309 `get_iframe_image` | Home.php:663 (`iframe_image`) | `ColdwellBankerNewsModel::findById(int $id)` | ported | |
| models/ui/Ui_model.php:406 `count_total_news` | Home.php:741 (`load_more_news`) | `ColdwellBankerNewsModel::countTotal()` | ported | Same shape `['count' => n]`. |
| models/ui/Ui_model.php:412 `load_more_news` | Home.php:744 (`load_more_news`) | `ColdwellBankerNewsModel::loadMore(int $totalNo, int $startNo)` | ported-with-change | LIMIT/OFFSET bound through the Query Builder (rule a). Same page size (8) and same `min(8, total - start)` limit. When start is past the total, CI3 sent a negative LIMIT (SQL error); the port returns `[]` (rule c). A computed limit of 0 also returns `[]`, as `LIMIT n,0` did. |
| views/media_center.php:48 (inline SQL) | view of Home.php:96 (`media_center`) | `ColdwellBankerNewsModel::latest()` | ported | `ORDER BY id DESC LIMIT 0,8`. |

## Industry news

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1458 `show_coldwell_banker_industry_news` | Section.php:1581 (`coldwell_banker_industry_news`) | `ColdwellBankerIndustryNewsModel::listNewestFirst()` | ported | |
| models/home_model.php:1464 `show_coldwell_banker_industry_news_id` | Section.php:1595 (`add_coldwell_banker_industry_news` edit form) | `ColdwellBankerIndustryNewsModel::findById(int $id)` | ported-with-change | Redundant `ORDER BY` on a primary-key lookup not repeated (rule d). |
| models/home_model.php:1472 `add_coldwell_banker_industry_news` | Section.php:1620 | `ColdwellBankerIndustryNewsModel::createItem(array $data)` | ported-with-change | As news: insert only after the image is stored. |
| models/home_model.php:1495 `update_coldwell_banker_industry_news` | Section.php:1618 | `ColdwellBankerIndustryNewsModel::updateItem(int $id, array $data, ?string $newImage = null)` | ported-with-change | As news. |
| models/home_model.php:1524 `delete_coldwell_banker_industry_news` | Section.php:1628 | `ColdwellBankerIndustryNewsModel::deleteItem(int $id)` | ported-with-change | As news. |
| models/ui/Ui_model.php:321 `iframe_industry_news` | Home.php:675 (`iframe_industry_news`) | `ColdwellBankerIndustryNewsModel::findById(int $id)` | ported | |
| models/ui/Ui_model.php:383 `count_total_industry_news` | Home.php:726 (`load_more_industry_news`) | `ColdwellBankerIndustryNewsModel::countTotal()` | ported | |
| models/ui/Ui_model.php:389 `load_more_industry_news` | Home.php:729 (`load_more_industry_news`) | `ColdwellBankerIndustryNewsModel::loadMore(int $totalNo, int $startNo)` | ported-with-change | As news. |
| views/industry_news.php:51 (inline SQL) | view of Home.php:104 (`industry_news`) | `ColdwellBankerIndustryNewsModel::latest()` | ported | |

## Knowledge series

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1536 `show_coldwell_banker_knowledge_series` | Section.php:1637 (`coldwell_banker_knowledge_series`) | `ColdwellBankerKnowledgeSeriesModel::listNewestFirst()` | ported | |
| models/home_model.php:1542 `show_coldwell_banker_knowledge_series_id` | Section.php:1651 (`add_coldwell_banker_knowledge_series` edit form) | `ColdwellBankerKnowledgeSeriesModel::findById(int $id)` | ported-with-change | Redundant `ORDER BY` not repeated (rule d). |
| models/home_model.php:1550 `add_coldwell_banker_knowledge_series` | Section.php:1675 | `ColdwellBankerKnowledgeSeriesModel::createItem(array $data)` | ported-with-change | As news. |
| models/home_model.php:1573 `update_coldwell_banker_knowledge_series` | Section.php:1673 | `ColdwellBankerKnowledgeSeriesModel::updateItem(int $id, array $data, ?string $newImage = null)` | ported-with-change | As news. |
| models/home_model.php:1602 `delete_coldwell_banker_knowledge_series` | Section.php:1683 | `ColdwellBankerKnowledgeSeriesModel::deleteItem(int $id)` | ported-with-change | As news. |
| models/ui/Ui_model.php:315 `iframe_knowledge_image` | Home.php:669 (`iframe_knowledge_image`) | `ColdwellBankerKnowledgeSeriesModel::findById(int $id)` | ported | |
| models/ui/Ui_model.php:361 `count_total_knowledge_series` | Home.php:710 (`load_more_knowledge_series`) | `ColdwellBankerKnowledgeSeriesModel::countTotal()` | ported | |
| models/ui/Ui_model.php:367 `load_more_knowledge_series` | Home.php:713 (`load_more_knowledge_series`) | `ColdwellBankerKnowledgeSeriesModel::loadMore(int $totalNo, int $startNo)` | ported-with-change | As news. |
| views/knowledge_series.php:51 (inline SQL) | view of Home.php:111 (`knowledge_series`) | `ColdwellBankerKnowledgeSeriesModel::latest()` | ported | |

## Newsletter

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1613 `show_coldwell_banker_newsletter` | Section.php:1692 (`coldwell_banker_newsletter`) | `ColdwellBankerNewsletterModel::listNewestFirst()` | ported | |
| models/home_model.php:1619 `show_coldwell_banker_newsletter_id` | Section.php:1706 (`add_coldwell_banker_newsletter` edit form) | `ColdwellBankerNewsletterModel::findById(int $id)` | ported-with-change | Redundant `ORDER BY` not repeated (rule d). |
| models/home_model.php:1627 `add_coldwell_banker_newsletter` | Section.php:1731 | `ColdwellBankerNewsletterModel::createItem(array $data)` | ported-with-change | As news. |
| models/home_model.php:1650 `update_coldwell_banker_newsletter` | Section.php:1729 | `ColdwellBankerNewsletterModel::updateItem(int $id, array $data, ?string $newImage = null)` | ported-with-change | As news. |
| models/home_model.php:1679 `delete_coldwell_banker_newsletter` | Section.php:1739 | `ColdwellBankerNewsletterModel::deleteItem(int $id)` | ported-with-change | As news. |
| models/ui/Ui_model.php:327 `iframe_newsletter` | Home.php:681 (`iframe_newsletter`) | `ColdwellBankerNewsletterModel::findById(int $id)` | ported | |
| models/ui/Ui_model.php:339 `count_total_newletter` | Home.php:695 (`load_more_newsletter`) | `ColdwellBankerNewsletterModel::countTotal()` | ported | |
| models/ui/Ui_model.php:345 `load_more_newsletter` | Home.php:698 (`load_more_newsletter`) | `ColdwellBankerNewsletterModel::loadMore(int $totalNo, int $startNo)` | ported-with-change | As news. |
| views/newsletter.php:59 (inline SQL) | view of Home.php:118 (`newsletter`) | `ColdwellBankerNewsletterModel::latest()` | ported | |

## Awards

| Old (file:line) | Callers | New | Status | Notes |
|---|---|---|---|---|
| models/home_model.php:1723 `add_coldwell_banker_award` | Section.php:1827 (`add_coldwell_banker_award`) | `AwardsModel::createItem(array $data)` | ported-with-change | As news: insert only after the image is stored. |
| models/home_model.php:1744 `update_coldwell_banker_award` | Section.php:1825 (`add_coldwell_banker_award` with id) | `AwardsModel::updateItem(int $id, array $data, ?string $newImage = null)` | ported-with-change | As news. |
| models/home_model.php:1773 `show_coldwell_banker_awards` | Section.php:1792 (`coldwell_banker_awards`) | `AwardsModel::listNewestFirst()` | ported | |
| models/home_model.php:1779 `delete_coldwell_banker_award` | Section.php:1835 (`delete_coldwell_banker_award`) | `AwardsModel::deleteItem(int $id)` | ported-with-change | As news. |
| models/home_model.php:1789 `show_coldwell_banker_award_by_id` | Section.php:1806 (`add_coldwell_banker_award` edit form) | `AwardsModel::findById(int $id)` | ported | |
| models/ui/Ui_model.php:87 `awards` | Home.php:127 (`awards`) | `AwardsModel::pageInfo()` | ported-with-change | Reads `pages` id 6 (the same page row that `Ui_model::newsletter` reads; kept as is). Returns an array or null instead of the `row()` object. Placed here because `PagesModel` belongs to another domain. |
| models/ui/Ui_model.php:333 `iframe_awards` | Home.php:687 (`iframe_awards`) | `AwardsModel::findById(int $id)` | ported | |
| views/awards.php:47 (inline SQL) | view of Home.php:125 (`awards`) | `AwardsModel::listNewestFirst()` | ported | Same query as `show_coldwell_banker_awards`. |

No item was dropped: every work-list function has a caller in the canonical
controllers (grep `->function_name(` over `controllers/Home.php` and
`controllers/admin/Section.php`, lines listed above).

## Upload rules

The CI3 functions below read `$_FILES`, store files and remove old ones. None of this is
in the models; the admin step rebuilds it with CI4 `UploadedFile` and passes the stored
file names to the model methods. The rules below describe what CI3 actually did.
All paths are `FCPATH`-relative (`$_SERVER['DOCUMENT_ROOT'] . FOLDER_NAME` in CI3).
"MIME subtype" means the part after `/` of the type detected with `finfo` from the
file content (e.g. `image/jpeg` gives `jpeg`).

### Research (`add_research`, `update_research`, `delete_research`)

- Fields: `image` and `documents` (form `views/admin/research/add.php`), both optional.
  A field is processed only when its client file name is not empty.
- Directory: `uploads/research/` for both.
- `image`: the CI3 condition was `(mime == png AND mime == jpeg AND mime == jpg) OR size < 5000000`;
  the first part can never be true, so the effective rule is: any type, size below
  5,000,000 bytes.
- `documents`: condition `mime == 'application/pdf' OR size < 5000000`, i.e. a PDF of any
  size, or any file below 5,000,000 bytes.
- File name: `rand() . time() . '.' . <extension of the client file name>`.
- Stored names go to `image_name` / `pdf_name`.
- Add: the row is inserted whether or not the files were stored
  (`createItem($data)` with the names that were stored).
- Update: the field update runs first; then for each stored file one update of
  `image_name` or `pdf_name` (`updateItem($id, $data, $imageName, $pdfName)`).
  The previous files are not removed on replace.
- Delete: removes `uploads/research/<image_name>` and `uploads/research/<pdf_name>` of the
  deleted row (CI3 did this before deleting the row and without checking that the
  names are non-empty or that the files exist). Use the row returned by `deleteItem()`.

### News (`add_coldwell_banker_news`, `update_coldwell_banker_news`, `delete_coldwell_banker_news`)

- Field: `image` (form `views/admin/coldwell_banker_news/add.php`).
- Directory: `uploads/news/`.
- Check: detected MIME is `image/png`, `image/jpeg` or `image/jpg` AND size below
  2,000,000 bytes.
- File name: `time() . rand() . '.' . <MIME subtype>`, stored in `image`.
- Add: the row is inserted only when the file was stored; otherwise nothing is inserted.
- Update: runs the field update, then (CI3 ran this whenever `$_FILES` was non-empty,
  i.e. on every form post) checks the file; on success updates `image`, on failure
  (including "no file chosen") printed an error text (see controller notes). The
  previous image is not removed on replace.
- Delete: removes `uploads/news/<image>` of the deleted row.

### Industry news, knowledge series, newsletter, awards

Same structure as news, with these differences:

| Function group | Field | Directory |
|---|---|---|
| `*_coldwell_banker_industry_news` | `image` | `uploads/industry_news/` |
| `*_coldwell_banker_knowledge_series` | `image` | `uploads/knowledge_series/` |
| `*_coldwell_banker_newsletter` | `image` | `uploads/newsletter/` |
| `*_coldwell_banker_award` | `image` | `uploads/awards/` |

- Check: the CI3 condition was `(mime == png AND mime == jpeg AND mime == jpg) OR size < 2000000`,
  so the effective rule is: any type, size below 2,000,000 bytes (only news enforced
  the image types).
- File name: `time() . rand() . '.' . <MIME subtype>`, stored in `image`.
- Add: insert only when the file was stored.
- Update: field update, then `image` update when a new file was stored; on a failed
  check CI3 printed an error text. With no file chosen the size check passed and the
  move failed silently. The previous image is not removed on replace.
- Delete: removes `uploads/<directory>/<image>` of the deleted row.

## Notes for the controller step

- Admin actions (`Section.php`) build `$data` themselves (`title`, `description` from
  `news_desc` for news/industry news or `description` for the others, `external_link`,
  `created_date = date('Y-m-d H:i:s')`; awards: `name` from the `title` field;
  research: `type`, `title`, `description`) and redirect to the list after add, update,
  delete and status change. Keep the redirects in the controllers.
- `update_coldwell_banker_*` and `update_coldwell_banker_award` printed
  "some error occured" when the image check failed (news: also when no file was chosen),
  before the redirect. Not ported into the models; decide in the controller step.
- `change_status_research($id, $status)`: call `ResearchModel::setStatus((int) $id, (int) $status, 'is_active')`.
- Delete actions: call `deleteItem($id)`, then remove the file(s) named in the returned row
  (see Upload rules), only when the row is not null and the name is not empty.
- Model ids must be positive integers: CI4 `Model::update()` rejects id 0, where CI3
  ran an update that matched nothing. Route segments should be constrained to `(:num)`.
- `load_more_*` (Home.php:692-750): read `start_no` from POST as an int, take
  `countTotal()['count']` as the total, call `loadMore($total, $start)`, build the
  `<li>` HTML and `echo`/`exit` as before (in CI4: return the response body).
- `iframe_*` (Home.php:662-690): the controller adds `image_type` to the returned row
  (`news`, `knowledge_series`, `industry_news`, `newsletter`, `awards`); when the row is
  null, CI3 ended up with an array holding only `image_type`.
- `Home::awards` and `Home::research` passed a `row()` object as `page_info`; the new
  `pageInfo()` methods return an array, so the views (or the controller) must switch
  from `->` to array access.
- Public views call: `media_center` -> `ColdwellBankerNewsModel::latest()`,
  `industry_news` -> `ColdwellBankerIndustryNewsModel::latest()`,
  `knowledge_series` -> `ColdwellBankerKnowledgeSeriesModel::latest()`,
  `newsletter` -> `ColdwellBankerNewsletterModel::latest()`,
  `awards` -> `AwardsModel::listNewestFirst()`,
  `research` -> `ResearchModel::activeByType('Project Reports')`,
  `area_insights` -> `ResearchModel::activeByType('Area Insights')`.
  Better passed in from the controller than queried in the view.
