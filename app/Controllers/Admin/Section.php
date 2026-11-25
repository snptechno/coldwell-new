<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AwardsModel;
use App\Models\CareersTestimonialsModel;
use App\Models\CbIndiaVideosModel;
use App\Models\CitiesModel;
use App\Models\ColdwellBankerIndustryNewsModel;
use App\Models\ColdwellBankerKnowledgeSeriesModel;
use App\Models\ColdwellBankerNewsletterModel;
use App\Models\ColdwellBankerNewsModel;
use App\Models\ContactEnquiryModel;
use App\Models\CorporateTeamsModel;
use App\Models\EventsModel;
use App\Models\FeaturedVideosModel;
use App\Models\FindingHomeVideosModel;
use App\Models\FranchiseeEnquiryModel;
use App\Models\FranchiseeProjectEnquiryModel;
use App\Models\FranchisesModel;
use App\Models\FranchisesOwnersModel;
use App\Models\FranchisesTestimonialsModel;
use App\Models\GalleryModel;
use App\Models\GalleryThumbsModel;
use App\Models\JobApplicationsModel;
use App\Models\JobsModel;
use App\Models\LearningModel;
use App\Models\LifeAtColdwellBankerImagesModel;
use App\Models\LifeAtColdwellBankerModel;
use App\Models\NewsletterSubscriptionsModel;
use App\Models\NriEnquiryModel;
use App\Models\PagesModel;
use App\Models\RealEstateSpecialistModel;
use App\Models\ResearchModel;
use App\Models\SellingHomeVideosModel;
use App\Models\SuccessStoryModel;
use App\Models\TestimonialVideosModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class Section extends BaseController
{
    /**
     * Allowed image MIME types and extensions (ADM-02).
     */
    private const IMAGE_MIMES = [
        'image/jpeg' => 'jpeg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
    ];

    private const IMAGE_EXTS = ['jpg', 'jpeg', 'png'];

    /**
     * Validate and store an uploaded image file under public/uploads/{subdir}/.
     *
     * @param string $fieldName Form input name (default 'image')
     * @param string $subdir    Subdirectory inside public/uploads/
     * @param int    $maxBytes  Maximum allowed file size in bytes (0 = no explicit cap beyond PHP ini)
     * @param bool   $useMimeSubtypeForName If true, names file time().rand().'.'.mimeSubtype (news/awards/life_at_cb style); otherwise rand().time().'.'.clientExt
     */
    private function storeUploadedImage(
        string $fieldName,
        string $subdir,
        int $maxBytes = 5_000_000,
        bool $useMimeSubtypeForName = false
    ): ?string {
        $file = $this->request->getFile($fieldName);
        if (! $file instanceof UploadedFile || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        if ($maxBytes > 0 && $file->getSize() >= $maxBytes) {
            return null;
        }

        $mime = strtolower((string) $file->getMimeType());
        $ext  = strtolower((string) $file->getClientExtension());

        $image = @getimagesize($file->getTempName());
        $mimeExtensions = ['image/jpeg' => ['jpg', 'jpeg'], 'image/jpg' => ['jpg', 'jpeg'], 'image/png' => ['png']];
        if (! isset(self::IMAGE_MIMES[$mime]) || ! in_array($ext, $mimeExtensions[$mime] ?? [], true) || $image === false || $image[0] > 8000 || $image[1] > 8000 || ($image[0] * $image[1]) > 20_000_000) {
            return null;
        }

        $finalExt = self::IMAGE_MIMES[$mime];
        $filename = $useMimeSubtypeForName
            ? time() . mt_rand() . '.' . $finalExt
            : mt_rand() . time() . '.' . $finalExt;

        $targetDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . trim($subdir, '\\/');
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (! $file->move($targetDir, $filename)) {
            return null;
        }

        return $filename;
    }

    /**
     * Validate and store an uploaded PDF document under public/uploads/{subdir}/.
     */
    private function storeUploadedPdf(string $fieldName, string $subdir, int $maxBytes = 5_000_000): ?string
    {
        $file = $this->request->getFile($fieldName);
        if (! $file instanceof UploadedFile || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        if ($maxBytes > 0 && $file->getSize() >= $maxBytes) {
            return null;
        }

        $mime = strtolower((string) $file->getMimeType());
        $ext  = strtolower((string) $file->getClientExtension());

        $handle = @fopen($file->getTempName(), 'rb');
        $signature = $handle !== false ? fread($handle, 1024) : false;
        if ($handle !== false) {
            fclose($handle);
        }
        if ($mime !== 'application/pdf' || $ext !== 'pdf' || ! is_string($signature) || ! str_starts_with($signature, '%PDF-')) {
            return null;
        }

        $filename  = mt_rand() . time() . '.pdf';
        $targetDir = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . trim($subdir, '\\/');
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if (! $file->move($targetDir, $filename)) {
            return null;
        }

        return $filename;
    }

    /**
     * Safely unlink a file inside public/uploads/{subdir}/ if it exists.
     */
    private function unlinkUploadFile(string $subdir, ?string $filename): void
    {
        $filename = trim((string) $filename);
        if ($filename === '') {
            return;
        }

        $safeName = basename($filename);
        $path     = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . trim($subdir, '\\/') . DIRECTORY_SEPARATOR . $safeName;
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /**
     * Render a view wrapped in admin/_layout_main.
     *
     * @param array<string, mixed> $data
     */
    private function renderAdmin(string $subview, array $data): string
    {
        $data['subview'] = view($subview, $data);

        return view('admin/_layout_main', $data);
    }

    // ------------------------------------------------------------------
    // Learning Section: Overview, Gallery, Thumbnails, Events
    // ------------------------------------------------------------------

    public function learning_overview(): string
    {
        $data = [
            'title'      => 'Learning Overview | List',
            'form_title' => 'Overview | List',
            'info'       => (new LearningModel())->overviewList(),
        ];

        return $this->renderAdmin('admin/manage_overview/list', $data);
    }

    public function update_overview($id = null): string|RedirectResponse
    {
        $rules = [
            'overview'   => ['label' => 'Overview is required', 'rules' => 'required|trim'],
            'university' => ['label' => 'University text is required', 'rules' => 'required|trim'],
        ];

        $model = new LearningModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Overview | Add/Update',
                'form_title' => 'Overview | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['page_info'] = $model->findOverview((int) $id);
            }

            return $this->renderAdmin('admin/manage_overview/update', $data);
        }

        $updateData = [
            'overview'     => $this->request->getPost('overview'),
            'university'   => $this->request->getPost('university'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $logo = $this->storeUploadedImage('image', 'university_logo');
        if ($logo !== null) {
            $updateData['university_logo'] = $logo;
        }

        if ($id !== null && $id !== '') {
            $model->updateOverview((int) $id, $updateData);
        }

        return redirect()->to(site_url('admin/section/learning_overview/'));
    }

    public function learning_gallery(): string
    {
        $data = [
            'title'      => 'Learning Gallery | List',
            'form_title' => 'Learning Gallery | List',
            'info'       => (new GalleryModel())->galleryList(),
        ];

        return $this->renderAdmin('admin/learning_gallery/list', $data);
    }

    public function add_learning_gallery($id = null): string|RedirectResponse
    {
        $rules = [
            'title' => ['label' => 'Title is required', 'rules' => 'required|trim'],
        ];

        $model = new GalleryModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Learning Gallery | Add/Update',
                'form_title' => 'Learning Gallery | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['image_info'] = $model->findImage((int) $id);
            }

            return $this->renderAdmin('admin/learning_gallery/image_add', $data);
        }

        $dateTime = trim((string) $this->request->getPost('datepicker'));
        $saveData = [
            'title'        => $this->request->getPost('title'),
            'date_time'    => $dateTime !== '' ? $dateTime : date('Y-m-d'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'learning_gallery');
        if ($imageName !== null) {
            $saveData['image_name'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateImage((int) $id, $saveData);
        } else {
            $model->addImage($saveData);
        }

        return redirect()->to(site_url('admin/section/learning_gallery/'));
    }

    public function delete_learning_gallery_image($id): RedirectResponse
    {
        $rows = (new GalleryModel())->deleteImage((int) $id);
        foreach ($rows as $row) {
            $this->unlinkUploadFile('learning_gallery', $row['image_name'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/learning_gallery/'));
    }

    public function change_status_learning_gallery($id, $status): RedirectResponse
    {
        (new GalleryModel())->setStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/learning_gallery/'));
    }

    public function update_group_id_life_at_coldwell_gallery(): ResponseInterface
    {
        $id      = (int) $this->request->getPost('id');
        $groupId = (int) $this->request->getPost('group_id');
        (new GalleryModel())->updateGroupId($id, $groupId);

        return $this->response->setContentType('text/plain')->setBody('');
    }

    public function learning_gallery_thumbnails(): string
    {
        $data = [
            'title'      => 'Learning Gallery Thumbnails| List',
            'form_title' => 'Learning Gallery Thumbnails | List',
            'info'       => (new GalleryThumbsModel())->thumbnailList(),
        ];

        return $this->renderAdmin('admin/learning_thumb_gallery/list', $data);
    }

    public function add_gallery_thumbnail($id = null): string|RedirectResponse
    {
        $rules = [
            'title'      => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'datepicker' => ['label' => 'Date is required', 'rules' => 'required|trim'],
        ];

        $model = new GalleryThumbsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Learning Gallery Thumbnail | Add/Update',
                'form_title' => 'Learning Gallery Thumbnail | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['image_info'] = $model->findThumbnail((int) $id);
            }

            return $this->renderAdmin('admin/learning_thumb_gallery/image_add', $data);
        }

        $saveData = [
            'title'        => $this->request->getPost('title'),
            'date_time'    => $this->request->getPost('datepicker'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'learning_gallery_thumb');
        if ($imageName !== null) {
            $saveData['image_name'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateThumbnail((int) $id, $saveData);
        } else {
            $model->addThumbnail($saveData);
        }

        return redirect()->to(site_url('admin/section/learning_gallery_thumbnails/'));
    }

    public function delete_gallery_thumbnail_image($id): RedirectResponse
    {
        $rows = (new GalleryThumbsModel())->deleteThumbnail((int) $id);
        foreach ($rows as $row) {
            $this->unlinkUploadFile('learning_gallery_thumb', $row['image_name'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/learning_gallery_thumbnails/'));
    }

    public function change_status_learning_gallery_thumbnail($id, $status): RedirectResponse
    {
        (new GalleryThumbsModel())->setStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/learning_gallery_thumbnails/'));
    }

    public function update_group_id_life_at_coldwell_gallery_thumb(): ResponseInterface
    {
        $id      = (int) $this->request->getPost('id');
        $groupId = (int) $this->request->getPost('group_id');
        (new GalleryThumbsModel())->updateGroupId($id, $groupId);

        return $this->response->setContentType('text/plain')->setBody('');
    }

    public function learning_events(): string
    {
        $data = [
            'title'      => 'Learning Events | List',
            'form_title' => 'Learning Events | List',
            'info'       => (new EventsModel())->eventList(),
        ];

        return $this->renderAdmin('admin/manage_events/list', $data);
    }

    public function add_events($id = null): string|RedirectResponse
    {
        $rules = [
            'title'      => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'start_date' => ['label' => 'Start Date is required', 'rules' => 'required|trim'],
            'end_date'   => ['label' => 'End Date is required', 'rules' => 'required|trim'],
            'duration'   => ['label' => 'Duration is required', 'rules' => 'required|trim'],
        ];

        $model = new EventsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Learning Events | Add/Update',
                'form_title' => 'Learning Events | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['event_info'] = $model->findEvent((int) $id);
            }

            return $this->renderAdmin('admin/manage_events/add', $data);
        }

        $saveData = [
            'event_name'   => $this->request->getPost('title'),
            'start_date'   => $this->request->getPost('start_date'),
            'end_date'     => $this->request->getPost('end_date'),
            'time'         => $this->request->getPost('duration'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateEvent((int) $id, $saveData);
        } else {
            $model->addEvent($saveData);
        }

        return redirect()->to(site_url('admin/section/learning_events/'));
    }

    public function change_event_status($id, $status): RedirectResponse
    {
        (new EventsModel())->setStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/learning_events/'));
    }

    public function delete_event($id): RedirectResponse
    {
        (new EventsModel())->deleteEvent((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/learning_events/'));
    }

    // ------------------------------------------------------------------
    // Cities & Franchises (Specialists, Owners, Testimonials)
    // ------------------------------------------------------------------

    public function manage_cities(): string
    {
        $data = [
            'title'      => 'Manage Cities| List',
            'form_title' => 'Manage Cities | List',
            'info'       => (new CitiesModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/manage_cities/list', $data);
    }

    public function add_city($id = null): string|RedirectResponse
    {
        $rules = [
            'name' => ['label' => 'City Name is required', 'rules' => 'required|trim'],
        ];

        $model = new CitiesModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Manage City | Add/Update',
                'form_title' => 'Manage City | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['city_info'] = $model->find((int) $id);
            }

            return $this->renderAdmin('admin/manage_cities/add', $data);
        }

        $saveData = [
            'city_name'    => $this->request->getPost('name'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateCity((int) $id, $saveData);
        } else {
            $model->addCity($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_cities/'));
    }

    public function delete_city($id): RedirectResponse
    {
        (new CitiesModel())->deleteCity((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_cities/'));
    }

    public function manage_franchises(): string
    {
        $data = [
            'title'      => 'Manage Franchises | List',
            'form_title' => 'Manage Franchises | List',
            'info'       => (new FranchisesModel())->listWithCity(),
        ];

        return $this->renderAdmin('admin/manage_franchises/list', $data);
    }

    public function add_franchise($id = null): string|RedirectResponse
    {
        $rules = [
            'title'            => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'city'             => ['label' => 'City is required', 'rules' => 'required|trim'],
            'location'         => ['label' => 'Location is required', 'rules' => 'required|trim'],
            'franchise_status' => ['label' => 'Status is required', 'rules' => 'required|trim'],
        ];

        $model = new FranchisesModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Manage Franchises | Add/Update',
                'form_title' => 'Manage Franchises | Add/Update',
                'city_list'  => (new CitiesModel())->listNewestFirst(),
            ];
            if ($id !== null && $id !== '') {
                $data['franchise_info'] = $model->findWithCity((int) $id);
            }

            return $this->renderAdmin('admin/manage_franchises/add', $data);
        }

        $city     = $this->request->getPost('city');
        $saveData = [
            'title'                => $this->request->getPost('title'),
            'project_description'  => $this->request->getPost('project_description'),
            'mobile_no'            => $this->request->getPost('mobile_no'),
            'email_id'             => $this->request->getPost('email_id'),
            'website_url'          => $this->request->getPost('web_url') ?? $this->request->getPost('website_url'),
            'video_url'            => $this->request->getPost('video_url'),
            'city'                 => $city,
            'location'             => $this->request->getPost('location'),
            'address'              => $this->request->getPost('address'),
            'latitude'             => $this->request->getPost('latitude'),
            'longitude'            => $this->request->getPost('longitude'),
            'speciality_markets'   => $city,
            'speciality_market'    => $this->request->getPost('sp_market'),
            'languages'            => $this->request->getPost('languages'),
            'location_description' => $this->request->getPost('location_description'),
            'office_specialities'  => $this->request->getPost('office_speciality') ?? $this->request->getPost('office_specialities'),
            'franchise_status'     => $this->request->getPost('franchise_status'),
            'external_link'        => $this->request->getPost('external_link'),
            'created_date'         => date('Y-m-d H:i:s'),
        ];

        $logo = $this->storeUploadedImage('image', 'franchises');
        if ($logo !== null) {
            $saveData['logo'] = $logo;
        }

        if ($id !== null && $id !== '') {
            $model->updateFranchise((int) $id, $saveData);
        } else {
            $model->addFranchise($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_franchises/'));
    }

    public function delete_franchise($id): RedirectResponse
    {
        $rows = (new FranchisesModel())->deleteFranchise((int) $id);
        foreach ($rows as $row) {
            $this->unlinkUploadFile('franchises', $row['logo'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_franchises/'));
    }

    public function change_status_franchises($id, $status): RedirectResponse
    {
        (new FranchisesModel())->setStatus((int) $id, (int) $status, 'is_active');

        return redirect()->to(site_url('admin/section/manage_franchises/'));
    }

    public function specialist_list($franId, $id = null): string
    {
        return $this->manage_real_estate_specialist($franId, $id);
    }

    public function manage_real_estate_specialist($franId, $id = null): string
    {
        $data = [
            'title'          => 'Manage Real Estate Specialist | List',
            'form_title'     => 'Manage Real Estate Specialist | List',
            'fran_id'        => $franId,
            'specialist_id'  => $id,
            'franchise_info' => (new RealEstateSpecialistModel())->listWithFranchiseTitle((int) $franId),
        ];

        return $this->renderAdmin('admin/manage_franchises/specialist_list', $data);
    }

    public function update_specialist($franchiseId, $id = null): string|RedirectResponse
    {
        return $this->update_real_estate_specialist($franchiseId, $id);
    }

    public function update_real_estate_specialist($franchiseId, $id = null): string|RedirectResponse
    {
        $rules = [
            'name'        => ['label' => 'Name is required', 'rules' => 'required|trim'],
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new RealEstateSpecialistModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'         => 'Real Estate Specialist | Add/Update',
                'form_title'    => 'Real Estate Specialist | Add/Update',
                'fran_id'       => $franchiseId,
                'specialist_id' => $id,
            ];
            if ($id !== null && $id !== '') {
                $data['franchise_info'] = $model->find((int) $id);
            }

            return $this->renderAdmin('admin/manage_franchises/specialist_update', $data);
        }

        $saveData = [
            'franchise_id' => $franchiseId,
            'name'         => $this->request->getPost('name'),
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'specialist');
        if ($imageName !== null) {
            $saveData['image'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateSpecialist((int) $id, $saveData);
        } else {
            if (! isset($saveData['image'])) {
                $saveData['image'] = '';
            }
            $model->addSpecialist($saveData);
        }

        return redirect()->to(site_url('admin/section/specialist_list/' . $franchiseId));
    }

    public function delete_specialist($franchiseId, $id): RedirectResponse
    {
        $rows = (new RealEstateSpecialistModel())->deleteSpecialist((int) $id);
        foreach ($rows as $row) {
            $this->unlinkUploadFile('specialist', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/specialist_list/' . $franchiseId));
    }

    public function franchise_owners_list($franId, $id = null): string
    {
        return $this->manage_franchise_owners($franId, $id);
    }

    public function manage_franchise_owners($franId, $id = null): string
    {
        $data = [
            'title'          => 'Manage Franchise Owners | List',
            'form_title'     => 'Manage Franchise Owners | List',
            'fran_id'        => $franId,
            'specialist_id'  => $id,
            'franchise_info' => (new FranchisesOwnersModel())->listWithFranchiseTitle((int) $franId),
        ];

        return $this->renderAdmin('admin/manage_franchises/franchise_owners_list', $data);
    }

    public function update_franchise_owner($franchiseId, $id = null): string|RedirectResponse
    {
        return $this->update_franchise_owners($franchiseId, $id);
    }

    public function update_franchise_owners($franchiseId, $id = null): string|RedirectResponse
    {
        $rules = [
            'name' => ['label' => 'Name is required', 'rules' => 'required|trim'],
        ];

        $model = new FranchisesOwnersModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'         => 'Franchise Owner | Add/Update',
                'form_title'    => 'Franchise Owner | Add/Update',
                'fran_id'       => $franchiseId,
                'specialist_id' => $id,
            ];
            if ($id !== null && $id !== '') {
                $data['franchise_owner_info'] = $model->find((int) $id);
            }

            return $this->renderAdmin('admin/manage_franchises/owners_update', $data);
        }

        $saveData = [
            'franchise_id'  => $franchiseId,
            'name'          => $this->request->getPost('name'),
            'gender'        => $this->request->getPost('gender'),
            'position_name' => $this->request->getPost('position_name'),
            'description'   => $this->request->getPost('description'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'franchise_owners', 2_000_000);
        if ($imageName !== null) {
            $saveData['image'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateOwner((int) $id, $saveData);
        } else {
            if (! isset($saveData['image'])) {
                $saveData['image'] = '';
            }
            $model->addOwner($saveData);
        }

        return redirect()->to(site_url('admin/section/franchise_owners_list/' . $franchiseId));
    }

    public function delete_franchise_owner($franchiseId, $id): RedirectResponse
    {
        $rows = (new FranchisesOwnersModel())->deleteOwner((int) $id);
        foreach ($rows as $row) {
            $this->unlinkUploadFile('franchise_owners', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/franchise_owners_list/' . $franchiseId));
    }

    public function manage_franchiese_testimonials($franId): string
    {
        $data = [
            'title'      => 'Franchise Testimonials| List',
            'form_title' => 'Franchise Testimonials',
            'info'       => (new FranchisesTestimonialsModel())->byFranchiseNewestFirst((int) $franId),
            'fran_id'    => $franId,
        ];

        return $this->renderAdmin('admin/manage_franchises/testimonials_list', $data);
    }

    public function add_franchise_testimonials($franchiseId, $id = null): string|RedirectResponse
    {
        $rules = [
            'name'        => ['label' => 'Name is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new FranchisesTestimonialsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Franchise Testimonials| Add/Update',
                'form_title' => 'Franchise Testimonials | Add/Update',
                'fran_id'    => $franchiseId,
            ];
            if ($id !== null && $id !== '') {
                $data['franchise_info'] = $model->find((int) $id);
            }

            return $this->renderAdmin('admin/manage_franchises/add_testimonials', $data);
        }

        $saveData = [
            'name'         => $this->request->getPost('name'),
            'designation'  => $this->request->getPost('designation'),
            'description'  => $this->request->getPost('description'),
            'franchise_id' => $franchiseId,
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateTestimonial((int) $id, $saveData);
        } else {
            $model->addTestimonial($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_franchiese_testimonials/' . $franchiseId));
    }

    public function delete_franchise_testimonials($franchiseId, $id): RedirectResponse
    {
        (new FranchisesTestimonialsModel())->deleteTestimonial((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_franchiese_testimonials/' . $franchiseId));
    }

    public function change_status_franchise_testimonials($franchiseId, $id, $status): RedirectResponse
    {
        (new FranchisesTestimonialsModel())->setStatus((int) $id, (int) $status, 'is_active');
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_franchiese_testimonials/' . $franchiseId));
    }

    // ------------------------------------------------------------------
    // Corporate Teams
    // ------------------------------------------------------------------

    public function manage_corporate_teams(): string
    {
        $data = [
            'title'      => 'Manage Corporate Teams | List',
            'form_title' => 'Manage Corporate Teams | List',
            'teams_info' => (new CorporateTeamsModel())->listForAdmin(),
        ];

        return $this->renderAdmin('admin/manage_corporate_team/list', $data);
    }

    public function update_corporate_team($id = null): string|RedirectResponse
    {
        $rules = [
            'name'        => ['label' => 'Name is required', 'rules' => 'required|trim'],
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new CorporateTeamsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'         => 'Corporate Team | Add/Update',
                'form_title'    => 'Corporate Team | Add/Update',
                'specialist_id' => $id,
            ];
            if ($id !== null && $id !== '') {
                $data['franchise_info'] = $model->memberById((int) $id);
            }

            return $this->renderAdmin('admin/manage_corporate_team/update', $data);
        }

        $saveData = [
            'name'         => $this->request->getPost('name'),
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'corporate_team');
        if ($imageName !== null) {
            $saveData['image'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateMember((int) $id, $saveData);
        } else {
            $model->addMember($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_corporate_teams/'));
    }

    public function delete_corporate_team($id, $imageId = null): RedirectResponse
    {
        $targetId = (int) ($imageId ?? $id);
        $row      = (new CorporateTeamsModel())->deleteMember($targetId);
        if ($row !== null) {
            $this->unlinkUploadFile('corporate_team', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_corporate_teams/'));
    }

    public function change_status_corporate_team($id, $status): RedirectResponse
    {
        (new CorporateTeamsModel())->setStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/manage_corporate_teams'));
    }

    public function update_order_no_corporate_team(): ResponseInterface
    {
        $memberId = (int) $this->request->getPost('member_id');
        $orderNo  = (int) $this->request->getPost('order_no');
        $updated  = (new CorporateTeamsModel())->updateOrderNo($memberId, $orderNo);

        return $this->response->setContentType('text/plain')->setBody($updated ? 'success' : '');
    }

    // ------------------------------------------------------------------
    // CMS Pages
    // ------------------------------------------------------------------

    public function list_pages(): string
    {
        $data = [
            'title'      => 'Manage Pages | List',
            'form_title' => 'Manage Pages',
            'info'       => (new PagesModel())->listForAdmin(),
        ];

        return $this->renderAdmin('admin/manage_pages/list', $data);
    }

    public function update_page($id = ''): string
    {
        $data = [
            'title'      => 'Manage Page | Update',
            'form_title' => 'Update Page',
            'page_info'  => (new PagesModel())->pageById((int) $id),
        ];

        return $this->renderAdmin('admin/manage_pages/update', $data);
    }

    public function save_page($id = ''): string|RedirectResponse
    {
        $rules = [
            'content' => ['label' => 'Page Content', 'rules' => 'required'],
        ];

        $model = new PagesModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Manage Page | Update',
                'form_title' => 'Update Page',
                'page_info'  => $model->pageById((int) $id),
            ];

            return $this->renderAdmin('admin/manage_pages/update', $data);
        }

        $updateData = [
            'content'          => $this->request->getPost('content'),
            'title'            => $this->request->getPost('title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords'    => $this->request->getPost('meta_keywords'),
        ];

        $name = $this->request->getPost('name');
        if ($name !== null && trim((string) $name) !== '') {
            $updateData['name'] = $name;
        }

        $model->updatePage((int) $id, $updateData);
        session()->setFlashdata('success_msg', 'Updated sccessfully');

        return redirect()->to(site_url('admin/section/list_pages'));
    }

    public function change_status_page($id, $status): RedirectResponse
    {
        (new PagesModel())->setStatus((int) $id, (int) $status);
        session()->setFlashdata('success_msg', 'Updated sccessfully');

        return redirect()->to(site_url('admin/section/list_pages'));
    }

    public function delete_page($id): RedirectResponse
    {
        (new PagesModel())->deletePage((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/list_pages'));
    }

    // ------------------------------------------------------------------
    // Videos (Finding Home, Selling Home, Testimonial, CB India, Featured, Success Story)
    // ------------------------------------------------------------------

    public function finding_home_videos(): string
    {
        $data = [
            'title'      => 'Manage Videos | List',
            'form_title' => 'Manage Finding A Home Videos',
            'info'       => (new FindingHomeVideosModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/finding_home_videos/list', $data);
    }

    public function add_home_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new FindingHomeVideosModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Finding A Home Videos | Add/Update',
                'form_title' => 'Finding A Home Videos | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['finding_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/finding_home_videos/add', $data);
        }

        $saveData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'video_url'   => $this->request->getPost('url'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/finding_home_videos'));
    }

    public function delete_home_videos($id): RedirectResponse
    {
        (new FindingHomeVideosModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/finding_home_videos'));
    }

    public function change_status_finding_home_videos($id, $status): RedirectResponse
    {
        (new FindingHomeVideosModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/finding_home_videos'));
    }

    public function selling_home_videos(): string
    {
        $data = [
            'title'      => 'Manage Selling A Home Videos | List',
            'form_title' => 'Manage Selling A Home Videos',
            'info'       => (new SellingHomeVideosModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/selling_home_videos/list', $data);
    }

    public function add_selling_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new SellingHomeVideosModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Selling A Home Videos | Add/Update',
                'form_title' => 'Selling A Home Videos | Add/Update',
                'cities'     => (new CitiesModel())->listNewestFirst(),
            ];
            if ($id !== null && $id !== '') {
                $data['selling_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/selling_home_videos/add', $data);
        }

        $saveData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'video_url'   => $this->request->getPost('url'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/selling_home_videos'));
    }

    public function delete_selling_videos($id): RedirectResponse
    {
        (new SellingHomeVideosModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/selling_home_videos'));
    }

    public function change_status_selling_videos($id, $status): RedirectResponse
    {
        (new SellingHomeVideosModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/selling_home_videos'));
    }

    public function testimonial_videos(): string
    {
        $data = [
            'title'      => 'Manage Testimonial Videos | List',
            'form_title' => 'Manage Testimonial Videos',
            'info'       => (new TestimonialVideosModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/testimonial_videos/list', $data);
    }

    public function add_testimonial_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new TestimonialVideosModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Testimonial Videos | Add/Update',
                'form_title' => 'Testimonial Videos | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['selling_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/testimonial_videos/add', $data);
        }

        $saveData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'video_url'   => $this->request->getPost('url'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/testimonial_videos'));
    }

    public function change_status_testimonial_videos($id, $status): RedirectResponse
    {
        (new TestimonialVideosModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/testimonial_videos'));
    }

    public function delete_testimonial_videos($id): RedirectResponse
    {
        (new TestimonialVideosModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/testimonial_videos'));
    }

    public function cb_india_videos(): string
    {
        $data = [
            'title'      => 'Manage Coldwell Banker India Videos | List',
            'form_title' => 'Manage Coldwell Banker India Videos',
            'info'       => (new CbIndiaVideosModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/cb_india_videos/list', $data);
    }

    public function add_cb_india_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new CbIndiaVideosModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker India Videos | Add/Update',
                'form_title' => 'Coldwell Banker India Videos | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['selling_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/cb_india_videos/add', $data);
        }

        $saveData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'video_url'   => $this->request->getPost('url'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/cb_india_videos'));
    }

    public function change_status_cb_india_videos($id, $status): RedirectResponse
    {
        (new CbIndiaVideosModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/cb_india_videos'));
    }

    public function delete_cb_india_videos($id): RedirectResponse
    {
        (new CbIndiaVideosModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/cb_india_videos'));
    }

    public function featured_videos(): string
    {
        $data = [
            'title'      => 'Manage Featured Videos | List',
            'form_title' => 'Manage Featured Videos',
            'info'       => (new FeaturedVideosModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/featured_videos/list', $data);
    }

    public function add_featured_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new FeaturedVideosModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Featured Videos | Add/Update',
                'form_title' => 'Featured Videos | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['selling_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/featured_videos/add', $data);
        }

        $saveData = [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'video_url'   => $this->request->getPost('url'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/featured_videos'));
    }

    public function change_status_featured_videos($id, $status): RedirectResponse
    {
        (new FeaturedVideosModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/featured_videos'));
    }

    public function delete_featured_videos($id): RedirectResponse
    {
        (new FeaturedVideosModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/featured_videos'));
    }

    public function success_stories_videos(): string
    {
        $data = [
            'title'      => 'Success Story Videos| List',
            'form_title' => 'Success Story Videos',
            'info'       => (new SuccessStoryModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/success_story_videos/list', $data);
    }

    public function success_story_videos($id = null): string|RedirectResponse
    {
        return $this->add_success_story_videos($id);
    }

    public function add_success_story_videos($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
            'url'         => ['label' => 'URL is required', 'rules' => 'required|trim'],
        ];

        $model = new SuccessStoryModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Success Story Videos| Add/Update',
                'form_title' => 'Success Story Videos | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['selling_home_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/success_story_videos/add', $data);
        }

        $saveData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'video_url'    => $this->request->getPost('url'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateVideo((int) $id, $saveData);
        } else {
            $model->addVideo($saveData);
        }

        return redirect()->to(site_url('admin/section/success_stories_videos'));
    }

    public function delete_success_story_videos($id): RedirectResponse
    {
        (new SuccessStoryModel())->deleteVideo((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/success_stories_videos'));
    }

    public function change_status_success_story($id, $status): RedirectResponse
    {
        (new SuccessStoryModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/success_stories_videos'));
    }

    // ------------------------------------------------------------------
    // Jobs & Job Applications & Careers Testimonials & Life at CB
    // ------------------------------------------------------------------

    public function job_openings_list(): string
    {
        $data = [
            'title'      => 'Manage Jobs Openings | List',
            'form_title' => 'Manage Jobs Openings',
            'info'       => (new JobsModel())->listAll(),
        ];

        return $this->renderAdmin('admin/manage_job_openings/list', $data);
    }

    public function update_job_opening($id = null): string|RedirectResponse
    {
        return $this->add_job_opening($id);
    }

    public function save_job($id = null): string|RedirectResponse
    {
        return $this->add_job_opening($id);
    }

    public function add_job_opening($id = null): string|RedirectResponse
    {
        $rules = [
            'type'           => ['label' => 'Job Type is required', 'rules' => 'required'],
            'position_name'  => ['label' => 'Position Name is required', 'rules' => 'required|trim'],
            'qualification'  => ['label' => 'Minimum Qualification is required', 'rules' => 'required|trim'],
            'experience'     => ['label' => 'Max Experience is required', 'rules' => 'required'],
            'min_experience' => ['label' => 'Min Experience is required', 'rules' => 'required'],
            'location'       => ['label' => 'Job Location is required', 'rules' => 'required|trim'],
            'description'    => ['label' => 'Job Description is required', 'rules' => 'required|trim'],
        ];

        $model = new JobsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Manage Jobs Openings | Add',
                'form_title' => 'Jobs Opening | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['job_info'] = $model->findJob((int) $id);
            }

            return $this->renderAdmin('admin/manage_job_openings/update', $data);
        }

        $positionName = (string) $this->request->getPost('position_name');
        $saveData     = [
            'type'           => $this->request->getPost('type'),
            'position'       => $positionName,
            'qualification'  => $this->request->getPost('qualification'),
            'max_experience' => $this->request->getPost('experience') ?? $this->request->getPost('max_experience'),
            'min_experience' => $this->request->getPost('min_experience'),
            'location'       => $this->request->getPost('location'),
            'description'    => $this->request->getPost('description'),
            'created_date'   => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateJob((int) $id, $saveData);
            session()->setFlashdata('success_msg', 'Updated sccessfully');

            return redirect()->to(site_url('admin/section/job_openings_list/'));
        }

        if ($model->findIdByPosition($positionName) !== null) {
            session()->setFlashdata('error_msg', 'Duplicate Entry');

            return redirect()->to(site_url('admin/section/update_job_opening/'));
        }

        $model->createJob($saveData);
        session()->setFlashdata('success_msg', 'Added sccessfully');

        return redirect()->to(site_url('admin/section/job_openings_list/'));
    }

    public function delete_job_opening($id): RedirectResponse
    {
        (new JobsModel())->deleteJob((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/job_openings_list'));
    }

    public function change_status_job_opening($id, $status): RedirectResponse
    {
        (new JobsModel())->setStatus((int) $id, (int) $status);
        session()->setFlashdata('success_msg', 'Updated sccessfully');

        return redirect()->to(site_url('admin/section/job_openings_list'));
    }

    public function job_application_list(): string
    {
        $data = [
            'title'      => 'Manage Jobs Application | List',
            'form_title' => 'Manage Jobs Application',
            'info'       => (new JobApplicationsModel())->listWithPosition(),
        ];

        return $this->renderAdmin('admin/manage_job_applications/list', $data);
    }

    public function check_details_candidate($id): string
    {
        return $this->job_candidate_details($id);
    }

    public function job_candidate_details($id): string
    {
        $data = [
            'title'      => 'Manage Candidate | Details',
            'form_title' => 'Manage Candidate',
            'info'       => (new JobApplicationsModel())->detailsWithPosition((int) $id),
        ];

        return $this->renderAdmin('admin/manage_job_applications/details', $data);
    }

    /**
     * ADM-04: Authenticated resume download streamed through AdminAuth-protected controller.
     * Direct HTTP access to /uploads/resume/* is blocked by public/uploads/resume/.htaccess.
     */
    public function get_candidate_resume($id): DownloadResponse
    {
        $row = (new JobApplicationsModel())->findApplication((int) $id);
        if ($row === null || empty($row['resume'])) {
            throw PageNotFoundException::forPageNotFound();
        }

        $safeName = basename((string) $row['resume']);
        $privateDir = rtrim(WRITEPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resume';
        if (! is_dir($privateDir)) {
            mkdir($privateDir, 0700, true);
        }
        $filePath = $privateDir . DIRECTORY_SEPARATOR . $safeName;
        if (! is_file($filePath)) {
            $legacyPath = rtrim(FCPATH, '\\/') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'resume' . DIRECTORY_SEPARATOR . $safeName;
            if (is_file($legacyPath) && ! @rename($legacyPath, $filePath)) {
                throw PageNotFoundException::forPageNotFound();
            }
        }
        if (! is_file($filePath)) {
            throw PageNotFoundException::forPageNotFound();
        }

        $ext      = pathinfo($safeName, PATHINFO_EXTENSION);
        $downName = 'Resume' . ($ext !== '' ? '.' . $ext : '');

        return $this->response->download($filePath, null)->setFileName($downName);
    }

    public function manage_testimonials(): string
    {
        $data = [
            'title'      => 'Careers Testimonials | List',
            'form_title' => 'Manage Careers Testimonials',
            'info'       => (new CareersTestimonialsModel())->allLatestFirst(),
        ];

        return $this->renderAdmin('admin/careers_testimonials/list', $data);
    }

    public function add_carrer_testimonial($id = null): string|RedirectResponse
    {
        $rules = [
            'title'         => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'name'          => ['label' => 'Name is required', 'rules' => 'required|trim'],
            'position_name' => ['label' => 'Position is required', 'rules' => 'required|trim'],
        ];

        $model = new CareersTestimonialsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Careers Testimonials | Add/Update',
                'form_title' => 'Careers Testimonials | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['image_info'] = $model->byId((int) $id);
            }

            return $this->renderAdmin('admin/careers_testimonials/image_add', $data);
        }

        $saveData = [
            'name'         => $this->request->getPost('name'),
            'position'     => $this->request->getPost('position_name'),
            'title'        => $this->request->getPost('title'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'carrers_testimonial', 2_000_000);
        if ($imageName !== null) {
            $saveData['image_name'] = $imageName;
        }

        if ($id !== null && $id !== '') {
            $model->updateTestimonial((int) $id, $saveData);
        } else {
            if (! isset($saveData['image_name'])) {
                $saveData['image_name'] = '';
            }
            $model->addTestimonial($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_testimonials'));
    }

    public function change_status_carrers_testimonial($id, $status): RedirectResponse
    {
        (new CareersTestimonialsModel())->changeStatus((int) $id, (int) $status);

        return redirect()->to(site_url('admin/section/manage_testimonials'));
    }

    public function delete_carrer_testimonial($id): RedirectResponse
    {
        $row = (new CareersTestimonialsModel())->deleteTestimonial((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('carrers_testimonial', $row['image_name'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_testimonials'));
    }

    public function life_at_coldwell_banker(): string
    {
        $data = [
            'title'      => 'Life At Coldwell Banker | List',
            'form_title' => 'Manage Life At Coldwell Banker',
            'info'       => (new LifeAtColdwellBankerModel())->entryList(),
        ];

        return $this->renderAdmin('admin/life_at_coldwell_banker/list', $data);
    }

    public function add_life_at_coldwell_banker($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new LifeAtColdwellBankerModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Life At Coldwell Banker | Add/Update',
                'form_title' => 'Life At Coldwell Banker | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['info'] = $model->findEntry((int) $id);
            }

            return $this->renderAdmin('admin/life_at_coldwell_banker/add', $data);
        }

        $saveData = [
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        if ($id !== null && $id !== '') {
            $model->updateEntry((int) $id, $saveData);
        }

        return redirect()->to(site_url('admin/section/life_at_coldwell_banker'));
    }

    public function life_at_coldwell_banker_images(): string
    {
        return $this->add_images_coldwell_banker();
    }

    public function add_images_coldwell_banker(): string
    {
        $data = [
            'title'      => 'Life At Coldwell Banker | Images List',
            'form_title' => 'Manage Life At Coldwell Banker Images',
            'info'       => (new LifeAtColdwellBankerImagesModel())->imageList(),
        ];

        return $this->renderAdmin('admin/life_at_coldwell_banker/images_list', $data);
    }

    public function add_life_at_coldwell_banker_image($id = null): string|RedirectResponse
    {
        $model = new LifeAtColdwellBankerImagesModel();

        if ($this->request->is('post')) {
            $filename = $this->storeUploadedImage('image', 'life_at_coldwell_banker', 2_000_000, true);
            if ($filename !== null) {
                if ($id !== null && $id !== '') {
                    $model->updateImage((int) $id, $filename);
                } else {
                    $model->addImage($filename);
                }
            }

            return redirect()->to(site_url('admin/section/add_images_coldwell_banker'));
        }

        $data = [
            'title'      => 'Life At Coldwell Banker | Add/Update',
            'form_title' => 'Life At Coldwell Banker | Add/Update',
        ];
        if ($id !== null && $id !== '') {
            $data['image_info'] = $model->findImage((int) $id);
        }

        return $this->renderAdmin('admin/life_at_coldwell_banker/image_add', $data);
    }

    public function delete_image_life_at_coldwell($id): RedirectResponse
    {
        $row = (new LifeAtColdwellBankerImagesModel())->deleteImage((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('life_at_coldwell_banker', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/add_images_coldwell_banker'));
    }

    // ------------------------------------------------------------------
    // Media & Research (Research, News, Industry News, Knowledge Series, Newsletter, Awards)
    // ------------------------------------------------------------------

    public function manage_research(): string
    {
        $data = [
            'title'      => 'Research | List',
            'form_title' => 'Manage Research',
            'info'       => (new ResearchModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/research/list', $data);
    }

    public function add_research($id = null): string|RedirectResponse
    {
        $rules = [
            'type'        => ['label' => 'Research Type is required', 'rules' => 'required'],
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new ResearchModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Research | Add/Update',
                'form_title' => 'Research | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['research_info'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/research/add', $data);
        }

        $saveData = [
            'type'         => $this->request->getPost('type'),
            'title'        => $this->request->getPost('title'),
            'description'  => $this->request->getPost('description'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'research', 5_000_000);
        $pdfName   = $this->storeUploadedPdf('documents', 'research', 5_000_000);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName, $pdfName);
        } else {
            if ($imageName !== null) {
                $saveData['image_name'] = $imageName;
            }
            if ($pdfName !== null) {
                $saveData['pdf_name'] = $pdfName;
            }
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/manage_research'));
    }

    public function change_status_research($id, $status): RedirectResponse
    {
        (new ResearchModel())->setStatus((int) $id, (int) $status, 'is_active');

        return redirect()->to(site_url('admin/section/manage_research'));
    }

    public function delete_research($id): RedirectResponse
    {
        $row = (new ResearchModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('research', $row['image_name'] ?? null);
            $this->unlinkUploadFile('research', $row['pdf_name'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/manage_research'));
    }

    public function coldwell_banker_news(): string
    {
        $data = [
            'title'      => 'Coldwell Banker News | List',
            'form_title' => 'Manage Coldwell Banker News',
            'info'       => (new ColdwellBankerNewsModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/coldwell_banker_news/list', $data);
    }

    public function add_coldwell_banker_news($id = null): string|RedirectResponse
    {
        $rules = [
            'title'     => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'news_desc' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new ColdwellBankerNewsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker News | Add/Update',
                'form_title' => 'Coldwell Banker News | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['news_info'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/coldwell_banker_news/add', $data);
        }

        $saveData = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('news_desc'),
            'external_link' => $this->request->getPost('external_link'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'news', 2_000_000, true);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName);
        } elseif ($imageName !== null) {
            $saveData['image'] = $imageName;
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/coldwell_banker_news'));
    }

    public function delete_coldwell_banker_news($id): RedirectResponse
    {
        $row = (new ColdwellBankerNewsModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('news', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/coldwell_banker_news'));
    }

    public function coldwell_banker_industry_news(): string
    {
        $data = [
            'title'      => 'Coldwell Banker Industry News | List',
            'form_title' => 'Manage Coldwell Banker Industry News',
            'info'       => (new ColdwellBankerIndustryNewsModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/coldwell_banker_industry_news/list', $data);
    }

    public function add_coldwell_banker_industry_news($id = null): string|RedirectResponse
    {
        $rules = [
            'title'     => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'news_desc' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new ColdwellBankerIndustryNewsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker Industry News | Add/Update',
                'form_title' => 'Coldwell Banker Industry News | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['news_industry_info'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/coldwell_banker_industry_news/add', $data);
        }

        $saveData = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('news_desc'),
            'external_link' => $this->request->getPost('external_link'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'industry_news', 2_000_000, true);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName);
        } elseif ($imageName !== null) {
            $saveData['image'] = $imageName;
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/coldwell_banker_industry_news'));
    }

    public function delete_coldwell_banker_industry_news($id): RedirectResponse
    {
        $row = (new ColdwellBankerIndustryNewsModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('industry_news', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/coldwell_banker_industry_news'));
    }

    public function coldwell_banker_knowledge_series(): string
    {
        $data = [
            'title'      => 'Coldwell Banker Knowledge Series | List',
            'form_title' => 'Manage Coldwell Banker Knowledge Series',
            'info'       => (new ColdwellBankerKnowledgeSeriesModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/coldwell_banker_knowledge_series/list', $data);
    }

    public function add_coldwell_banker_knowledge_series($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new ColdwellBankerKnowledgeSeriesModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker Knowledge Series | Add/Update',
                'form_title' => 'Coldwell Banker Knowledge Series | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['news_knowledge_series'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/coldwell_banker_knowledge_series/add', $data);
        }

        $saveData = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'external_link' => $this->request->getPost('external_link'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'knowledge_series', 2_000_000, true);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName);
        } elseif ($imageName !== null) {
            $saveData['image'] = $imageName;
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/coldwell_banker_knowledge_series'));
    }

    public function delete_coldwell_banker_knowledge_series($id): RedirectResponse
    {
        $row = (new ColdwellBankerKnowledgeSeriesModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('knowledge_series', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/coldwell_banker_knowledge_series'));
    }

    public function coldwell_banker_newsletter(): string
    {
        $data = [
            'title'      => 'Coldwell Banker Newsletter | List',
            'form_title' => 'Manage Coldwell Banker Newsletter',
            'info'       => (new ColdwellBankerNewsletterModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/coldwell_banker_newsletter/list', $data);
    }

    public function add_coldwell_banker_newsletter($id = null): string|RedirectResponse
    {
        $rules = [
            'title'       => ['label' => 'Title is required', 'rules' => 'required|trim'],
            'description' => ['label' => 'Description is required', 'rules' => 'required|trim'],
        ];

        $model = new ColdwellBankerNewsletterModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker Newsletter | Add/Update',
                'form_title' => 'Coldwell Banker Newsletter | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['news_knowledge_series'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/coldwell_banker_newsletter/add', $data);
        }

        $saveData = [
            'title'         => $this->request->getPost('title'),
            'description'   => $this->request->getPost('description'),
            'external_link' => $this->request->getPost('external_link'),
            'created_date'  => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'newsletter', 2_000_000, true);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName);
        } elseif ($imageName !== null) {
            $saveData['image'] = $imageName;
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/coldwell_banker_newsletter'));
    }

    public function delete_coldwell_banker_newsletter($id): RedirectResponse
    {
        $row = (new ColdwellBankerNewsletterModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('newsletter', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/coldwell_banker_newsletter'));
    }

    public function coldwell_banker_awards(): string
    {
        $data = [
            'title'      => 'Coldwell Banker Awards | List',
            'form_title' => 'Manage Coldwell Banker Awards',
            'info'       => (new AwardsModel())->listNewestFirst(),
        ];

        return $this->renderAdmin('admin/coldwell_banker_awards/list', $data);
    }

    public function add_coldwell_banker_award($id = null): string|RedirectResponse
    {
        $rules = [
            'title' => ['label' => 'Title is required', 'rules' => 'required|trim'],
        ];

        $model = new AwardsModel();

        if (! $this->request->is('post') || ! $this->validate($rules)) {
            $data = [
                'title'      => 'Coldwell Banker Awards | Add/Update',
                'form_title' => 'Coldwell Banker Awards | Add/Update',
            ];
            if ($id !== null && $id !== '') {
                $data['awards_info'] = $model->findById((int) $id);
            }

            return $this->renderAdmin('admin/coldwell_banker_awards/add', $data);
        }

        $saveData = [
            'name'         => $this->request->getPost('title'),
            'created_date' => date('Y-m-d H:i:s'),
        ];

        $imageName = $this->storeUploadedImage('image', 'awards', 2_000_000, true);

        if ($id !== null && $id !== '') {
            $model->updateItem((int) $id, $saveData, $imageName);
        } elseif ($imageName !== null) {
            $saveData['image'] = $imageName;
            $model->createItem($saveData);
        }

        return redirect()->to(site_url('admin/section/coldwell_banker_awards'));
    }

    public function delete_coldwell_banker_award($id): RedirectResponse
    {
        $row = (new AwardsModel())->deleteItem((int) $id);
        if ($row !== null) {
            $this->unlinkUploadFile('awards', $row['image'] ?? null);
        }

        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/coldwell_banker_awards'));
    }

    // ------------------------------------------------------------------
    // AJAX MIME check endpoint (ADM-02)
    // ------------------------------------------------------------------

    public function check_mime(): ResponseInterface
    {
        $type = strtolower(trim((string) $this->request->getPost('filetype')));
        $file = $this->request->getFile('image');

        if (! $file instanceof UploadedFile || ! $file->isValid()) {
            return $this->response->setContentType('text/plain')->setBody('2');
        }

        $mime = strtolower((string) $file->getMimeType());

        if ($type === 'pdf') {
            $ok = ($mime === 'application/pdf');
        } else {
            // Default to image MIME check when filetype is 'image' or omitted
            $ok = isset(self::IMAGE_MIMES[$mime]);
        }

        return $this->response->setContentType('text/plain')->setBody($ok ? '1' : '2');
    }

    // ------------------------------------------------------------------
    // Enquiries, Subscriptions & Excel Exports (LC-09)
    // ------------------------------------------------------------------

    public function projects_contact_enquiry(): string
    {
        $data = [
            'title'      => 'Manage Projects Contact Enquiry | List',
            'form_title' => 'Manage Projects Contact Enquiry',
            'info'       => (new ContactEnquiryModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/projects_contact_enquiry/list', $data);
    }

    public function franchisee_enquiry(): string
    {
        $data = [
            'title'      => 'Manage Franchisee Enquiry | List',
            'form_title' => 'List',
            'info'       => (new FranchiseeEnquiryModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/franchisee_enquiry/list', $data);
    }

    public function delete_franchisee($id): RedirectResponse
    {
        (new FranchiseeEnquiryModel())->deleteById((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/franchisee_enquiry'));
    }

    public function contact_enquiry(): string
    {
        $data = [
            'title'      => 'Manage Contact Enquiry | List',
            'form_title' => 'List',
            'info'       => (new ContactEnquiryModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/contact_enquiry/list', $data);
    }

    public function delete_contact($id): RedirectResponse
    {
        (new ContactEnquiryModel())->deleteById((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/contact_enquiry'));
    }

    public function franchisee_project_enquiries(): string
    {
        $data = [
            'title'      => 'Manage Franchisee Project Enquiries | List',
            'form_title' => 'Franchisee Project Enquiries | List',
            'info'       => (new FranchiseeProjectEnquiryModel())->allWithFranchiseName(),
        ];

        return $this->renderAdmin('admin/franchisee_project_enquiries/list', $data);
    }

    public function delete_franchisee_project_enquiry($id): RedirectResponse
    {
        (new FranchiseeProjectEnquiryModel())->deleteById((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/franchisee_project_enquiries/'));
    }

    public function newsletter_list(): string
    {
        $data = [
            'title'      => 'Newsletter | List',
            'form_title' => 'Newsletter | List',
            'info'       => (new NewsletterSubscriptionsModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/newsletters/list', $data);
    }

    public function delete_newslettes_subscriber($id): RedirectResponse
    {
        (new NewsletterSubscriptionsModel())->deleteById((int) $id);
        session()->setFlashdata('success_msg', 'Deleted sccessfully');

        return redirect()->to(site_url('admin/section/newsletter_list'));
    }

    public function nri_enquiry(): string
    {
        $data = [
            'title'      => 'Manage Contact Enquiry | List',
            'form_title' => 'List',
            'info'       => (new NriEnquiryModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/nri_enquiry/list', $data);
    }

    public function nri_enquiry_list(): string
    {
        $data = [
            'title'      => 'Manage NRI Enquiry | List',
            'form_title' => 'List',
            'info'       => (new NriEnquiryModel())->allNewestFirst(),
        ];

        return $this->renderAdmin('admin/nri_enquiry_list/list', $data);
    }

    /**
     * Parse daterangepicker string "MM/DD/YYYY - MM/DD/YYYY" into ['YYYY-MM-DD', 'YYYY-MM-DD'].
     *
     * @return array{0: string, 1: string}|null
     */
    private function parseReservationDateRange(): ?array
    {
        $reservation = $this->request->getPost('reservation');
        if (! is_string($reservation) || ! str_contains($reservation, '-')) {
            return null;
        }

        $parts = explode('-', $reservation);
        $start = explode('/', trim($parts[0] ?? ''));
        $end   = explode('/', trim($parts[1] ?? ''));
        if (count($start) !== 3 || count($end) !== 3) {
            return null;
        }

        return [
            trim($start[2]) . '-' . trim($start[0]) . '-' . trim($start[1]),
            trim($end[2]) . '-' . trim($end[0]) . '-' . trim($end[1]),
        ];
    }

    /**
     * Stream a PhpSpreadsheet instance as a BIFF8 .xls download.
     */
    private function streamXlsDownload(Spreadsheet $spreadsheet, string $filename): ResponseInterface
    {
        $writer = new Xls($spreadsheet);
        ob_start();
        $writer->save('php://output');
        $binary = (string) ob_get_clean();

        return $this->response
            ->setStatusCode(200)
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setBody($binary);
    }

    /**
     * Contact enquiries Excel export (LC-09: PhpSpreadsheet replacing PHPExcel).
     */
    public function export_to_excell($string = null): ResponseInterface|RedirectResponse
    {
        ini_set('memory_limit', '1024M');

        $model = new ContactEnquiryModel();
        if ($string !== null) {
            $leads = $model->allNewestFirst();
        } else {
            $range = $this->parseReservationDateRange();
            $leads = $range !== null ? $model->byCreatedDateRange($range[0], $range[1]) : [];
        }

        if (empty($leads)) {
            session()->setFlashdata('error', 'No data found.');

            return redirect()->to(site_url('admin/section/contact_enquiry'));
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Leads');
        $sheet->setCellValue('A2', 'Name');
        $sheet->setCellValue('B2', 'Email');
        $sheet->setCellValue('C2', 'Phone');
        $sheet->setCellValue('D2', 'Country');
        $sheet->setCellValue('E2', 'City');
        $sheet->setCellValue('F2', 'Message');
        $sheet->setCellValue('G2', 'Created On');

        $i = 3;
        foreach ($leads as $lead) {
            $sheet->setCellValue('A' . $i, $lead['name'] ?? '');
            $sheet->setCellValue('B' . $i, $lead['email'] ?? '');
            $sheet->setCellValue('C' . $i, $lead['phone'] ?? '');
            $sheet->setCellValue('D' . $i, $lead['country'] ?? '');
            $sheet->setCellValue('E' . $i, $lead['city'] ?? '');
            $sheet->setCellValue('F' . $i, $lead['message'] ?? '');
            $sheet->setCellValue('G' . $i, $lead['created_on'] ?? '');
            $i++;
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'BFBFBFBF'],
            ],
        ]);
        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'EAEAEAEA'],
            ],
        ]);

        $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(12)->setBold(true);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
            $sheet->getStyle($col . '2')->getFont()->setSize(11)->setBold(true);
        }

        $sheet->setTitle('Leads');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamXlsDownload($spreadsheet, 'Leads.xls');
    }

    /**
     * Franchisee enquiries Excel export (LC-09: PhpSpreadsheet replacing PHPExcel).
     */
    public function export_franchisee_enquiries_to_excell($string = null): ResponseInterface|RedirectResponse
    {
        $model = new FranchiseeEnquiryModel();
        if ($string !== null) {
            $leads = $model->allNewestFirst();
        } else {
            $range = $this->parseReservationDateRange();
            $leads = $range !== null ? $model->byCreatedDateRange($range[0], $range[1]) : [];
        }

        if (empty($leads)) {
            session()->setFlashdata('error', 'No data found.');

            return redirect()->to(site_url('admin/section/franchisee_enquiry'));
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Leads');
        $sheet->setCellValue('A2', 'Name');
        $sheet->setCellValue('B2', 'Email');
        $sheet->setCellValue('C2', 'Phone');
        $sheet->setCellValue('D2', 'Country');
        $sheet->setCellValue('E2', 'City');
        $sheet->setCellValue('F2', 'Message');
        $sheet->setCellValue('G2', 'Created On');

        $i = 3;
        foreach ($leads as $lead) {
            $sheet->setCellValue('A' . $i, $lead['name'] ?? '');
            $sheet->setCellValue('B' . $i, $lead['email'] ?? '');
            $sheet->setCellValue('C' . $i, $lead['phone'] ?? '');
            $sheet->setCellValue('D' . $i, $lead['country'] ?? '');
            $sheet->setCellValue('E' . $i, $lead['city'] ?? '');
            $sheet->setCellValue('F' . $i, $lead['message'] ?? '');
            $sheet->setCellValue('G' . $i, $lead['created_on'] ?? '');
            $i++;
        }

        $sheet->getStyle('A1:G1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'BFBFBFBF'],
            ],
        ]);
        $sheet->getStyle('A2:G2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'EAEAEAEA'],
            ],
        ]);

        $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(12)->setBold(true);

        foreach (['A', 'B', 'C', 'D', 'E', 'F', 'G'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
            $sheet->getStyle($col . '2')->getFont()->setSize(11)->setBold(true);
        }

        $sheet->setTitle('Leads');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamXlsDownload($spreadsheet, 'franchisee_enquiry.xls');
    }

    /**
     * Franchisee project enquiries Excel export (LC-09: PhpSpreadsheet replacing PHPExcel).
     */
    public function export_franchisee_project_enquiries_to_excell($string = null): ResponseInterface|RedirectResponse
    {
        $model = new FranchiseeProjectEnquiryModel();
        if ($string !== null) {
            $leads = $model->allWithFranchiseName();
        } else {
            $range = $this->parseReservationDateRange();
            $leads = $range !== null ? $model->byCreatedDateRangeWithFranchiseName($range[0], $range[1]) : [];
        }

        if (empty($leads)) {
            session()->setFlashdata('error', 'No data found.');

            return redirect()->to(site_url('admin/section/franchisee_enquiry'));
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $sheet = $spreadsheet->setActiveSheetIndex(0);
        $sheet->setCellValue('A1', 'Leads');
        $sheet->setCellValue('A2', 'Name');
        $sheet->setCellValue('B2', 'Email Id');
        $sheet->setCellValue('C2', 'Phone No');
        $sheet->setCellValue('D2', 'Franchise Name');
        $sheet->setCellValue('E2', 'Interested In');
        $sheet->setCellValue('F2', 'Created On');

        $i = 3;
        foreach ($leads as $lead) {
            $sheet->setCellValue('A' . $i, $lead['name'] ?? '');
            $sheet->setCellValue('B' . $i, $lead['email_id'] ?? '');
            $sheet->setCellValue('C' . $i, $lead['phone'] ?? '');
            $sheet->setCellValue('D' . $i, $lead['franchise_name'] ?? '');
            $sheet->setCellValue('E' . $i, $lead['interested_in'] ?? '');
            $sheet->setCellValue('F' . $i, $lead['created_date'] ?? '');
            $i++;
        }

        $sheet->getStyle('A1:F1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'BFBFBFBF'],
            ],
        ]);
        $sheet->getStyle('A2:F2')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color'    => ['argb' => 'EAEAEAEA'],
            ],
        ]);

        $sheet->getStyle('A1')->getFont()->getColor()->setARGB(Color::COLOR_BLACK);
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(12)->setBold(true);

        foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
            $sheet->getColumnDimension($col)->setWidth(25);
            $sheet->getStyle($col . '2')->getFont()->setSize(11)->setBold(true);
        }

        $sheet->setTitle('Leads');
        $spreadsheet->setActiveSheetIndex(0);

        return $this->streamXlsDownload($spreadsheet, 'franchisee_project_enquiries.xls');
    }
}
