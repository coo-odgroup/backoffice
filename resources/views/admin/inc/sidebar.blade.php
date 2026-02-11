 <div class="bg-white border-end" id="sidebar-wrapper">
     <div class="d-flex justify-content-end p-3 d-md-none">
         <button class="btn btn-light" id="close-sidebar">
             <i class="fa-solid fa-xmark"></i>
         </button>
     </div>
     <div class="d-md-none text-center border-bottom pb-3">
         <img src="https://i.pravatar.cc/60" class="rounded-circle mb-2" width="60" height="60">
         <div class="fw-semibold">David Stevenson</div>
         <div class="text-muted small">Administrator</div>
     </div>
     <div class="sidebar-heading p-3 fw-bold">Menu</div>

     <div class="list-group list-group-flush">

         <a href="#" class="list-group-item list-group-item-action">
             <i class="fa-solid fa-gauge me-2"></i> Dashboard
         </a>

         <!-- Parent Menu -->
         <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#tablesMenu">
             <span><i class="fa-solid fa-table me-2"></i> Tables</span>
             <i class="fa-solid fa-chevron-down small"></i>
         </a>

         <!-- Sub Menu -->
         <div class="collapse show" id="tablesMenu">
             <a href="#" class="list-group-item list-group-item-action ps-4 active">
                 <i class="fa-solid fa-file-lines me-2"></i> Booking Report
             </a>
             <a href="#" class="list-group-item list-group-item-action ps-4">
                 <i class="fa-solid fa-list me-2"></i> Simple Table
             </a>
         </div>

         <!-- Another Parent -->
         <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
             data-bs-toggle="collapse" href="#formsMenu">
             <span><i class="fa-solid fa-pen-to-square me-2"></i> Forms</span>
             <i class="fa-solid fa-chevron-down small"></i>
         </a>

         <div class="collapse" id="formsMenu">
             <a href="#" class="list-group-item list-group-item-action ps-4">
                 <i class="fa-solid fa-user-plus me-2"></i> Add User
             </a>
             <a href="#" class="list-group-item list-group-item-action ps-4">
                 <i class="fa-solid fa-gear me-2"></i> Settings
             </a>
         </div>

     </div>

 </div>