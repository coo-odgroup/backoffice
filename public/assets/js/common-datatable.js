$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function loadDataTable(tableId = 'datatable', dataTableColumns = [], orderBy = [], searchParams = {}, displayColumns = []) {
    //let page_title = $("#page_title").html().trim();
    let page_title = '';
    let table = $('#' + tableId).DataTable({
        // dom: '<"top row noPrint" <"col-md-2"fl><"col-md-4"B><"col-md-6"p>>rt<"bottom row noPrint" <"col-md-6"i><"col-md-6"p><"clear">>',
        dom: 'Brtip',
        pageLength:10,
        pagingType: "full_numbers",
        searching: false,
        ordering: true,
        columnDefs: [{ orderable: false, targets: "no-sort" }],
        order: [orderBy],
        processing: false,
        serverSide: true,
        lengthChange: false,
        autoWidth: false,
        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],
        destroy: true,
        responsive: true,
        ajax: {
            url: $('#' + tableId).data('url'),
            type: "POST",
            data: searchParams,
            dataType: 'json',
            beforeSend: function () {
                $('#tableLoader').removeClass('d-none').addClass('d-flex');
            },
            complete: function () {
                $('#tableLoader').removeClass('d-flex').addClass('d-none');
            }
            // beforeSend: function() {
            //     $('#' + tableId).LoadingOverlay("show", { background: "rgb(134, 168, 192, 0.5)" });
            // },
            // complete: function() {
            //     $('#' + tableId).LoadingOverlay("hide", true);
            // },
        },       
        buttons: [
        { extend: 'excel', className: 'd-none' },
        { extend: 'pdfHtml5', className: 'd-none' },
        { extend: 'print', className: 'd-none' }
    ],
        columns: dataTableColumns,
        drawCallback: function(settings) {
            if (settings._irecordsFiltered < settings._iDisplayLength) {
                $('#' + tableId + '_paginate').css('display', 'none');
            } else {
                $('#' + tableId + '_paginate').css('display', 'block');
            }

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
         table.button('.buttons-excel').trigger();
    });

    $('#btnPdf').click(function () {
        table.button('.buttons-pdf').trigger();
    });

    $('#btnPrint').click(function () {
        table.button('.buttons-print').trigger();
    });


    $('#pageSizeDatatable').off('change').on('change', function () {
        let newLength = parseInt($(this).val());
        table.page.len(newLength).draw();
    });

     return table;  
}
