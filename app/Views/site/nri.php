<!doctype html>
<html>

<head>
    <title><?= esc($page_info->title) ?></title>
	<meta name="description" content="<?= esc($page_info->meta_description, 'attr') ?>" />
	<meta name="keywords" content="<?= esc($page_info->meta_keywords, 'attr') ?>" />
	<?= $this->include('partials/header') ?>
  <?= $this->include('partials/ga_code_header') ?>
</head>

<body class="nri-bg">

  <div class="lead-form-wrp">
    <p class="enquire-btn">Enquire Now</p>
    <div class="enquire-wrp">
      <form id="nri_enquiry" method="post" action="<?php echo base_url(); ?>">
<?= csrf_field() ?>
          <div id="message" class="message <?= esc($flash_class, 'attr') ?>"><?= esc($flash_message) ?></div>
          <input type="text" name="name" id="name" placeholder="Name*">
          <input type="text" name="email" id="email" placeholder="Email*">
          <input type="text" name="phone" id="phone" placeholder="Phone*">
          <input type="hidden" id="nri_country" name="nri_country" value="">
          <textarea name="message" id="message" placeholder="Message"></textarea>
          <div class="button-wrp">
            <input type="submit" name="" id="index_submit" value="Submit" class="sub-btn">
            <span class="">*Fields are mandatory.</span>
          </div>
      </form>
      <p class="call-wrp">
        <span>USA Toll Free Number:</span>
        <a href="tel: +1 855-444-6747">+1 855-444-6747</a>
      </p>
    </div>
  </div>

    <article class="nri aos-all">
        <section>
            <div class="container">
                <?php $this->setVar('act', $page_active); // CI3: $data1['act'] for the navigation partial ?><?= $this->include('partials/navigation') ?>
            </div>
        </section>
        <section class="inner_banner_container">
            <div class="inner_banner">
                <div class="slide-txt">
                    <div class="fr-main-left aos-item"data-aos="fade-up">
                        <h1>Investment <br>opportunities<br> back home !</h1>
                        
                    </div>
                    <div class="fr-main-right learn-right">
                        <img src="<?php echo base_url(); ?>assets/images/nri-slider-img.png" alt="" class="no-margin">
                    </div>
                </div>
            </div>
            <div class="banner_sidebar learn-sidebar  aos-item" data-aos="slide-left">
                <h2 class="m-t-01">Ask our Specialist <span>+91 22 6172 5797</span> </h2>
                <div class="divider"></div>
                <h2>Or <a href="javascript:void(0);" class="hm-enq-btn">Enquire Now</a></h2>
            </div>
        </section>

        <section class="" id="nri">
            <div class="container">            

                <div class="aos-item presence" data-aos="fade-up">
                   <h3>Think Real Estate, Think India </h3>

                   <p>The face of India’s real estate is changing rapidly and there are avenues for investment like never before. As global markets experience appreciation in Real Estate, India takes center stage as an attractive investment destination. Non-Resident Indians (NRIs) looking for re-connecting with their homeland, have numerous options to choose from. Coldwell Banker India believes in the untapped potential of Indian Real Estate. Be a part of this emerging market, make your investment in this growing economy with Coldwell Banker India.</p>
                </div>               

            </div>
        </section>

        <section class="aos-item nri-services-bg" data-aos="fade-up">
            <div class="container">
                <h3>Our Services</h3>

                <div class="nri-services">                   
                    <div class="tab">
                      <a href="javascript:void(0)" class="tablinks active" onclick="openCity(event, 'Buy')" id="">Buy</a>
                      <a href="javascript:void(0)" clashttp://coldwellbanker.netbizlabs.com/home/contact_enquirys="tablinks" onclick="openCity(event, 'Sell')">Sell</a>
                      <a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'Documentation')"><small>Documentation</small> & Taxation</a>
                      <a href="javascript:void(0)" class="tablinks" onclick="openCity(event, 'Property')">Property Management</a>
                    </div>

                    <div id="Buy" class="tabcontent" style="display: block">
                      <ul>
                          <li><img src="<?php echo base_url(); ?>assets/images/righ-loc-icn.png" alt=""> <span>Right Location</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/right-prop-icn.png" alt=""> <span>Right Property</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/neg-icn.png" alt=""> <span>Negotiation</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/doc-icn.png" alt=""> <span>Legal Consultancy & Documentation</span></li>
                      </ul>
                    </div>

                    <div id="Sell" class="tabcontent">
                      <ul>
                          <li><img src="<?php echo base_url(); ?>assets/images/val-icn.png" alt=""> <span>Property Valuation</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/Right-buyer.png" alt=""> <span>Right Buyer</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/neg-icn.png" alt=""> <span>Negotiation</span></li>
                          <li class="b-r-0"><img src="<?php echo base_url(); ?>assets/images/doc-icn.png" alt=""> <span>Documentation</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/legal-cons-icn.png" alt=""> <span>Legal Consultancy</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/finance-icn.png" alt=""> <span>Finance</span></li>
                      </ul>
                    </div>

                    <div id="Documentation" class="tabcontent">
                      <ul>
                          <li><img src="<?php echo base_url(); ?>assets/images/legal-cons-icn.png" alt=""> <span>Legal Consultancy</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/Tax.png" alt=""> <span>Tax Consultancy</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/Registration.png" alt=""> <span>Registration & Stamp Duty</span></li>
                      </ul>
                    </div>

                    <div id="Property" class="tabcontent">
                      <ul>
                          <li><img src="<?php echo base_url(); ?>assets/images/Tenant Acquisition.png" alt=""> <span>Tenant Acquisition</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/right-prop-icn.png" alt=""> <span>Property Maintainance </span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/dep-icn.png" alt=""> <span>Rent Collection & Deposit</span></li>
                          <li><img src="<?php echo base_url(); ?>assets/images/repart-icn.png" alt=""> <span>Repatriation</span></li>
                      </ul>
                    </div>
                   
                </div>
                
            </div>
        </section>

        <section class="nri-network aos-item"data-aos="fade-up">
            <div class="container">
                    <h3>NRI Homes Network</h3>
                    <img src="<?php echo base_url(); ?>assets/images/nri-networks.png" alt="" class="m-t-40">
             </div>
        </section>

        <section class="nri-evnt-sec">
            <div class="container">
              <h3>Upcoming Events</h3>
              <div class="owl-carousel" id="nri-events">        
                  <div class="item">
				  <a href="http://www.embassyprojects.com/event.html" target="_blank">
                    <div class="nri-event-wrap">
                     <div class="event-img"><img src="<?php echo base_url(); ?>assets/images/nri-event1.jpg" alt=""></div>
                     <div class="event-info">
                        <h3 style="margin:5px 0;font-size: 21px;">Luxury Meet Excellence</h3><br/>
                        <strong>Date: <small style="font-weight: normal;">Sunday, 30<sup style="vertical-align: super;">th</sup> July’2017</small></strong><br/><br/>
						<strong>Venue: <small style="font-weight: normal;">Four Seasons Hotel Silicon Valley</small></strong><br/><br/>
                        <strong>Location: <small style="font-weight: normal;">2050 University Avenue, East Palo Alto, CA 94303</small></strong>
						<br/><br/>
						<strong>Time: </strong><br/>
                        <strong><small style="font-weight: normal;">Luxury Meet Excellence  : 5 pm-8 pm</small></strong><br/><br/>
						
						<strong><small style="font-weight: normal;">RSVP: +1-888-628-4080</small></strong>

                       <!--  <a href="">Register Now</a> -->
                      </div>
                    </div>
					</a>
                  </div>
				<div class="item">
				  <a href="http://www.embassyprojects.com/event.html" target="_blank">
                    <div class="nri-event-wrap">
                     <div class="event-img"><img src="<?php echo base_url(); ?>assets/images/nri-event1.jpg" alt=""></div>
                     <div class="event-info">
                        <h3 style="margin:5px 0;font-size: 21px;">Four Seasons Event</h3><br/>
                        <strong>Date: <small style="font-weight: normal;">Sunday, 30<sup style="vertical-align: super;">th</sup> July’2017</small></strong><br/><br/>
						<strong>Venue: <small style="font-weight: normal;">Four Seasons Hotel Silicon Valley</small></strong><br/><br/>
                        <strong>Location: <small style="font-weight: normal;">2050 University Avenue, East Palo Alto, CA 94303</small></strong>
						<br/><br/>
						<strong>Time: </strong><br/>
                        <strong><small style="font-weight: normal;">Four Seasons Event  : 5 pm-8 pm</small></strong><br/><br/>
						
						<strong><small style="font-weight: normal;">RSVP: +1-888-628-4080</small></strong>

                       <!--  <a href="">Register Now</a> -->
                      </div>
                    </div>
					</a>
                  </div>
				
                  <div class="item">
                   <a href="http://www.embassyprojects.com/event.html" target="_blank">
                    <div class="nri-event-wrap">
                     <div class="event-img"><img src="<?php echo base_url(); ?>assets/images/nri-event1.jpg" alt=""></div>
                     <div class="event-info">
                        <h3 style="margin:5px 0;font-size: 21px;">Luxury Meet Excellence</h3><br/>
                        <strong>Date: <small style="font-weight: normal;">Sunday, 6<sup style="vertical-align: super;">th</sup> August’2017</small></strong><br/><br/>
						<strong>Venue: <small style="font-weight: normal;">Hilton Bellevue</small></strong><br/><br/>
                        <strong>Location: <small style="font-weight: normal;"> 300 112th Avenue SE Bellevue, WA 98004</small></strong>
						<br/><br/>
						<strong>Time: </strong><br/>
                        <strong><small style="font-weight: normal;">Luxury Meet Excellence : 5 pm-8 pm</small></strong><br/><br/>
						
						<strong><small style="font-weight: normal;">RSVP: +1-888-628-4080</small></strong>

                       <!--  <a href="">Register Now</a> -->
                      </div>
                    </div>
					</a>
                  </div>

                  <div class="item">
                   <a href="http://www.embassyprojects.com/event.html" target="_blank">
                    <div class="nri-event-wrap">
                     <div class="event-img"><img src="<?php echo base_url(); ?>assets/images/nri-event1.jpg" alt=""></div>
                     <div class="event-info">
                        <h3 style="margin:5px 0;font-size: 21px;">Four Seasons Event</h3><br/>
                        <strong>Date: <small style="font-weight: normal;">Sunday, 6<sup style="vertical-align: super;">th</sup> August’2017</small></strong><br/><br/>
						<strong>Venue: <small style="font-weight: normal;">Hilton Bellevue</small></strong><br/><br/>
                        <strong>Location: <small style="font-weight: normal;"> 300 112th Avenue SE Bellevue, WA 98004</small></strong>
						<br/><br/>
						<strong>Time: </strong><br/>
                    
						<strong><small style="font-weight: normal;">Four Seasons Event : 5 pm-8 pm</small></strong><br/><br/>
						<strong><small style="font-weight: normal;">RSVP: +1-888-628-4080</small></strong>

                       <!--  <a href="">Register Now</a> -->
                      </div>
                    </div>
					</a>
                  </div>
              </div>
            </div>
        </section>

        <section class="enquire-sec">
            <p class="para">Investing back home is now hassle free!</p>
            <p class="enquire-btn">Enquire Now</p>
        </section>

        <section class="faq-sec">
            <div class="container">
                <h3>FAQs</h3>
                <div class="acc-box">
                    <span class="acc-trigger active"><a href="javascipt:void()">Who is an NRI?</a></span>
                    <div class="acc-container open">
                        <p>A person residing outside India who is a citizen of India or a person outside India who is of Indian Origin is an NRI. The definition of person resident outside India is defined under section 2(w) of Foreign Exchange Management Act, 1999 as "a person who is not resident in India"</p>
						<p>A person shall be deemed to be a person not resident in India in the following cases:</p>
						<ul>
                            <li>Person residing in India for less than or up to 182 days during the preceding financial year. </li>
                            <li>Person who is residing abroad or residing outside India, in either case: </li>
                             </ul>
								<ul>
                                 	<li>For or on taking up employment outside India, or</li>
	                                <li>For carrying on outside India a business or vocation, or</li>
	                                <li>For any other purpose, in such circumstances as would indicate his intention to stay outside India for an uncertain period</li>


							   </ul></li>
                        </ul>
                                  
                    </div>
                    <span class="acc-trigger"><a href="javascipt:void()">Who is a PIO?</a></span>
                    <div class="acc-container">
                        <p>The definition of ‘Person of Indian Origin’ Is defined under section 2 (B) of Foreign Exchange Management (borrowing and lending in rupees) Regulations, 2000 and under Section 2 (XII) Of Foreign Exchange Management (Deposit) Regulations, 2000 as given under: </p>
						<ul>
                       <li>Person of Indian Origin’ means a citizen of any country other than Pakistan or Bangladesh, If</li>
<li>He at any time held an Indian passport; or</li>
<li>He or either of his parents or any of his grandparents was a citizen of India by virtue of the Constitution of India or the Citizenship Act, 1955 (57 of 1955); or</li>
<li>The person is a spouse of an Indian citizen or a person referred to in sub-clause (a) or (b)” </li>

                        
						</ul>
                        <br>

       <p>Person of Indian Origin (PIO) for the purpose of acquiring immovable property in India as given under: -</p>                   
	<li> 'Person of Indian Origin’ means an Individual (not being a citizen of Pakistan, Bangladesh, Sri Lanka, Afghanistan, China, Iran, Nepal or Bhutan), who at any time, held an Indian passport; or </li>
<li> Who or either of whose father or mother or whose grandfather or grandmother was a citizen of India by virtue of the Constitution of India or the Citizenship Act, 1955 (57 of 1955) </li>
						
						
						</ul>
                    </div>

                    <span class="acc-trigger"><a href="javascipt:void()">What are the various facilities available to an NRI?</a></span>
                    <div class="acc-container">
                        <p>NRIs are granted the following facilities:</p>
                        <ul>
                           <li>Maintenance of bank accounts in India</li>
                            <li>Investments in securities/shares of, and deposits with, Indian firms/companies</li>
                           <li>Investments in immovable properties in India</li>

                        
						</ul>
                    </div>
                    <span class="acc-trigger"><a href="javascipt:void()">Who can purchase immovable property in India?</a></span>
                    <div class="acc-container">
                        <p>Under the general permission available, the following categories can freely purchase immovable property in India:<p>
						<ul>
                            <li>Non-Resident Indian (NRI) – that is a citizen of India resident outside India</li>
                            <li>Person of Indian Origin (PIO) – that is an individual (not being a citizen of Pakistan, Bangladesh, Sri Lanka, Afghanistan, China, Iran, Nepal or Bhutan), who</li>
                            <li>At any time, held Indian passport, or</li>
                            <li>Who or either of whose father or grandfather was a citizen of India by virtue of the Constitution of India or the Citizenship Act, 1955 (57 of 1955). The general permission, however, covers only purchase of residential and commercial property</li>
						</ul>
                    </div>

                    <span class="acc-trigger"><a href="javascipt:void()">Can a NRI acquire a commercial property in India ?</a></span>
                    <div class="acc-container">
                        <p>Yes, under the general permission granted by the Reserve Bank of India:</p>
<p>Properties other than Agricultural Land / Farm House / Plantation Property can be acquired by NRIs provided</p>

						<ul>
                            <li>The purchase consideration is met either out of Inward remittances in foreign exchange through normal banking channels or</li>
							<li>Out of funds from the purchaser’s NRE/NRO/FCNR accounts maintained with banks in India and a declaration is submitted to the central office of Reserve Bank in Form IPI 7 within a period of 90 days from the date of purchase of the property/final payment of purchase consideration.</li>
						</ul>
                    </div>

                    <span class="acc-trigger"><a href="javascipt:void()">Can an NRI obtain loan for purpose of acquiring a residential or commercial property in India?</a></span>
                     <div class="acc-container">
                         <p>Certain Financial Institutions have been granted general permission by the Reserve Bank of India (RBI) to provide housing loans to NRI’s for acquisition of a House / Flat for self-occupation subject to certain conditions. These financial institutions are HDFC, LIC Housing Finance Ltd., etc. and other authorized dealers.</p>
						
						<p>Norms regarding the purpose of the loan, margin money and the quantum of loan will be at par with those applicable to Resident Indians. Repayment of the loan should be made within a period not exceeding 15 years, out of inward remittance through banking channels or out of funds held in the investor’s NRE/FCNR/NRO Accounts.</p>
					 </div>

                     <span class="acc-trigger"><a href="javascipt:void()">What is a Power of Attorney?</a></span>
                     <div class="acc-container">
                        <p>A Power of Attorney (POA) or Letter of Attorney is a written authorization to represent or act on another’s behalf in private affairs, business, or some other legal matter, sometimes against the wishes of the other. The person authorizing the other to act is known as the Principal, Grantor, or Donor (of the power)</p>
						<p>A Power of Attorney is not an instrument of transfer in regard to any right, title or interest in an immovable property.</p>
						<p>A Power of Attorney, or Letter of Attorney, is a document that authorizes another person known as the Agent or Attorney-In-Fact, usually a legally competent relative or close friend over 18 years old to handle any combination of financial, legal and health care decisions. A Power of Attorney is also referred to as a POA.<p>
						
						<p>Generally, one chooses a POA as a provision if he or she becomes incapacitated.</p>
                        <p><strong>*Types of Power of Attorney</strong></p>
                        <p>Power of Attorney (POA) is an instrument that is used by people to confer authority on somebody else to legally act on their behalf. POA are of two types. – </p>
                        <ul>
                            <li>Special Power of Attorney (SPA): While an SPA is used for transfer of a specific right to the person on whom it is conferred.</li>
							<li>General Power of Attorney (GPA): The GPA authorizes the holder to do whatever is necessary.</li>
						</ul>
                        <p>There is no sale clause of immovable property mentioned in POA (Notarized). Registered POA from registration office allows sale clause and POA to anyone.</p>
						<p>Below mentioned are the essential things to be considered while executing the POA:<p>
						<ul>
                       <li>Customer prepares POA as per defined format. </li>
                       <li>Person, who executes requires to paste his/her photograph along with signature on each page. <//li>
                        <li>Authentic/ Adjudicate the POA from Indian Embassy or Local Authority. </li>
                        <li>Send authenticated/ Adjudicated POA in India. </li>
                        <li>In India, the POA holder has to paste his/her photograph along with his/her left-hand thumb impression and signature. </li>
                        <li>This document must be stamped for Rs. 500 (ESBTR, Franking, Stamp Paper) and notarized from a registered notary. Ensure that a stamp of “before Me” is affixed on the document. </li>
                       <li>POA holder and executants photo ID attach before notary. </li>

                        
						</ul>
                    </div>

                     <span class="acc-trigger"><a href="javascipt:void()">What is the loan sanction process & documentation required?</a></span>
                     <div class="acc-container">
                        <p>The documentation needed to be submitted by the NRIs differ from the Resident Indians. Resident Indians are required to submit additional documents like copy of the passport and a copy of the works contract, etc. While NRIs are required to follow certain eligibility criteria to get home loans in India. </p>
						
						<p>Power of Attorney (POA) is another key document, which is required during the processing of NRI Home Loan. The POA is essential because the borrower is not based in India. Therefore, the home finance company would need a ‘representative’ ‘in lieu of’ the NRI to deal with and if needed. Although, not obligatory, the POA is usually drawn on the NRI’s Parents/ Wife/ Children/ Close relatives or Friends.</p>
					 </div>

                     <span class="acc-trigger"><a href="javascipt:void()">Can an NRI/PIO acquire agricultural land/plantation property/farmhouse in India?</a></span>
                     <div class="acc-container">
                         <p>Since NRI/PIO are not granted with the general permission to acquire agricultural land/plantation property/farmhouse in India, such proposals will require specific approval of Reserve Bank and the proposals are considered in consultation with the Government of India.</p>
					 </div>

                     <span class="acc-trigger"><a href="javascipt:void()">Tax on Income from immovable property selling / renting.</a></span>
                     <div class="acc-container">
                        <p>What is the tax treatment for income generated from property selling or renting for NRI/ PIO/OCI? </p>
						<p>The mere acquisition of property does not attract income tax. However, any income accruing from the ownership of it, in the form of rent (if it is let out)/annual value of the house (if is not let out and it is not the only residential property owned by that person in India) and/or capital gains (short term or long term) arising on the sale of this house or part thereof is taxable in the hands of the owner.</p>
                        <p><strong>Do NRI/PIO/OCI have to file return in India for their property rental income and Capital Gains Tax?</strong></p>
                        <p>The Government of India has granted general permission for NRI/PIO/OCI to buy property in India and they do not have to pay any taxes even while acquiring property in India. However, taxes have to be paid if they are selling this property. Rental income earned is taxable in India, and they will have to obtain a pan and file return of income if they have rented this property. On sale of the property, the profit on sale shall be subject to capital gains.</p>
                        <p><strong>Tax on income from immovable property selling/renting?</strong></p>
                        <p>If they have held the property for less than or equal to 3 years after taking actual possession then the gains would be short-term capital gains, which are to be included in their total income as tax as per the normal slab rates shall be payable and if the property has been held for more than 3 years then the resultant gain would be long-term capital gains subject to 20% tax plus applicable cess.</p>
                        <p><strong>How does the Double Taxation Avoidance Agreement (DTAA) work in the context of tax on income and Capital Gains tax paid in India by NRI? </strong></p>
                        <p>India has DTAA’s with several countries which give a favorable tax treatment in respect of certain heads of income. However, in case of sale of immovable property, the DTAA with most countries provide that the capital gains will be taxed in the country where the immovable property is situated. Hence, the non-resident will be subject to tax in India on the capital gains which arise on the sale of immovable property in India. Letting of immovable property in India would be taxed in India under most tax treaties in view of the fact that the property is situated in India.</p>
                    </div>

                     <span class="acc-trigger"><a href="javascipt:void()">Does Capital Gains Tax (CGT) apply to NRI/PIO/OCI?</a></span>
                     <div class="acc-container">Yes. Long-Term and Short-Term Capital Gains are taxable in the hands of non-residents.
</div>

                     <span class="acc-trigger"><a href="javascipt:void()">How is rate of CGT computed?</a></span>
                     <div class="acc-container">
                        <p><strong>Type of asset:</strong> like house property, land and building, jewelry, development rights etc. Rate of Tax Deduction at Source (TDS):</p> 
                        <p><strong>Long term –</strong> 20.6%</p> 
                        <p><strong>Short term –</strong> 30.9%.</p>
                        <p>Exemption available (only for long-term capital gains) the long-term capital gains arising on sale of a residential house can be invested in buying/ constructing another residential house, within the prescribed time. The exemption is restricted to the amount of capital gains or amount invested in new residential house, whichever is lower. If the amount of capital gains is invested in bonds of National Highways Authority of India (NHAI).</p>
						
						<p>(NHAI) or rural electrification corporation, then the entire capital gains is exempted, else the proportionate gain is exempted. As per the financial budget 2007-08, a cap of Rs. 50 lakhs have been imposed on investment that can be made in capital tax saving bonds.</p>
                     </div>
					 
					 <span class="acc-trigger"><a href="javascipt:void()">How does Double Taxation Avoidance Agreement (DTAA) work in the context of CGT paid in India on the foreign tax treatment?</a></span>
                     <div class="acc-container">
                         <p>In case the non-resident pays any tax on capital gains arising in India, he would normally be able to obtain a tax credit in respect of the taxes paid in India in the home country, because the income in India would also be included in the country of tax residence. The amount of the tax credit as also the basis of computing the tax credit that can be claimed are specified in the respective country’s DTAA and is also dependent on the laws of the home country where the taxpayer is a tax resident.</p>
						
						
					 </div>
                </div>

            </div>
        </section>

        <div class="clear"></div>
        <?= $this->include('partials/sub_footer') ?>
    </article>
    <?= $this->include('partials/footer') ?>

<script>
$(document).ready(function(){
	$(document).on('keyup','#phone',function(){
    var flag = $('#nri_enquiry .selected-flag').attr('title');
    var res = flag.split(":");
    var dialing_code = res[1];
    var country = $.trim(res[0].replace(/\(.*?\)/, ''));
    $("#nri_country").val(country);
  });

  $('.lead-form-wrp .enquire-btn').click(function(){
    if($('.lead-form-wrp').hasClass('active')){
      $('.lead-form-wrp').removeClass('active');
      //console.log('remove')
    }else{
      $('.lead-form-wrp').addClass('active')
      console.log('add')
    }
  })

  $('.enquire-sec .enquire-btn').click(function(){
    $('.lead-form-wrp').addClass('active');
  });

  // if($('.lead-form-wrp').hasClass('active')){
  //   $('.lead-form-wrp .enquire-btn').click(function(){
  //     $('.lead-form-wrp').removeClass('active')
  //   })
  //   }else{
  //     $('.lead-form-wrp').addClass('active')
  //   }
});
</script>
</body>
</html>
