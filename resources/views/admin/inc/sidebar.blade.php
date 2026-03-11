<div class="bg-white border-end" id="sidebar-wrapper">
    <div class="d-flex justify-content-end p-3 d-md-none">
        <button class="btn btn-light" id="close-sidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
    <div class="d-md-none text-center border-bottom pb-3">
        <img src="https://i.pravatar.cc/60" class="rounded-circle mb-2" width="60" height="60">
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
            aria-expanded="{{ Request::is('admin/states*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/roles*','admin/reason*','admin/modules*','admin/boardingDropping*','admin/apiapps*','admin/apikeys*','admin/cityapis*','admin/users*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-bus me-2"></i> Bus Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/states*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/roles*','admin/reason*','admin/modules*','admin/boardingDropping*','admin/apiapps*','admin/apikeys*','admin/cityapis*','admin/users*') ? 'show' : '' }}" id="busManagement">

            <a href="{{ url('admin/states') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/states*') ? 'active' : '' }}">
                <i class="fa-solid fa-location me-2"></i> State
            </a>

            <a href="{{ url('admin/district') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/district*') ? 'active' : '' }}">
                <i class="fa-solid fa-location me-2"></i> District
            </a>

            <a href="{{ url('admin/cities') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/cities*') ? 'active' : '' }}">
                <i class="fa-solid fa-location me-2"></i> City
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
                <i class="fa-solid fa-heart me-2"></i> Amenity Category
            </a>

            <a href="{{ url('admin/amenities') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/amenities*') ? 'active' : '' }}">
                <i class="fa-solid fa-heart me-2"></i> Amenity
            </a>

            <a href="{{ url('admin/roles') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/roles*') ? 'active' : '' }}">
                <i class="fa-solid fa-user me-2"></i> Roles
            </a>

            <a href="{{ url('admin/reason') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/reason*') ? 'active' : '' }}">
                <i class="fa-solid fa-user me-2"></i> Reason
            </a>

            <a href="{{ url('admin/boardingDropping') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/boardingDropping*') ? 'active' : '' }}">
                <i class="fa-solid fa-plane-departure me-2"></i> Boarding / Dropping
            </a>

            <a href="{{ url('admin/modules') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/modules*') ? 'active' : '' }}">
                <i class="fa-solid fa-address-book me-2"></i> Modules
            </a>

            <a href="{{ url('admin/faq') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/faq*') ? 'active' : '' }}">
                <i class="fa-solid fa-address-book me-2"></i> FAQ
            </a>

            <a href="{{ url('admin/faqcategory') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/faqcategory*') ? 'active' : '' }}">
                <i class="fa-solid fa-address-book me-2"></i> FAQ Category
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
                <i class="fa-solid fa-users me-2"></i> Users
            </a>

        </div>

        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#blogManagement"
            aria-expanded="{{ Request::is('admin/blog-category*','admin/blogs*','admin/blog-images*','admin/blog-routes*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-blog me-2"></i> Blog Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/blog-category*','admin/blogs*','admin/blog-images*','admin/blog-routes*') ? 'show' : '' }}" id="blogManagement">

            <a href="{{ url('admin/blog-category') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-category*') ? 'active' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Blog Category
            </a>

            <a href="{{ url('admin/blogs') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blogs*') ? 'active' : '' }}">
                <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Blogs
            </a>

            <a href="{{ url('admin/blog-images') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-images*') ? 'active' : '' }}">
                <i class="fa-solid fa-image me-2"></i> Blog Images
            </a>

            <a href="{{ url('admin/blog-routes') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/blog-routes*') ? 'active' : '' }}">
                <i class="fa-solid fa-image me-2"></i> Blog Routes
            </a>

        </div>
        <!-- Parent Menu -->
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
            data-bs-toggle="collapse"
            href="#adManagement"
            aria-expanded="{{ Request::is('admin/vendor*','admin/ad-placement*','admin/pricing-plan*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-rectangle-ad me-2"></i> Ad Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/vendor*','admin/ad-placement*','admin/pricing-plan*') ? 'show' : '' }}" id="adManagement">

            <a href="{{ url('admin/vendor') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/vendor*') ? 'active' : '' }}">
                <i class="fa-solid fa-building me-2"></i> Vendor
            </a>

            <a href="{{ url('admin/ad-placement') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/ad-placement*') ? 'active' : '' }}">
                <i class="fa fa-bullhorn me-2"></i> Ad Placement
            </a>

             <a href="{{ url('admin/pricing-plan') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/pricing-plan*') ? 'active' : '' }}">
                <i class="fa-solid fa-money-check-dollar me-2"></i>Pricing Plan
            </a>

        </div>
    </div>
</div>