-- Minimal rows the test suite expects in the coldwell DB on GitHub Actions.
-- Dummy values only; no real customer or franchise data.

-- EnquiryFormsTest::testFranchiseeDetailsEnquiryBlocksExternalRefererOpenRedirect posts to
-- franchisee_details_enquiry/12, which loads franchise #12 to build the redirect.
INSERT INTO `franchises`
    (`id`, `title`, `project_description`, `logo`, `mobile_no`, `email_id`, `website_url`, `video_url`,
     `city`, `location`, `address`, `latitude`, `longitude`, `description`, `speciality_markets`,
     `office_specialities`, `franchise_status`, `external_link`, `is_active`)
VALUES
    (12, 'CI Test Office', '', '', '0000000000', 'ci-office@example.com', '', '',
     1, 'Pune', 'Test address', '0', '0', '', '', '', 'Active', '', 1);
