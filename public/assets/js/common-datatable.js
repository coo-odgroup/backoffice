function loadDataTable(tableId = 'datatable', dataTableColumns = [], orderBy = [], searchParams = {}, displayColumns = []) {
    //let page_title = $("#page_title").html().trim();
    let page_title = '';
    return table = $('#' + tableId).DataTable({
        dom: '<"top row noPrint" <"col-md-2"fl><"col-md-4"B><"col-md-6"p>>rt<"bottom row noPrint" <"col-md-6"i><"col-md-6"p><"clear">>',
        pagingType: "full_numbers",
        searching: false,
        ordering: true,
        columnDefs: [{ orderable: false, targets: "no-sort" }],
        order: [orderBy],
        processing: false,
        serverSide: true,
        lengthChange: true,
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
            // beforeSend: function() {
            //     $('#' + tableId).LoadingOverlay("show", { background: "rgb(134, 168, 192, 0.5)" });
            // },
            // complete: function() {
            //     $('#' + tableId).LoadingOverlay("hide", true);
            // },
        },
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa-regular fa-file-excel fa-lg fa-fw"></i> Excel',
                title: page_title,
                titleAttr: 'EXCEL',
                exportOptions: {
                    modifier: {
                        order: 'current',
                        page: 'all'
                    },
                    columns: displayColumns,
                },
                id: 'excel-all'
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa-regular fa-file-pdf fa-lg fa-fw"></i> PDF',
                orientation: 'landscape', // portrait
                pageSize: 'A4', // LEGAL
                // filename: 'dt_custom_pdf',
                title: page_title,
                titleAttr: 'PDF',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    },
                    columns: displayColumns,
                    // columns: [1,2,3,4,5],
                    search: 'applied',
                    filter: 'applied',
                    order: 'current'
                },
                id: 'pdf-all',
                customize: function ( doc ) {
                    doc.content.splice(0,1);
                    // Create a date string that we use in the footer. Format is dd-mm-yyyy
                    var now = new Date();
                    var jsDate = now.getDate()+'-'+(now.getMonth()+1)+'-'+now.getFullYear();

                    doc.pageMargins = [20,60,20,30];
                    // Set the font size fot the entire document
                    doc.defaultStyle.fontSize = 8;
                    // Set the fontsize for the table header
                    doc.styles.tableHeader.fontSize = 10;

                    doc['header']=(function() {
                        return {
                            columns: [
                                {
                                    image: 'data:image/png;base64,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',
                                    width: 100
                                },
                                {
                                    alignment: 'center',
                                    italics: false,
                                    text: page_title,
                                    fontSize: 18,
                                    margin: [10,0]
                                }
                                // ,
                                // {
                                //     alignment: 'right',
                                //     fontSize: 14,
                                //     text: 'Custom PDF export with dataTables'
                                // }
                            ],
                            margin: 20
                        }
                    });
                    // Create a footer object with 2 columns
                    // Left side: report creation date
                    // Right side: current page and total pages
                    doc['footer']=(function(page, pages) {
                        return {
                            columns: [
                                {
                                    alignment: 'left',
                                    text: ['Created on: ', { text: jsDate.toString() }]
                                },
                                {
                                    alignment: 'right',
                                    text: ['page ', { text: page.toString() },  ' of ', { text: pages.toString() }]
                                }
                            ],
                            margin: 20
                        }
                    });

                    var objLayout = {};
                    objLayout['hLineWidth'] = function(i) { return .5; };
                    objLayout['vLineWidth'] = function(i) { return .5; };
                    objLayout['hLineColor'] = function(i) { return '#aaa'; };
                    objLayout['vLineColor'] = function(i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function(i) { return 15; };
                    objLayout['paddingRight'] = function(i) { return 10; };
                    objLayout['paddingTop'] = function(i) { return 5; };
                    objLayout['paddingBottom'] = function(i) { return 5; };
                    doc.content[0].layout = objLayout;
                },
                //"action": newexportaction
            },
            {
                text: '<i class="fa fa-print fa-lg fa-fw"></i> Print',
                titleAttr: 'PRINT',
                action: function() {
                    printPdf();
                },
                id: 'print-all'
            }
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
}
