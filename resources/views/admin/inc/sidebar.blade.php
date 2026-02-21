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
            aria-expanded="{{ Request::is('admin/states*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/boardingDropping*') ? 'true' : 'false' }}">
            <span><i class="fa-solid fa-bus me-2"></i> Bus Management</span>
            <i class="fa-solid fa-chevron-down small"></i>
        </a>

        <!-- Sub Menu -->
        <div class="collapse {{ Request::is('admin/states*','admin/district*','admin/cities*','admin/bustype*','admin/seatingtype*','admin/amenitycategory*','admin/amenities*','admin/boardingDropping*') ? 'show' : '' }}" id="busManagement">

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

            <a href="{{ url('admin/boardingDropping') }}"
                class="list-group-item list-group-item-action ps-4 {{ Request::is('admin/boardingDropping*') ? 'active' : '' }}">
                <i class="fa-solid fa-plane-departure me-2"></i> Boarding / Dropping
            </a>

        </div>
    </div>
</div>