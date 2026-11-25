<?php

namespace App\Database\Seeds;

/**
 * CMS "FAQ" page (slug faq, Figma FAQ – v2 143:617): navy header (no photo) + the FAQ accordion with
 * the old page's online-payment questions. Idempotent.
 *
 *   php spark db:seed FaqPageSeeder
 */
class FaqPageSeeder extends CmsPageSeeder
{
    public const SLUG = 'faq';

    public function run(): void
    {
        $id = $this->createPage(self::SLUG, 'FAQ', [
            'meta_title' => 'Frequently Asked Questions | Coldwell Banker India',
            'meta_description' => 'Answers about online payments, applications and what happens next with Coldwell Banker India.',
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Header', [
            'eyebrow' => 'Home  ·  Help  ·  FAQ', 'title' => 'Frequently Asked Questions',
            'lead'    => 'Answers about online payments, applications and what happens next.', 'image' => '',
        ]);
        $this->block($id, 20, 'faq', 'Questions', [
            'title' => 'Online Payments',
            'lead'  => 'Everything you need to know before you pay or apply online with Coldwell Banker India. Still have questions? Write to info@coldwellbanker.in.',
        ], ['qa' => [
            ['title' => 'How safe and secure is this platform for online payment?', 'body' => 'The online payment gateway is hosted on a secure network. On every successful transaction you will receive a reference number and a confirmation email with all your transaction details. You will also receive a call from our Sales team to complete the subsequent formalities.'],
            ['title' => 'Once I have applied for the offer online, what are the next steps?', 'body' => 'On completion of a successful transaction you will receive an Expression of Interest confirmation with a unique customer code. The Sales team will contact you and carry out the next steps, including the site visit.'],
            ['title' => 'What is an “Expression of Interest”?', 'body' => 'An Expression of Interest serves as an approval to the organisation, as per the details provided by the Applicant.'],
            ['title' => 'Are credit and debit cards of all banks accepted?', 'body' => 'Yes — all bank networks of VISA and MasterCard are accepted.'],
        ]]);
        $this->newsletter($id, 30);
        $this->linkMenu($id, 'faq');
    }
}
