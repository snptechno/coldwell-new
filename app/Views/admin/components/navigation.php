  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?php echo base_url(); ?>assets/admin/dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Admin</p>
          <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
      </div>
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu">
		<li>
          <a href="<?php echo base_url(); ?>admin/dashboard/index">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
          </a>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-sitemap"></i>
            <span>CMS</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?= esc(site_url('admin/cms/pages'), 'attr') ?>"><i class="fa fa-circle-o"></i> Pages</a></li>
            <li><a href="<?= esc(site_url('admin/cms/pages') . '?type=article', 'attr') ?>"><i class="fa fa-circle-o"></i> Articles</a></li>
            <li><a href="<?= esc(site_url('admin/cms/menus'), 'attr') ?>"><i class="fa fa-circle-o"></i> Menus</a></li>
            <li><a href="<?= esc(site_url('admin/cms/settings'), 'attr') ?>"><i class="fa fa-circle-o"></i> Site settings</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-pencil-square-o"></i>
            <span>Blog</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/blog/posts"><i class="fa fa-circle-o"></i> Posts</a></li>
            <li><a href="<?php echo base_url(); ?>admin/blog/posts/add"><i class="fa fa-circle-o"></i> Add New Post</a></li>
            <li><a href="<?php echo base_url(); ?>admin/blog/categories"><i class="fa fa-circle-o"></i> Categories</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Learning Section</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/learning_overview"><i class="fa fa-circle-o"></i> Manage Overview</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/learning_events/"><i class="fa fa-circle-o"></i> Manage Events</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/learning_gallery/"><i class="fa fa-circle-o"></i> Manage Gallery</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/learning_gallery_thumbnails/"><i class="fa fa-circle-o"></i> Manage Gallery Thumb</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Franchises</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/manage_cities"><i class="fa fa-circle-o"></i>Manage Cities</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/manage_franchises"><i class="fa fa-circle-o"></i>Manage Franchises</a></li>
         </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Corporate Teams</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
        <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/manage_corporate_teams"><i class="fa fa-circle-o"></i>List</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Media</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
        <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/coldwell_banker_news"><i class="fa fa-circle-o"></i>Coldwell Banker News</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/coldwell_banker_industry_news"><i class="fa fa-circle-o"></i>Industry News</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/coldwell_banker_knowledge_series"><i class="fa fa-circle-o"></i>Knowledge Series</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/coldwell_banker_newsletter"><i class="fa fa-circle-o"></i>Newsletter</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/coldwell_banker_awards"><i class="fa fa-circle-o"></i>Manage Awards</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Videos</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
        <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/finding_home_videos"><i class="fa fa-circle-o"></i>Finding A Home</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/selling_home_videos"><i class="fa fa-circle-o"></i>Selling A Home</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/testimonial_videos"><i class="fa fa-circle-o"></i>Testimonials</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/cb_india_videos"><i class="fa fa-circle-o"></i>CB India Videos</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/featured_videos"><i class="fa fa-circle-o"></i>Featured Videos</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Research</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/manage_research"><i class="fa fa-circle-o"></i>Manage Research</a></li>
          </ul>
        </li>

        <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Careers</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/job_openings_list/"><i class="fa fa-circle-o"></i> Manage Jobs</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/manage_testimonials/"><i class="fa fa-circle-o"></i> Manage Testimonials</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/life_at_coldwell_banker/"><i class="fa fa-circle-o"></i> Life at coldwell banker</a></li>
            <li><a href="<?php echo base_url(); ?>admin/section/success_stories_videos/"><i class="fa fa-circle-o"></i>Success Story Videos</a></li>
          </ul>
      </li>

		  <li class="treeview">
          <a href="#">
            <i class="fa fa-book"></i>
            <span>Manage Job Applications</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li><a href="<?php echo base_url(); ?>admin/section/job_application_list/"><i class="fa fa-circle-o"></i> List</a></li>
          </ul>
      </li>
		<li>
			<a href="<?php echo base_url(); ?>admin/section/list_pages">
				<i class="fa fa-file"></i> <span>Manage Pages</span>
			</a>
		</li>

    <li>
      <a href="<?php echo base_url(); ?>admin/section/franchisee_enquiry">
        <i class="fa fa-phone"></i> <span>Franchisee Enquiry</span>
      </a>
    </li>
	<li>
      <a href="<?php echo base_url(); ?>admin/section/contact_enquiry">
        <i class="fa fa-phone"></i> <span>Contact Enquiry</span>
      </a>
    </li>
      <li>
      <a href="<?php echo base_url(); ?>admin/section/franchisee_project_enquiries">
        <i class="fa fa-phone"></i> <span>Franchisee Project Enquiries</span>
      </a>
    </li>
    <li>
      <a href="<?php echo base_url(); ?>admin/section/newsletter_list">
        <i class="fa fa-phone"></i> <span>Newsletters subscribers </span>
      </a>
    </li>
    <li>
      <a href="<?php echo base_url(); ?>admin/section/nri_enquiry_list">
        <i class="fa fa-phone"></i> <span>NRI Enquiry </span>
      </a>
    </li>

      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>
