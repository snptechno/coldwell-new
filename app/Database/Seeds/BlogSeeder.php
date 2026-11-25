<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeds sample blog categories and published blog posts for Coldwell Banker India.
 * Idempotent: skips any category or post whose slug already exists.
 *
 *   php spark db:seed BlogSeeder
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $categories = [
            [
                'name'             => 'Market Insights',
                'slug'             => 'market-insights',
                'description'      => 'Data-backed trends, price movements, and quarterly commentary across Indian real estate markets.',
                'meta_title'       => 'Real Estate Market Insights | Coldwell Banker India Blog',
                'meta_description' => 'Explore Indian real estate market trends, residential demand shifts, and investment insights from Coldwell Banker India.',
                'status'           => 1,
                'sort_order'       => 1,
            ],
            [
                'name'             => 'Franchise & Growth',
                'slug'             => 'franchise-and-growth',
                'description'      => 'Guidance for brokerage owners and entrepreneurs scaling a real estate business with a global brand.',
                'meta_title'       => 'Real Estate Franchise & Growth | Coldwell Banker India Blog',
                'meta_description' => 'Learn how Indian real estate entrepreneurs build high-performing brokerages with Coldwell Banker India.',
                'status'           => 1,
                'sort_order'       => 2,
            ],
            [
                'name'             => 'Home Buying',
                'slug'             => 'home-buying',
                'description'      => 'Practical checklists, financing tips, and neighbourhood guides for home buyers in India.',
                'meta_title'       => 'Home Buying Guides | Coldwell Banker India Blog',
                'meta_description' => 'Step-by-step advice on choosing the right property, RERA checks, home loans, and closing smoothly.',
                'status'           => 1,
                'sort_order'       => 3,
            ],
            [
                'name'             => 'Home Selling',
                'slug'             => 'home-selling',
                'description'      => 'Pricing strategy, staging, and negotiation advice to help homeowners sell faster and at the right value.',
                'meta_title'       => 'Home Selling Tips | Coldwell Banker India Blog',
                'meta_description' => 'Discover proven home staging, valuation, and marketing strategies from Coldwell Banker India specialists.',
                'status'           => 1,
                'sort_order'       => 4,
            ],
            [
                'name'             => 'NRI Real Estate',
                'slug'             => 'nri-real-estate',
                'description'      => 'FEMA compliance, repatriation rules, and city-level opportunities for Non-Resident Indian investors.',
                'meta_title'       => 'NRI Real Estate Guide | Coldwell Banker India Blog',
                'meta_description' => 'Essential legal, tax, and investment guidance for NRIs buying or managing property in India.',
                'status'           => 1,
                'sort_order'       => 5,
            ],
            [
                'name'             => 'Luxury Living',
                'slug'             => 'luxury-living',
                'description'      => 'Architecture, design trends, and premier residential enclaves across India\'s top metropolitan cities.',
                'meta_title'       => 'Luxury Real Estate & Living | Coldwell Banker India Blog',
                'meta_description' => 'Inside India\'s finest luxury residences, bespoke interiors, and ultra-prime neighbourhoods.',
                'status'           => 1,
                'sort_order'       => 6,
            ],
        ];

        $categoryIds = [];
        $catTable    = $this->db->table('blog_categories');

        foreach ($categories as $cat) {
            $existing = $catTable->where('slug', $cat['slug'])->get(1)->getRowArray();
            if ($existing) {
                $categoryIds[$cat['slug']] = (int) $existing['id'];
                continue;
            }

            $cat['created_at'] = $now;
            $cat['updated_at'] = $now;
            $catTable->insert($cat);
            $categoryIds[$cat['slug']] = (int) $this->db->insertID();
        }

        $posts = [
            [
                'title'            => 'India\'s Residential Market Outlook: Key Trends Shaping Metro Demand',
                'slug'             => 'india-residential-market-outlook-metro-demand',
                'category_slug'    => 'market-insights',
                'author'           => 'Coldwell Banker India Research',
                'featured_image'   => 'smart-city_1.jpg',
                'excerpt'          => 'End-user confidence, infrastructure upgrades, and larger home formats continue to drive resilient residential demand across Delhi NCR, Mumbai, Bengaluru, and Hyderabad.',
                'content'          => '<p>India\'s residential real estate sector has entered a sustained phase of end-user-driven growth. Across major metropolitan corridors, buyers are prioritizing well-planned communities, branded developers, and transit-connected neighbourhoods.</p>'
                    . '<h2>1. Shift Toward Larger, Flexible Layouts</h2>'
                    . '<p>Hybrid work models have permanently reshaped homebuyer preferences. Demand for 3-BHK and 4-BHK residences with dedicated study spaces, balconies, and wellness amenities continues to outpace compact formats.</p>'
                    . '<h2>2. Infrastructure as a Value Multiplier</h2>'
                    . '<p>New metro extensions, expressways, and airport corridors in NCR, MMR, Bengaluru, and Pune are unlocking emerging micro-markets while compressing commute times.</p>'
                    . '<h2>3. Rise of Trusted Advisory</h2>'
                    . '<p>With greater transparency under RERA, buyers and investors increasingly rely on professionally trained real estate advisors for comparative valuation and due diligence.</p>',
                'meta_title'       => 'India Residential Market Outlook | Coldwell Banker India',
                'meta_description' => 'Key trends driving residential real estate demand across India\'s top metro cities.',
                'meta_keywords'    => 'India real estate outlook, metro housing demand, Coldwell Banker India',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-14 days')),
            ],
            [
                'title'            => 'Why Real Estate Entrepreneurs Choose a Global Franchise Model in India',
                'slug'             => 'why-real-estate-entrepreneurs-choose-global-franchise-india',
                'category_slug'    => 'franchise-and-growth',
                'author'           => 'Coldwell Banker India Franchise Desk',
                'featured_image'   => 'buy-or-sell_.jpg',
                'excerpt'          => 'Building an independent brokerage from scratch takes years of trial and error. Here is how a globally recognised franchise accelerates brand trust, training, and deal flow.',
                'content'          => '<p>India\'s real estate brokerage landscape is rapidly professionalising. Clients expect structured advisory, transparent processes, and international service standards.</p>'
                    . '<h2>Instant Brand Credibility</h2>'
                    . '<p>Partnering with a 120-year-old global brand immediately differentiates your brokerage when pitching exclusive mandates to developers, institutional sellers, and high-net-worth buyers.</p>'
                    . '<h2>Structured Training & Technology</h2>'
                    . '<p>Through Coldwell Banker University, franchise owners gain repeatable onboarding, sales coaching, and marketing playbooks that help new associates reach productivity faster.</p>'
                    . '<h2>Cross-City & Global Referral Network</h2>'
                    . '<p>Operating within a connected network across 40+ countries opens referral opportunities from relocating families, corporate clients, and NRI investors.</p>',
                'meta_title'       => 'Real Estate Franchise Model in India | Coldwell Banker India',
                'meta_description' => 'Discover how a Coldwell Banker franchise helps Indian real estate entrepreneurs scale faster.',
                'meta_keywords'    => 'real estate franchise India, brokerage growth, Coldwell Banker franchise',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-10 days')),
            ],
            [
                'title'            => 'First-Time Homebuyer Checklist: 7 Essential Checks Before You Sign',
                'slug'             => 'first-time-homebuyer-checklist-7-essential-checks',
                'category_slug'    => 'home-buying',
                'author'           => 'Coldwell Banker India Editorial',
                'featured_image'   => 'RERA.jpg',
                'excerpt'          => 'From RERA registration and title verification to carpet area calculations and hidden closing costs, use this practical checklist before committing to a property.',
                'content'          => '<p>Buying your first home is both an emotional milestone and a major financial decision. A disciplined due-diligence checklist protects your investment and prevents last-minute surprises.</p>'
                    . '<ul>'
                    . '<li><strong>Verify RERA Registration:</strong> Confirm the project\'s RERA number, sanctioned plans, and quarterly construction updates on the state portal.</li>'
                    . '<li><strong>Inspect Title & Approvals:</strong> Review the encumbrance certificate, commencement certificate, and occupancy certificate for ready homes.</li>'
                    . '<li><strong>Compare Carpet Area, Not Super Built-Up:</strong> Evaluate price per sq. ft. on actual usable carpet area.</li>'
                    . '<li><strong>Budget for Total Acquisition Cost:</strong> Account for stamp duty, registration, GST (if under construction), maintenance deposits, and interiors.</li>'
                    . '<li><strong>Get Home Loan Pre-Approval:</strong> Locking in eligibility early strengthens your negotiating position.</li>'
                    . '</ul>',
                'meta_title'       => 'First-Time Homebuyer Checklist India | Coldwell Banker India',
                'meta_description' => '7 essential legal, financial, and RERA checks every first-time homebuyer in India should complete.',
                'meta_keywords'    => 'homebuyer checklist India, RERA verification, buying a home in India',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-7 days')),
            ],
            [
                'title'            => 'How to Price and Present Your Home for a Faster, Higher-Value Sale',
                'slug'             => 'how-to-price-and-present-your-home-for-faster-sale',
                'category_slug'    => 'home-selling',
                'author'           => 'Coldwell Banker India Editorial',
                'featured_image'   => 'happy-to-be-home1.jpg',
                'excerpt'          => 'Overpricing stalls momentum while poor presentation invites low offers. Learn how data-led pricing and simple staging elevate buyer perception.',
                'content'          => '<p>The first 30 days on the market generate the strongest buyer interest for any residential listing. Getting pricing and presentation right from day one directly impacts your final closing price.</p>'
                    . '<h2>Anchor Your Asking Price in Recent Comparables</h2>'
                    . '<p>Use registered sale comparables within the same society or immediate micro-market rather than aspirational portal listings.</p>'
                    . '<h2>Declutter, Brighten, and Repair</h2>'
                    . '<p>Fresh neutral paint, working lighting fixtures, uncluttered countertops, and crisp natural-light photography dramatically improve both online click-through rates and in-person site visits.</p>'
                    . '<h2>Work With a Dedicated Listing Specialist</h2>'
                    . '<p>A structured marketing plan—combining qualified buyer databases, professional photography, and vetted site visits—keeps negotiations focused and transparent.</p>',
                'meta_title'       => 'How to Price and Stage Your Home | Coldwell Banker India',
                'meta_description' => 'Proven pricing and home presentation strategies to sell your property faster in India.',
                'meta_keywords'    => 'sell home faster India, property valuation, home staging tips',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-5 days')),
            ],
            [
                'title'            => 'NRI Property Investment in India: FEMA Rules, Taxation & Top Cities',
                'slug'             => 'nri-property-investment-india-fema-rules-taxation',
                'category_slug'    => 'nri-real-estate',
                'author'           => 'Coldwell Banker India NRI Desk',
                'featured_image'   => 'ofhomeandhearths.jpg',
                'excerpt'          => 'A clear primer for Non-Resident Indians looking to invest in Indian residential or commercial real estate—covering NRE/NRO accounts, POA, and repatriation.',
                'content'          => '<p>Non-Resident Indians continue to view Indian real estate as a compelling long-term asset class, supported by currency dynamics, rental yields in tech hubs, and transparent regulations.</p>'
                    . '<h2>What NRIs Can Purchase Under FEMA</h2>'
                    . '<p>NRIs and OCIs can freely purchase residential and commercial properties in India using NRE, NRO, or FCNR accounts. Agricultural land, plantation property, and farmhouses require specific RBI approval.</p>'
                    . '<h2>Managing Transactions Remotely</h2>'
                    . '<p>A properly notarised and adjudicated Power of Attorney (POA) allows trusted family members or representatives to complete registration formalities when you cannot travel in person.</p>'
                    . '<h2>Taxation & Repatriation</h2>'
                    . '<p>Rental income and capital gains are subject to Indian tax rules and TDS, with DTAA benefits available depending on your country of residence. Consult a qualified chartered accountant alongside your real estate advisor.</p>',
                'meta_title'       => 'NRI Property Investment Guide India | Coldwell Banker India',
                'meta_description' => 'FEMA rules, NRE/NRO funding, POA, and taxation essentials for NRI real estate investors in India.',
                'meta_keywords'    => 'NRI real estate India, FEMA property rules, NRI home investment',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-3 days')),
            ],
            [
                'title'            => 'Inside India\'s Luxury Housing Boom: What Discerning Buyers Expect Today',
                'slug'             => 'inside-india-luxury-housing-boom-buyer-expectations',
                'category_slug'    => 'luxury-living',
                'author'           => 'Coldwell Banker Global Luxury India',
                'featured_image'   => 'Gardenhomes1.jpg',
                'excerpt'          => 'Privacy, biophilic design, concierge services, and low-density living have become the new benchmarks for luxury residences across India.',
                'content'          => '<p>Luxury real estate in India has evolved far beyond square footage and marble finishes. Today\'s affluent buyers evaluate residences through the lens of lifestyle, privacy, and long-term architectural timelessness.</p>'
                    . '<h2>Low-Density & Private Access</h2>'
                    . '<p>Private elevator lobbies, fewer residences per floor, and expansive landscaped decks top the wishlist in prime markets like South Delhi, Worli, Bandra, Indiranagar, and Jubilee Hills.</p>'
                    . '<h2>Wellness & Sustainability</h2>'
                    . '<p>Air-purification systems, acoustic glazing, daylight-optimised floor plans, and green-building certifications are now core expectations rather than optional add-ons.</p>',
                'meta_title'       => 'India Luxury Housing Trends | Coldwell Banker India',
                'meta_description' => 'Explore what discerning luxury homebuyers look for in India\'s premier residential enclaves.',
                'meta_keywords'    => 'luxury real estate India, Coldwell Banker Global Luxury, prime residences',
                'published_at'     => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
        ];

        $postTable = $this->db->table('blog_posts');

        foreach ($posts as $post) {
            $existing = $postTable->where('slug', $post['slug'])->get(1)->getRowArray();
            if ($existing) {
                if (empty($existing['featured_image']) && ! empty($post['featured_image'])) {
                    $postTable->where('id', $existing['id'])->update([
                        'featured_image' => $post['featured_image'],
                        'og_image'       => 'uploads/blog/' . $post['featured_image'],
                        'updated_at'     => $now,
                    ]);
                }
                continue;
            }

            $categorySlug = $post['category_slug'];
            unset($post['category_slug']);

            $post['category_id'] = $categoryIds[$categorySlug] ?? null;
            $post['og_image']    = ! empty($post['featured_image']) ? 'uploads/blog/' . $post['featured_image'] : null;
            $post['image_alt']   = $post['title'];
            $post['status']      = 1;
            $post['noindex']     = 0;
            $post['views']       = 0;
            $post['created_at']  = $now;
            $post['updated_at']  = $now;

            $postTable->insert($post);
        }
    }
}
