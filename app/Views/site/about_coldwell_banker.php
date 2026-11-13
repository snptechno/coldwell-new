<!doctype html>
<html>

<head>
    <title><?= esc($page_info->title) ?></title>
	<meta name="description" content="<?= esc($page_info->meta_description, 'attr') ?>" />
	<meta name="keywords" content="<?= esc($page_info->meta_keywords, 'attr') ?>" />
	<?= $this->include('partials/header') ?>
    <?= $this->include('partials/ga_code_header') ?>
</head>

<body>
    <article class="aos-all usa">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
       
        <section class="inner_banner_container">
            <div class="main-frnachisee-bg">
                <div class="slide-txt" style="margin-top: 173px;">
                    <div class="fr-main-left aos-item aos-init aos-animate" data-aos="fade-up">
                        <h1>About the Coldwell<br> Banker<sup>®</sup> Brand<br><small>Fulfilling the Dream of Home Since 1906</small> </h1>
                        <span class="txt-para">For over a century, helping customers to find a perfect home has been our supreme purpose. Our vision defines us and every millennial, who is a part of our global network. Our aim will never diverge from our ultimate mission to deliver the treasure of abode, both ethically and reliably.</span>
                    </div>
                    <div class="fr-main-right usa-bgimg">
                        <img src="<?php echo base_url(); ?>assets/images/usa-slider-bg.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>

        </section>
       
        <div class="clear"></div>

        <section class="usa_container">
            <div class="container">
                <h2>A Story of Leadership</h2>
                <p>Coldwell Banker is one of the most established residential real estate franchise systems in North America. Its story of leadership, innovation and success dates back to 1906. After San Francisco was hit by a devastating earthquake, a young real estate agent, Colbert Coldwell saw the need for a trustworthy, honest and knowledgeable real estate service to help rebuild the city. This inspired him and he founded his real estate firm on August 27, 1906. Later, Benjamin Arthur Banker was welcomed on board, as a partner. For over a 100 years, the founding tradition and heritage revolves around infusing professionalism and superior customer service. It remains at the core of the Coldwell Banker philosophy.</p>

                <ul class="leader_list">
                    <li><img src="<?php echo base_url(); ?>assets/images/year-icon.png"><span>Since <strong>1906</strong></span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/world_icon.png"><span><strong>49</strong> Countries</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/offices_icon.png"><span><strong>3,000+</strong> Offices</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/agent_icon.png"><span><strong>88,000+ </strong>Agents</span></li>
                </ul>
                <div class="clear"></div>
                <hr>
                <br><br><br>

                <h2>About Coldwell Banker India</h2>
               <!-- <p>Coldwell Banker India is the Indian arm of world’s largest residential Brokerage & Advisory company, Coldwell Banker Real Estate LLC. Known for our people-centric endeavors to fulfill the dream of a perfect home, we are committed to perpetuating the legacy of more than a century that was first founded in 1906 in North America. Being a part of the renowned global conglomerate, we wish to create a happy home buying experience in India too. With the valued assistance of our dedicated team, we render credible real estate advisory services and widespread network of affiliated sales associates.</p>-->
<p>Coldwell Banker is a recognized residential real estate franchise system in Northern America. Its story of innovation, leadership, and success dates back to 1906. After San Francisco was hit by a disturbing earthquake, a young Realty agent, Colbert Coldwell saw the requirement for an honest, trustworthy and knowledgeable realty service that would help in rebuilding the city. This motivated him and he founded his realty firm on August 27, 1906. Later, Benjamin Arthur Banker was welcomed on board, as a partner. For more than 100 years, the formation of tradition and heritage revolves majorly around infusing superior customer service and professionalism. It certainly remains at the core philosophy of Coldwell Banker. </p>
                <ul class="leader_list">
                    <li><img src="<?php echo base_url(); ?>assets/images/cities-icon.png"><span>8<strong>Cities</strong></span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/agent_icon.png"><span><strong>150+</strong> Agents</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/offices_icon.png"><span><strong>30</strong> Offices</span></li>
                </ul>

                <div class="leadrs-wrap">
                    <h2>Our Leadership</h2>

                    <ul class="teamleader-list">
                        <li><img src="<?php echo base_url(); ?>assets/images/ramnik.jpg" alt="" class="ramn"><span>Ramnik Chopra <small>Managing Director</small></span></li>
                        <li class="col-6"><img src="<?php echo base_url(); ?>assets/images/anupam.jpg" alt=""><span>Anupam Sinha <small>Director </small></span></li>
                        <li class="col-6"><img src="<?php echo base_url(); ?>assets/images/charu.jpg" alt=""><span>Charu D'souza <small>HR Head</small></span></li>
                        <li class="col-6"><img src="<?php echo base_url(); ?>assets/images/syed.png" alt=""><span>Mojahid Taj Syed <small>Head NRI Sales - INDIA</small></span></li>
                        <li class="col-6"><img src="<?php echo base_url(); ?>assets/images/rahul.png" alt=""><span>Rahul Gaikwad <small>Head - International Business Development</small></span></li>


                       <!--  <li class="col-4"><img src="<?php echo base_url(); ?>assets/images/manesh.jpg" alt=""><span>Manesh Mamnia<small>President - Franchisee Sales & Consumer Marketing</small></span></li> -->
                        <li class="col-6"><img src="<?php echo base_url(); ?>assets/images/alok.png" alt=""><span>Alok Tiwari<small>Business Development</small></span></li>
                        <li class="col-6"><img src="<?php echo base_url(); ?>uploads/corporate_team/9270439511488803213.jpg" alt=""><span>Diana Joyal<small>General Manager - Operations, Strategy, Marketing</small></span></li>
                    </ul>
                </div>  
            </div>
        </section> 

        <section class="usa_container values-wrap">
            <div class="container">
                 <h2>Our Values</h2>
                <ul class="values-list">
                    <li><img src="<?php echo base_url(); ?>assets/images/trust-icn.png" alt=""><span>Trust</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/innovation-icn.png" alt=""><span>Innovation</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/collab-icn.png" alt=""><span>Collaboration</span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/busin-acc-icn.png" alt=""><span>Business Acumen </span></li>
                    <li><img src="<?php echo base_url(); ?>assets/images/cust-focus-icn.png" alt=""><span>Customer Focus</span></li>
                </ul>
            </div>
        </section>
        
        <section class="usa_container">
            <div class="container">
                <ul class="leader_bottom_list">
                    <li><h2>Legacy</h2>
                    <p>For over 109 years, 'Professionalism and Superior Customer Service' have been one of the core philosophies of Coldwell Banker. <br><br>Coldwell Banker belongs to the largest network of agents and offices worldwide. Our reach is incomparable.<br><br> It's simple: No other real estate company offers such vast exposure coupled with so many years of experience.<br><br>

                    </li>

                    <li><h2><img src="<?php echo base_url(); ?>assets/images/genblue.png" alt=""></h2>
                    <p>When you make a commitment to join the Coldwell Banker network, you become part of the fabric of Generation Blue.<br><br>
                    It is our shared culture of Coldwell Banker professionals in 43 countries across the globe that focuses on expanding its business, constantly striving to stay ahead with emerging technology and industry trends.</p>
                    </li>

                    <li><h2>Innovation</h2>
                    <p>As an industry pioneer for over a century, Coldwell Banker was the first full service real estate brand to:</p>
                        <ul>
                            <li>Launch a national website</li>
                            <li>Stream online videos</li>
                            <li>Create apps and responsive websites</li>
                            <li>Start a dedicated Youtube channel</li>
                            <li>Be recognized for best-in-class training</li>
                        </ul>
                    </li>

                    <li><h2><img src="<?php echo base_url(); ?>assets/images/univer-lgog.png" alt=""></h2>
                    <p>Make success a habit with our training and expert guidance. Discover your potential and adopt the best practices in the industry with us. Our training modules are internationally considered #1 in Real Estate. Our innovative approach shapes your growth and equips you with the skills needed to provide a truly exceptional experience to your customers.</p>
                    </li>
                </ul>
            </div>
        </section>

    
        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
    </article>
        <?= $this->include('partials/footer') ?>
<script>
$(document).ready(function(){
	
});
</script>
</body>
</html>
