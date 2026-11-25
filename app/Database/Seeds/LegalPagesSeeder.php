<?php

namespace App\Database\Seeds;

/**
 * The four CMS legal pages (Figma Privacy 143:3, Terms 143:146, Disclaimer 143:300, Refund 143:467):
 * navy header (no photo) + the legal_text block. Wording is the existing site's, lightly cleaned;
 * it has NOT been reviewed by a lawyer — see the notes in the clauses marked [ ]. Idempotent per page.
 *
 *   php spark db:seed LegalPagesSeeder
 */
class LegalPagesSeeder extends CmsPageSeeder
{
    public const SLUGS = ['privacy-policy', 'terms-and-conditions', 'disclaimer', 'refund'];

    public function run(): void
    {
        $this->page('privacy-policy', 'Privacy Policy', 'Legal  ·  Privacy', 'How Coldwell Banker India protects the information you share with us.', [
            ['Our Commitment', "Coldwell Banker India shall take reasonable security practices to protect the privacy of the information provided by the Applicant(s).\n\nColdwell Banker India shall not disclose any such information of the applicant to any third party, except as set out below."],
            ['How We Use Your Information', 'To send important notices and communications regarding the status of their Application, relevant policies, terms, conditions, etc. of Coldwell Banker India as applicable.'],
            ['When We May Disclose Information', "- When the disclosure is required by law or requested by any statutory, regulatory or judicial authority or recognised self-regulating organisation having jurisdiction over the Parties.\n- When the disclosure is required in connection with any lawsuit."],
        ], 'privacy-policy', 'privacy_policy', 'privacy');

        $this->page('terms-and-conditions', 'Terms & Conditions', 'Legal  ·  Terms', 'The terms that apply when you apply or pay online with Coldwell Banker India.', [
            ['Eligibility', 'Only Indian Residents, NRIs and PIOs who are capable of entering into a contract under the Indian Contract Act, 1872 shall be eligible to apply.'],
            ['Compliance With Law', 'The Applicant(s), NRIs and PIOs shall be responsible for obeying all legal agreements under the applicable laws prevailing in India from time to time, and the Company shall not be responsible for any such non-compliance in any manner.'],
            ['Online Payments', "Subscribing the payment online requires a valid credit card (VISA / MasterCard) or any other card organisation, as well as the rules and regulations of the Reserve Bank of India applicable and prevailing from time to time.\n\nFor risks involved with online payment — such as technical failure during the payment gateway process, or an amount debited from the Applicant’s account but not credited to the account of Coldwell Banker India due to a technical error — Coldwell Banker India shall not be responsible for any such technical error in the payment transaction(s) or otherwise."],
            ['Your Reference Number', 'After a successful payment, a reference number will be generated and allotted to the Applicant(s), which must be quoted in all further communication. The reference number will be emailed to the Applicant on the email ID given in the KYC form.'],
            ['Nature of the Payment', 'The online payment plan is simply towards expressing interest in a particular deal with Coldwell Banker India.'],
            ['Governing Law & Jurisdiction', 'The terms and conditions herein shall be construed in accordance with Indian law, and any issues or disputes arising out of this transaction will be subject to the exclusive jurisdiction of the Courts at [City].'],
        ], 'terms-and-conditions', 'terms_and_conditions', 'terms');

        $this->page('disclaimer', 'Disclaimer', 'Legal  ·  Disclaimer', 'Please read this before relying on any information on this website.', [
            ['Use of Information', "In any event Coldwell Banker India will not be responsible for any loss arising from the use of, or reliance on, information provided on this website. The use of any information on this website is completely at one’s own risk.\n\nThe information on this website does not constitute a legal contract of any type between the owner and the visitor."],
            ['Intellectual Property', "The names, marks and copyrights in the artistic style in which they are represented herein belong to and are owned by Coldwell Banker India.\n\nThis website contains material which is owned by or licensed to Coldwell Banker India and is protected by copyright and intellectual property laws. Reproduction is prohibited without prior permission in writing."],
            ['Changes to This Website', 'Coldwell Banker India has the right to alter the information on this website without liability to communicate any changes.'],
            ['Information You Submit', 'A visitor, by submitting information through the website or by email, is considered to have provided true, accurate, current and complete information. By entering a mobile number, the visitor is considered to have authorised Coldwell Banker India to send promotional SMS and alerts, and to make contact.'],
            ['Viruses & Security', 'Coldwell Banker India shall make every effort to keep the website free of viruses and other harmful components. This website and emails from Coldwell Banker India are vulnerable to tampering, data corruption, viruses, interception and delivery error, and the visitor takes complete liability for any consequences that may arise.'],
            ['Images & Specifications', 'The images, features, designs, specifications, plans, facilities, etc. shown on this website are only indicative and subject to the approval of the respective authorities. The owner has the right to change these without any notice.'],
            ['Limitation of Liability', 'Coldwell Banker India or any of its representatives, managers and employees shall not be answerable for any damage arising out of or related to the use of the website.'],
            ['Links to Other Websites', 'The website may contain links to other websites which are not under the control of Coldwell Banker India. Any link to off-site pages may be accessed at one’s own risk.'],
        ], 'disclaimer');

        $this->page('refund', 'Refund Policy', 'Legal  ·  Refunds', 'What happens if an online payment does not go through as expected.', [
            ['All Sales Are Final', 'All sales are final and no exchange or refund will be permitted. You are wholly responsible for the invoice number you pay against and for all charges that result from those purchases. Coldwell Banker India and its services are not responsible for any payment made against an incorrect invoice number.'],
            ['Failed Transactions', 'If you have completed a transaction online and the money has been deducted from your card or bank account, but the payment is not reflected in your account within 24 hours of the transaction, you will be entitled to a full refund.'],
            ['How to Request a Refund', "Email us at info@coldwellbanker.in or send us a message from the Contact Us page, including:\n- Company name\n- Invoice number\n- Transaction date"],
            ['Refund Timeline', 'We will investigate the incident and refund your money within 30 working days of receiving your email, if we find that money was charged to your account or card without being reflected in your account.'],
        ], 'refund');
    }

    /** @param list<array{0:string,1:string}> $clauses */
    private function page(string $slug, string $title, string $eyebrow, string $lead, array $clauses, string ...$oldUrls): void
    {
        $id = $this->createPage($slug, $title, [
            'meta_title' => $title . ' | Coldwell Banker India',
            'meta_description' => $lead,
        ]);
        if ($id === null) {
            return;
        }
        $this->block($id, 10, 'page_hero', 'Header', ['eyebrow' => 'Home  ·  ' . $eyebrow, 'title' => $title, 'lead' => $lead, 'image' => '']);
        $this->block($id, 20, 'legal_text', 'Clauses', [
            'updated' => '', 'help_title' => $slug === 'refund' ? 'Payment not reflected?' : 'Questions about this page?',
            'help_text' => 'Write to us at info@coldwellbanker.in', 'cta_label' => 'Contact Us', 'cta_url' => 'contact-us',
        ], ['clause' => array_map(static fn (array $c): array => ['title' => $c[0], 'body' => $c[1]], $clauses)]);
        $this->newsletter($id, 30);
        $this->linkMenu($id, ...$oldUrls);
    }
}
