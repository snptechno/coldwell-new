<?php

use App\Libraries\Cms\Offices;
use CodeIgniter\Test\CIUnitTestCase;

final class OfficeCityFilterTest extends CIUnitTestCase
{
    public function testMatchCityResolvesExactNormalizedAndPartialNames(): void
    {
        $cities = [
            'Mumbai'    => 12,
            'Bengaluru' => 5,
            'Delhi NCR' => 1,
            'Pune'      => 2,
        ];

        $this->assertSame('Delhi NCR', Offices::matchCity('Delhi NCR', $cities));
        $this->assertSame('Delhi NCR', Offices::matchCity('delhi ncr', $cities));
        $this->assertSame('Delhi NCR', Offices::matchCity('Delhi (NCR)', $cities));
        $this->assertSame('Delhi NCR', Offices::matchCity('Delhi', $cities));
        $this->assertSame('Mumbai', Offices::matchCity('Mumbai', $cities));
        $this->assertSame('', Offices::matchCity('UnknownCity', $cities));
        $this->assertSame('', Offices::matchCity('', $cities));
        $this->assertSame('', Offices::matchCity(null, $cities));
    }

    public function testCitySliderBuildsCityQueryLinkForOurFranchise(): void
    {
        $html = view('cms/blocks/city_slider', [
            's'     => ['title' => 'Our Presence Across India', 'lead' => ''],
            'items' => [
                'city' => [
                    [
                        'title'     => 'Delhi NCR',
                        'subtitle'  => 'Corporate Headquarters',
                        'body'      => 'Gurugram · Noida · New Delhi',
                        'image'     => 'assets/images/home-v2/city-delhi.jpg',
                        'image_alt' => 'Delhi NCR',
                        'cta_url'   => 'our-franchise',
                    ],
                ],
            ],
            'sfx'   => '',
        ], ['saveData' => false]);

        $decoded = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $this->assertStringContainsString('our-franchise?city=Delhi+NCR#offices', $decoded);
    }
}
