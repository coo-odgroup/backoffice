<div class="bg-white border-end" id="sidebar-wrapper">
    <div class="d-flex justify-content-end p-3 d-md-none">
        <button class="btn btn-light" id="close-sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="d-md-none text-center border-bottom pb-3">
        <img src="https://i.pravatar.cc/60" class="rounded-circle mb-2" width="60" height="60" alt="PROFILE">
        <div class="fw-semibold">ODBUS</div>
        <div class="text-muted small">Administrator</div>
    </div>
    <div class="sidebar-heading p-3 fw-bold">Menu</div>

    <div class="list-group list-group-flush">

        <a href="#" class="list-group-item list-group-item-action">
            <i class="fa-solid fa-gauge me-2"></i> Dashboard
        </a>

        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#busManagement"
            aria-expanded="{{ Request::is('admin/states*','admin/department*','admin/branch-type*','admin/branch*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/roles*','admin/roles-hierarchy*','admin/reason*','admin/modules*','admin/boardingDropping*','admin/apiapps*','admin/apikeys*','admin/cityapis*','admin/users*','admin/brand*','admin/bus-model*','admin/axle-type*','admin/bus-service*','admin/mst-seatlayout*','admin/annexture-type','admin/annexture','admin/cancellationslab*','admin/cancellationslab-info*','admin/seat-layout*','admin/festive-days*','admin/ticket-fare-slab*','admin/ticketfareslab-info*','admin/bus-schedule*','admin/bus-cancel*','admin/seat-block*','admin/seat-open*','admin/notification-template*','admin/bus','admin/cron-job','admin/notification-rules*','admin/schema*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-bus me-2"></i> Bus Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/states*','admin/department*','admin/branch-type*','admin/branch*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/roles*','admin/roles-hierarchy*','admin/reason*','admin/modules*','admin/boardingDropping*','admin/apiapps*','admin/apikeys*','admin/cityapis*','admin/users*','admin/brand*','admin/bus-model*','admin/axle-type*','admin/bus-service*','admin/mst-seatlayout*','admin/annexture-type','admin/annexture','admin/cancellationslab*','admin/cancellationslab-info*','admin/seat-layout*','admin/festive-days*','admin/ticket-fare-slab*','admin/ticketfareslab-info*','admin/bus-schedule*','admin/bus-cancel*','admin/seat-block*','admin/seat-open*','admin/notification-template*','admin/bus','admin/cron-job','admin/notification-rules*', 'schema*') ? 'show' : '' }}" id="busManagement">

            <a href="{{ url('admin/department') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/department*') ? 'active' : '' }}">
                <i class="fa-solid fa-building-user me-2"></i>Department
            </a>

            <a href="{{ url('admin/branch') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/branch*') ? 'active' : '' }}">
                <i class="fa-solid fa-code-branch me-2"></i>Branch
            </a>

            <a href="{{ url('admin/branch-type') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/branch-type*') ? 'active' : '' }}">
                <i class="fa-solid fa-sitemap me-2"></i>Branch Type
            </a>

            <a href="{{ url('admin/schema') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/schema*') ? 'active' : '' }}">
                <i class="fa-solid fa-database me-2"></i>Schema
            </a>

            <a href="{{ url('admin/notification-rules') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/notification-rules*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell-concierge me-2"></i>Notification Rules
            </a>

            <a href="{{ url('admin/cron-job') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cron-job*') ? 'active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left me-2"></i>Cron Job
            </a>

            <a href="{{ url('admin/bus') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bus') ? 'active' : '' }}">
                <i class="fa-solid fa-route me-2"></i>Bus
            </a>

            <a href="{{ url('admin/notification-template') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/notification-template*') ? 'active' : '' }}">
                <i class="fa-solid fa-bell me-2"></i> Notification Template
            </a>

            <a href="{{ url('admin/seat-open') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/seat-open*') ? 'active' : '' }}">
                <i class="fa-solid fa-door-open me-2"></i> Seat Open
            </a>

            <a href="{{ url('admin/seat-block') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/seat-block*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-slash me-2"></i> Seat Block
            </a>

            <a href="{{ url('admin/bus-schedule') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bus-schedule*') ? 'active' : '' }}">
                <i class="fa-solid fa-calendar-days me-2"></i> Bus Schedule
            </a>

            <a href="{{ url('admin/bus-cancel') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bus-cancel*') ? 'active' : '' }}">
                <i class="fa-solid fa-ban me-2"></i> Bus Cancel
            </a>


            <a href="{{ url('admin/ticketfareslab-info') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ticketfareslab-info*') ? 'active' : '' }}">
                <i class="fa-solid fa-table me-2"></i> Ticket Fare Slab Info
            </a>


            <a href="{{ url('admin/ticket-fare-slab') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ticket-fare-slab*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-2"></i> Ticket Fare Slab
            </a>

            <a href="{{ url('admin/festive-days') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/festive-days*') ? 'active' : '' }}">
                <i class="fa-solid fa-gift me-2"></i> Festive Days
            </a>

            <a href="{{ url('admin/brand') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/brand*') ? 'active' : '' }}">
                <i class="fa-solid fa-tag me-2"></i> Bus Brand
            </a>

            <a href="{{ url('admin/bus-model') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bus-model*') ? 'active' : '' }}">
                <i class="fa-solid fa-bus me-2"></i> Bus Model
            </a>

            <a href="{{ url('admin/axle-type') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/axle-type*') ? 'active' : '' }}">
                <i class="fa-solid fa-truck me-2"></i> Bus Axle Type
            </a>

            <a href="{{ url('admin/bus-service') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bus-service*') ? 'active' : '' }}">
                <i class="fa-solid fa-gears me-2"></i> Bus Service
            </a>

            <a href="{{ url('admin/mst-seatlayout') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/mst-seatlayout*') ? 'active' : '' }}">
                <i class="fa-solid fa-chair me-2"></i> Bus Seat Layout
            </a>

            <a href="{{ url('admin/annexture-type') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/annexture-type') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-2"></i> Annexture Type
            </a>

            <a href="{{ url('admin/annexture') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/annexture') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines me-2"></i> Annexture
            </a>

            <a href="{{ url('admin/seat-layout') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/seat-layout*') ? 'active' : '' }}">
                <i class="fa-solid fa-object-group me-2"></i> Seat Layout
            </a>
            <a href="{{ url('admin/states') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/states*') ? 'active' : '' }}">
                <i class="fa-solid fa-map me-2"></i> State
            </a>

            <a href="{{ url('admin/district') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/district*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot me-2"></i> District
            </a>

            <a href="{{ url('admin/cities') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cities*') ? 'active' : '' }}">
                <i class="fa-solid fa-city me-2"></i> City
            </a>

            <a href="{{ url('admin/bustype') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/bustype*') ? 'active' : '' }}">
                <i class="fa-solid fa-bus-simple me-2"></i> Bus Type
            </a>

            <a href="{{ url('admin/seatingtype') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/seatingtype*') ? 'active' : '' }}">
                <i class="fa-solid fa-couch me-2"></i> Seating Type
            </a>

            <a href="{{ url('admin/amenitycategory') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/amenitycategory*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-2"></i> Amenity Category
            </a>

            <a href="{{ url('admin/amenities') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/amenities*') ? 'active' : '' }}">
                <i class="fa-solid fa-star me-2"></i> Amenity
            </a>

            <a href="{{ url('admin/roles-hierarchy') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/roles-hierarchy') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield me-2"></i> Roles Hierarchy
            </a>
            <a href="{{ url('admin/roles') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/roles') ? 'active' : '' }}">
                <i class="fa-solid fa-user-shield me-2"></i> Roles
            </a>

            <a href="{{ url('admin/reason') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/reason*') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-question me-2"></i> Reason
            </a>

            <a href="{{ url('admin/boardingDropping') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/boardingDropping*') ? 'active' : '' }}">
                <i class="fa-solid fa-plane-departure me-2"></i> Boarding / Dropping
            </a>

            <a href="{{ url('admin/modules') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/modules*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group me-2"></i> Modules
            </a>

            <a href="{{ url('admin/faq') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/faq*') ? 'active' : '' }}">
                <i class="fa-solid fa-headset me-2"></i> FAQ
            </a>

            <a href="{{ url('admin/faqcategory') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/faqcategory*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open me-2"></i> FAQ Category
            </a>

            <a href="{{ url('admin/apiapps') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/apiapps*') ? 'active' : '' }}">
                <i class="fa-solid fa-font me-2"></i> Api Apps
            </a>

            <a href="{{ url('admin/apikeys') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/apikeys*') ? 'active' : '' }}">
                <i class="fa-solid fa-key me-2"></i> Api Keys
            </a>

            <a href="{{ url('admin/cityapis') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cityapis*') ? 'active' : '' }}">
                <i class="fa-solid fa-lock me-2"></i> City Apis
            </a>

            <a href="{{ url('admin/users') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/users*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-group me-2"></i> Users
            </a>

            <a href="{{ url('admin/reviewcategory') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/reviewcategory*') ? 'active' : '' }}">
                <i class="fa-solid fa-comments me-2"></i> Review Category
            </a>

            <a href="{{ url('admin/cancellationslab') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cancellationslab*') ? 'active' : '' }}">
                <i class="fa-solid fa-ban me-2"></i> Cancellation Slab
            </a>

            <a href="{{ url('admin/cancellationslab-info') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cancellationslab-info*') ? 'active' : '' }}">
                <i class="fa-solid fa-circle-info me-2"></i> Cancellation Slab Info
            </a>

        </div>


        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#blogManagement"
            aria-expanded="{{ Request::is('admin/blog-author*','admin/blogs*','admin/blog-category*','admin/blogs*','admin/blog-images*','admin/blog-routes*','admin/blog-tags*','admin/blog-tag-map*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-blog me-2"></i> Blog Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/blog-author*','admin/blogs*','admin/blog-category*','admin/blogs*','admin/blog-images*','admin/blog-routes*','admin/blog-tags*','admin/blog-tag-map*') ? 'show' : '' }}" id="blogManagement">

            <a href="{{ url('admin/blog-author') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-author*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-edit me-2"></i> Blog Author
            </a>

            <a href="{{ url('admin/blog-category') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-category*') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open me-2"></i> Blog Category
            </a>

            <a href="{{ url('admin/blogs') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blogs*') ? 'active' : '' }}">
                <i class="fa-solid fa-pen-to-square me-2"></i> Blogs
            </a>

            <a href="{{ url('admin/blog-images') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-images*') ? 'active' : '' }}">
                <i class="fa-solid fa-image me-2"></i> Blog Images
            </a>

            <a href="{{ url('admin/blog-routes') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-routes*') ? 'active' : '' }}">
                <i class="fa-solid fa-route me-2"></i> Blog Routes
            </a>

            <a href="{{ url('admin/blog-tags') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-tags*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags me-2"></i> Blog Tags
            </a>

            <a href="{{ url('admin/blog-tag-map') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-tag-map*') ? 'active' : '' }}">
                <i class="fa-solid fa-link me-2"></i> Blog Tag Map
            </a>

        </div>

        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#adManagement"
            aria-expanded="{{ Request::is('admin/vendor*','admin/ad-placement*','admin/pricing-plan*','admin/ad-campaign*','admin/ads*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-rectangle-ad me-2"></i> Ad Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/vendor*','admin/ad-placement*','admin/pricing-plan*','admin/ad-campaign*','admin/ads*') ? 'show' : '' }}" id="adManagement">

            <a href="{{ url('admin/vendor') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/vendor*') ? 'active' : '' }}">
                <i class="fa-solid fa-building me-2"></i> Vendor
            </a>

            <a href="{{ url('admin/ad-placement') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ad-placement*') ? 'active' : '' }}">
                <i class="fa-solid fa-location-dot me-2"></i> Ad Placement
            </a>

            <a href="{{ url('admin/pricing-plan') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/pricing-plan*') ? 'active' : '' }}">
                <i class="fa-solid fa-hand-holding-dollar me-2"></i> Pricing Plan
            </a>

            <a href="{{ url('admin/ad-campaign') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ad-campaign*') ? 'active' : '' }}">
                <i class="fa-solid fa-bullhorn me-2"></i> Ad Campaign
            </a>

            <a href="{{ url('admin/ads') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ads*') ? 'active' : '' }}">
                <i class="fa-solid fa-rectangle-ad me-2"></i> Ads
            </a>
        </div>


        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#routeManagementSEO"
            aria-expanded="{{ Request::is('admin/manage-city-content*','admin/manage-route-distance*','admin/manage-boarding-dropping*','admin/manage-template*','admin/template-list*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-route me-2"></i> Manage Route SEO</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <div class="collapse {{ Request::is('admin/manage-city-content*','admin/manage-route-distance*','admin/manage-boarding-dropping*','admin/manage-popular-routes','admin/manage-template*','admin/template-list*') ? 'show' : '' }}" id="routeManagementSEO">
            <a href="{{ url('admin/manage-city-content') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/manage-city-content*') ? 'active' : '' }}">
                <i class="fa-solid fa-city me-2"></i> City Content Management
            </a>

            <a href="{{ url('admin/manage-route-distance') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/manage-route-distance*') ? 'active' : '' }}">
                <i class="fa-solid fa-route me-2"></i> Manage Route Distance
            </a>
            <a href="{{ url('admin/manage-boarding-dropping') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/manage-boarding-dropping*') ? 'active' : '' }}">
                <i class="fa-solid fa-map-location-dot me-2"></i> Manage Boarding Dropping
            </a>

            <a href="{{ url('admin/manage-popular-routes') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/manage-popular-routes*') ? 'active' : '' }}">
                <i class="fa-solid fa-route me-2"></i> Manage Popular Routes
            </a>

            <a href="{{ url('admin/manage-template') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/manage-template*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines me-2"></i> Manage Template
            </a>

            <a href="{{ url('admin/template-list') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/template-list*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines me-2"></i> Template List
            </a>
        </div>

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#orgManagement"
            aria-expanded="{{ Request::is('admin/organization-type*','admin/organization') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-route me-2"></i> Organization Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <div class="collapse {{ Request::is('admin/organization-type*','admin/organization') ? 'show' : '' }}" id="orgManagement">
            <a href="{{ url('admin/organization-type',) }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/organization-type') ? 'active' : '' }}">
                <i class="fa-solid fa-city me-2"></i> Organization Type
            </a>

            <a href="{{ url('admin/organization') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/organization') ? 'active' : '' }}">
                <i class="fa-solid fa-city me-2"></i> Organization
            </a>


        </div>
    </div>
</div>