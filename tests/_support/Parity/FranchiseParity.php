<?php

namespace Tests\Support\Parity;

use App\Models\CitiesModel;
use App\Models\FranchisesModel;
use App\Models\FranchisesOwnersModel;
use App\Models\FranchisesTestimonialsModel;
use App\Models\RealEstateSpecialistModel;

/**
 * Parity checks for the franchise domain (franchises, cities, franchise owners,
 * franchise testimonials, real estate specialists).
 *
 * Sample ids come from the staging copy: franchise 14 (owners + specialists +
 * testimonials), 8 (testimonials), 15 (no owner), 16 (four specialists),
 * city 5 (largest city), 999999 (missing id).
 */
class FranchiseParity implements ParitySuite
{
    public function domain(): string
    {
        return 'franchise';
    }

    public function checks(): array
    {
        $franchises   = new FranchisesModel();
        $cities       = new CitiesModel();
        $owners       = new FranchisesOwnersModel();
        $testimonials = new FranchisesTestimonialsModel();
        $specialists  = new RealEstateSpecialistModel();

        $checks = [
            // ---- cities ----------------------------------------------------
            [
                'label'   => 'home_model::get_cities_list (home_model.php:9)',
                'old_sql' => 'SELECT * FROM `cities` ORDER BY `id` DESC',
                'new'     => static fn () => $cities->listNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_cities_list (home_model.php:1689)',
                'old_sql' => 'SELECT * FROM `cities` ORDER BY `id` DESC',
                'new'     => static fn () => $cities->listNewestFirst(),
            ],
            [
                'label'   => 'home_model::show_city_by_id(5) (home_model.php:1702)',
                'old_sql' => "SELECT * FROM `cities` WHERE `id` = '5'",
                'new'     => static fn () => $cities->find(5),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_city_by_id(999999) missing (home_model.php:1702)',
                'old_sql' => "SELECT * FROM `cities` WHERE `id` = '999999'",
                'new'     => static fn () => $cities->find(999999),
                'compare' => 'row',
            ],

            // ---- franchises (admin) ---------------------------------------
            [
                'label'   => 'home_model::show_franchises_list (home_model.php:177)',
                'old_sql' => 'SELECT `f`.*, `c`.`city_name` FROM `franchises` `f` JOIN `cities` `c` ON `f`.`city` = `c`.`id` ORDER BY `f`.`id` DESC',
                'new'     => static fn () => $franchises->listWithCity(),
            ],
            [
                'label'   => 'home_model::show_franchise_by_id(14) (home_model.php:264)',
                'old_sql' => "SELECT `f`.*, `c`.`city_name` FROM `franchises` `f` JOIN `cities` `c` ON `f`.`city` = `c`.`id` WHERE `f`.`id` = '14'",
                'new'     => static fn () => $franchises->findWithCity(14),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_franchise_by_id(999999) missing (home_model.php:264)',
                'old_sql' => "SELECT `f`.*, `c`.`city_name` FROM `franchises` `f` JOIN `cities` `c` ON `f`.`city` = `c`.`id` WHERE `f`.`id` = '999999'",
                'new'     => static fn () => $franchises->findWithCity(999999),
                'compare' => 'row',
            ],

            // ---- specialists (admin + AJAX) -------------------------------
            [
                'label'   => 'home_model::show_specialist_by_id(17) (home_model.php:274)',
                'old_sql' => "SELECT * FROM `real_estate_specialist` WHERE `id` = '17'",
                'new'     => static fn () => $specialists->find(17),
                'compare' => 'row',
            ],
            [
                'label'   => 'home_model::show_specialist_list(16) (home_model.php:299)',
                'old_sql' => "SELECT `r`.*, `f`.`title` as `franchise` FROM `real_estate_specialist` `r` JOIN `franchises` `f` ON `f`.`id` = `r`.`franchise_id` WHERE `f`.`id` = '16'",
                'new'     => static fn () => $specialists->listWithFranchiseTitle(16),
                'compare' => 'set',
            ],
            [
                'label'   => 'Ui_model::get_specialist_info(22) (Ui_model.php:291)',
                'old_sql' => "SELECT * FROM `real_estate_specialist` WHERE `id` = '22'",
                'new'     => static fn () => $specialists->find(22),
                'compare' => 'row',
            ],

            // ---- owners (admin + AJAX) ------------------------------------
            [
                'label'   => 'home_model::show_franchise_owners_list(14) (home_model.php:1833)',
                'old_sql' => "SELECT `fo`.*, `f`.`title` as `franchise` FROM `franchises_owners` `fo` JOIN `franchises` `f` ON `f`.`id` = `fo`.`franchise_id` WHERE `f`.`id` = '14' ORDER BY `fo`.`id` DESC",
                'new'     => static fn () => $owners->listWithFranchiseTitle(14),
            ],
            [
                'label'   => 'home_model::show_franchise_owner_by_id(13) (home_model.php:1910)',
                'old_sql' => "SELECT * FROM `franchises_owners` WHERE `id` = '13'",
                'new'     => static fn () => $owners->find(13),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_owner_info(35) (Ui_model.php:285)',
                'old_sql' => "SELECT * FROM `franchises_owners` WHERE `id` = '35'",
                'new'     => static fn () => $owners->find(35),
                'compare' => 'row',
            ],

            // ---- testimonials (admin) -------------------------------------
            [
                'label'   => 'home_model::show_franchiese_testimonials(8) (home_model.php:2081)',
                'old_sql' => "SELECT * FROM `franchises_testimonials` WHERE `franchise_id` = '8' ORDER BY `id` DESC",
                'new'     => static fn () => $testimonials->byFranchiseNewestFirst(8),
            ],
            [
                'label'   => 'home_model::show_franchise_testimonials_by_id(12) (home_model.php:2109)',
                'old_sql' => "SELECT * FROM `franchises_testimonials` WHERE `id` = '12'",
                'new'     => static fn () => $testimonials->find(12),
                'compare' => 'row',
            ],

            // ---- franchises (public, Ui_model) ----------------------------
            [
                'label'   => 'Ui_model::check_franchisee(14) (Ui_model.php:123)',
                'old_sql' => "select id from franchises where id='14' ",
                'new'     => static fn () => $franchises->findIdOnly(14),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::check_franchisee(999999) missing (Ui_model.php:123)',
                'old_sql' => "select id from franchises where id='999999' ",
                'new'     => static fn () => $franchises->findIdOnly(999999),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_franchisee_details(8) (Ui_model.php:434)',
                'old_sql' => "SELECT * FROM `franchises` WHERE `id` = '8'",
                'new'     => static fn () => $franchises->find(8),
                'compare' => 'row',
            ],
            [
                'label'   => 'Ui_model::get_franchisee_location_by_city(5) (Ui_model.php:440)',
                'old_sql' => "select location from franchises where city='5' and is_active='1' group by location",
                'new'     => static fn () => $franchises->activeLocationsByCity(5),
                'compare' => 'set',
            ],
            [
                'label'   => 'Ui_model::search_franchisee_by_location(1, 5, null) (Ui_model.php:446)',
                'old_sql' => "select f.*, c.city_name from franchises f, cities c where f.city=c.id and f.is_active='1' and f.city='5' ",
                'new'     => static fn () => $franchises->searchByCity(5, null),
                'compare' => 'set',
                'note'    => 'comma join written as INNER JOIN (same rows)',
            ],
            [
                'label'   => "Ui_model::search_franchisee_by_location(1, 5, 'Andheri (E)') (Ui_model.php:446)",
                'old_sql' => "select f.*, c.city_name from franchises f, cities c where f.city=c.id and f.is_active='1' and f.city='5' and f.location = 'Andheri (E)'",
                'new'     => static fn () => $franchises->searchByCity(5, 'Andheri (E)'),
                'compare' => 'set',
                'note'    => 'comma join written as INNER JOIN (same rows)',
            ],
            [
                'label'   => "Ui_model::search_franchisee_by_location(2, 'Brad Realty, Whitefield') (Ui_model.php:446)",
                // PUB-12 fixed: compared against the intended (bracketed, trimmed, literal) query.
                'old_sql' => "select * from franchises where is_active='1' and title LIKE '%Brad Realty%' and location LIKE '%Whitefield%'",
                'new'     => static fn () => $franchises->searchByTitleOrLocation('Brad Realty, Whitefield'),
                'compare' => 'set',
                'note'    => 'PUB-12 fixed: AND instead of unbracketed OR, location trimmed',
            ],
            [
                'label'   => "Ui_model::search_franchisee_by_location(2, 'Realty') no comma (Ui_model.php:446)",
                'old_sql' => "select * from franchises where is_active='1' and title LIKE '%Realty%'",
                'new'     => static fn () => $franchises->searchByTitleOrLocation('Realty'),
                'compare' => 'set',
                'note'    => 'PUB-12 fixed: no location filter when there is no comma',
            ],
            [
                'label'   => "Ui_model::search_franchisee_by_location(2, 'Spaces,Andheri%') (Ui_model.php:446)",
                'old_sql' => "select * from franchises where is_active='1' and title LIKE '%Spaces%' and location LIKE '%Andheri\\\\%%'",
                'new'     => static fn () => $franchises->searchByTitleOrLocation('Spaces,Andheri%'),
                'compare' => 'set',
                'note'    => 'PUB-12 fixed: % in the text is literal, not a wildcard',
            ],
            [
                'label'   => "Ui_model::get_franchisee_name('Realty') (Ui_model.php:475)",
                'old_sql' => "select title, location from franchises where is_active='1' and title LIKE '%Realty%'",
                'new'     => static fn () => $franchises->activeTitlesLike('Realty'),
                'compare' => 'set',
            ],
            [
                'label'   => 'Ui_model::get_all_franchisee_location (Ui_model.php:481)',
                'old_sql' => "select * from franchises where is_active='1'",
                'new'     => static fn () => $franchises->allActive(),
                'compare' => 'set',
            ],

            // ---- views/our_franchisee.php ---------------------------------
            [
                'label'   => 'our_franchisee.php:39 cities with active franchises',
                'old_sql' => 'select distinct c.city_name, c.id from cities c inner join franchises f on c.id=f.city  where is_active=1 order by c.city_name',
                'new'     => static fn () => array_map(
                    static fn (array $r) => ['city_name' => $r['city_name'], 'id' => $r['id']],
                    $cities->withActiveFranchises()
                ),
                'note'    => 'shares CitiesModel::withActiveFranchises() with :144; keys re-ordered here only (view reads by key)',
            ],
            [
                'label'   => 'our_franchisee.php:144 cities with active franchises',
                'old_sql' => 'select distinct c.id,c.city_name from franchises f inner join cities c on f.city=c.id where is_active=1 order by c.city_name',
                'new'     => static fn () => $cities->withActiveFranchises(),
            ],
            [
                'label'   => 'our_franchisee.php:154 active franchises of city 5',
                'old_sql' => "select id,title,location,external_link, franchise_status from franchises where is_active=1 and city='5' order by title",
                'new'     => static fn () => $franchises->activeByCity(5),
            ],
            [
                'label'   => 'our_franchisee.php:154 active franchises of city 4',
                'old_sql' => "select id,title,location,external_link, franchise_status from franchises where is_active=1 and city='4' order by title",
                'new'     => static fn () => $franchises->activeByCity(4),
            ],
        ];

        // views/our_franchisee_details.php and its unreferenced 24-05-2017 copy
        // run the same SQL; both line sets are checked against the same methods.
        $detailViews = [
            'our_franchisee_details.php'            => [3, 281, 319, 348, 378],
            'our_franchisee_details-24-05-2017.php' => [4, 223, 250, 277, 307],
        ];

        foreach ($detailViews as $view => [$l1, $lT, $lO, $lS, $lC]) {
            foreach ([14, 15] as $fid) {
                $checks[] = [
                    'label'   => "{$view}:{$l1} franchise + owner ({$fid})",
                    'old_sql' => "select franchises_owners.*, franchises.* from franchises  left join franchises_owners on franchises_owners.franchise_id = franchises.id where franchises.id ='{$fid}' limit 1",
                    'new'     => static fn () => $franchises->detailsWithOwner($fid),
                    'compare' => 'row',
                ];
            }
            $checks[] = [
                'label'   => "{$view}:{$lT} active testimonials (8)",
                'old_sql' => 'select * from franchises_testimonials where franchise_id="8" and is_active=1',
                'new'     => static fn () => $testimonials->activeByFranchise(8),
                'compare' => 'set',
            ];
            $checks[] = [
                'label'   => "{$view}:{$lO} owners (14)",
                'old_sql' => "select * from franchises_owners where franchise_id='14' order by id",
                'new'     => static fn () => $owners->byFranchise(14),
            ];
            $checks[] = [
                'label'   => "{$view}:{$lS} specialists (16)",
                'old_sql' => "select id,name,title,description,image from real_estate_specialist where franchise_id='16' order by name",
                'new'     => static fn () => $specialists->byFranchiseOrderedByName(16),
            ];
            $checks[] = [
                'label'   => "{$view}:{$lC} city name (5)",
                'old_sql' => "select city_name from cities where id='5' ",
                'new'     => static fn () => $cities->findName(5),
                'compare' => 'row',
            ];
        }

        return $checks;
    }
}
