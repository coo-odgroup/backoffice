import $ from 'jquery';

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.dataTableInstance = null;

export function loadDataTable(tableId = 'datatable', dataTableColumns = [], orderBy = [], searchParams = {}, displayColumns = []) {

    if ($.fn.DataTable.isDataTable('#' + tableId)) {



        let table = $('#' + tableId).DataTable();

        table.state.clear();
        table.page.len(10);
        table.page(0).draw('page');

        table.ajax.reload(null, true); // true = reset paging
        return;
    }

    let selectedLength = parseInt($('#pageSizeDatatable').val());
    if (isNaN(selectedLength)) {
        selectedLength = 10;
    }
   
    let page_title = $("#page_title").html().trim();
    window.dataTableInstance  = $('#' + tableId).DataTable({
        // dom: '<"top row noPrint" <"col-md-2"fl><"col-md-4"B><"col-md-6"p>>rt<"bottom row noPrint" <"col-md-6"i><"col-md-6"p><"clear">>',
        dom: 'Brt',
        paging: true,
        pageLength: selectedLength,
        pagingType: "full_numbers",
        searching: false,
        ordering: true,
        columnDefs: [{ orderable: false, targets: "no-sort" }],
        order: [orderBy],
        processing: true,
        serverSide: true,
        stateSave: true,
        lengthChange: false,
        autoWidth: false,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        // destroy: true,
        responsive: true,
        ajax: {
            url: $('#' + tableId).data('url'),
            type: "POST",
            data: function (searchParams) {

                // Get all form fields dynamically
                let formData = $('#backoffice-form').serializeArray();

                $.each(formData, function (index, field) {
                    searchParams[field.name] = field.value;
                });

                // CSRF if needed
                searchParams._token = $('meta[name="csrf-token"]').attr('content');
                
            }, 
            dataType: 'json',
            beforeSend: function () {
              $('#pageLoader').removeClass('d-none').addClass('d-flex');
            },
            complete: function () {
               $('#pageLoader').removeClass('d-flex').addClass('d-none');
            }           
        },       
        
        buttons: [
        { extend: 'excel', 
            className: 'd-none',
            exportOptions: {
                columns: ':not(.noPrint)'
            } 
         },
        { extend: 'pdfHtml5', 
            className: 'd-none',
            exportOptions: {
                columns: ':not(.noPrint)'
            } 
        },
        { extend: 'print', 
            className: 'd-none', 
            exportOptions: {
                columns: ':not(.noPrint)'
            } 
        }
    ],
        columns: dataTableColumns,
        drawCallback: function(settings) { 
            
            commonAjax.initTooltips();

            let api = this.api();
            let pageInfo = api.page.info();

            let total = pageInfo.recordsDisplay;

             // ===== If NO RECORDS =====
            if (total === 0) {

                // Hide top controls
                $('#tableActions').hide();  
                $('#datatable').hide();

                // Hide pagination (top & bottom)
                $('#customPagination').hide();
                $('#customPaginationTop').hide();

                // Hide info text
                $('#customTableInfo').hide();
                $('#customTableInfoTop').hide();

                // Optional: show custom no record message
                $('#norecord').show();

                return; // stop further execution
            }

             // ===== If RECORDS EXIST =====

            // Show controls
            $('#tableActions').show();
            $('#datatable').show();
            $('#customPagination').show();
            $('#customPaginationTop').show();
            $('#customTableInfo').show();
            $('#customTableInfoTop').show();
            $('#norecord').hide();

            let start = pageInfo.start + 1;
            let end = pageInfo.end;

            // ===== Info Text =====
           let infoText = `Showing ${start} to ${end} of ${total} entries`;

           $('#customTableInfo').html(infoText);
           $('#customTableInfoTop').html(infoText);

            // ===== Pagination Build =====
            let totalPages = pageInfo.pages;
            let currentPage = pageInfo.page + 1;
            let maxLength = 7;

            let paginationHtml = '<ul class="pagination mb-0 pagination-sm">';

            // Prev
            paginationHtml += `
                <li class="page-item ${pageInfo.page === 0 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pageInfo.page - 1}">Prev</a>
                </li>`;

            function pageItem(page) {
                return `
                    <li class="page-item ${page === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${page - 1}">${page}</a>
                    </li>`;
            }

            if (totalPages <= maxLength) {
                for (let i = 1; i <= totalPages; i++) {
                    paginationHtml += pageItem(i);
                }
            } else {

                paginationHtml += pageItem(1);

                if (currentPage > 3) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                let startMid = Math.max(currentPage - 1, 2);
                let endMid = Math.min(currentPage + 1, totalPages - 1);

                for (let i = startMid; i <= endMid; i++) {
                    paginationHtml += pageItem(i);
                }

                if (currentPage < totalPages - 2) {
                    paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                }

                paginationHtml += pageItem(totalPages);
            }

            // Next
            paginationHtml += `
                <li class="page-item ${pageInfo.page === totalPages - 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${pageInfo.page + 1}">Next</a>
                </li>`;

            paginationHtml += '</ul>';

            $('#customPagination').html(paginationHtml);
            $('#customPaginationTop').html(paginationHtml);        

            // ===== IMPORTANT: Attach Click Event AFTER rendering =====
            $('#customPagination, #customPaginationTop').find('.page-link').off('click').on('click', function (e) {

                e.preventDefault();

                let page = $(this).data('page');

                if (page >= 0 && page < totalPages) {
                    api.page(page).draw('page');
                }
            });

            $('#defaultRcord').css('display', 'none');
            if (settings.aiDisplay.length <= 0) {
                $('#norecord').css('display', 'block');
                $('#' + tableId + '_wrapper').css('display', 'none');
                $('#tfooter').css('display', 'none');
            } else {
                $('#' + tableId + '_wrapper').css('display', 'block');
                $('#norecord').css('display', 'none');
                $('#tfooter').css('display', 'block');
            }

        }
    });

    $('#btnExcel').click(function () {
         window.dataTableInstance.button('.buttons-excel').trigger();
    });

    $('#btnPdf').click(function () {
        window.dataTableInstance.button('.buttons-pdf').trigger();
    });

    $('#btnPrint').click(function () {
        window.dataTableInstance.button('.buttons-print').trigger();
    });

    $('#pageSizeDatatable').off('change').on('change', function () {
        let newLength = parseInt($(this).val());
        window.dataTableInstance.page.len(newLength).draw();
    });

    $(document).on('click', '#customPagination .page-link', function (e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page !== undefined) {
            window.dataTableInstance.page(page).draw('page');
        }
    });

    //  return table;  
}
