@if ($crud->exportButtons())
    <script src="{{ asset('vendor/backpack/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/buttons.bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/jszip.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/pdfmake.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/vfs_fonts.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/buttons.print.min.js') }}"></script>
    <script src="{{ asset('vendor/backpack/datatables/buttons.colVis.min.js') }}"></script>

    <script>
        crud.dataTableConfiguration.buttons = [
            {
                name: 'copyHtml5',
                extend: 'copyHtml5',
                exportOptions: {
                    columns: [':visible:not(.not-export-col):not(.hidden)'],
                },
                action: function(e, dt, button, config) {
                    $.fn.DataTable.ext.buttons.copyHtml5.action.call(this, e, dt, button, config);
                }
            },
            {
                name: 'excelHtml5',
                extend: 'excelHtml5',
                exportOptions: {
                    columns: [':visible:not(.not-export-col):not(.hidden)'],
                },
                action: function(e, dt, button, config) {
                    $.fn.DataTable.ext.buttons.excelHtml5.action.call(this, e, dt, button, config);
                }
            },
            {
                name: 'csvHtml5',
                extend: 'csvHtml5',
                exportOptions: {
                    columns: [':visible:not(.not-export-col):not(.hidden)'],
                },
                action: function(e, dt, button, config) {
                    $.fn.DataTable.ext.buttons.csvHtml5.action.call(this, e, dt, button, config);
                }
            },
            {
                name: 'pdfHtml5',
                extend: 'pdfHtml5',
                exportOptions: {
                    columns: [':visible:not(.not-export-col):not(.hidden)'],
                },
                orientation: 'landscape',
                action: function(e, dt, button, config) {
                    $.fn.DataTable.ext.buttons.pdfHtml5.action.call(this, e, dt, button, config);
                }
            },
            {
                name: 'print',
                extend: 'print',
                exportOptions: {
                    columns: [':visible:not(.not-export-col):not(.hidden)'],
                },
                action: function(e, dt, button, config) {
                    $.fn.DataTable.ext.buttons.print.action.call(this, e, dt, button, config);
                }
            },
            'colvis'
        ];

        // move the datatable buttons in the top-right corner and make them smaller
        function moveExportButtonsToTopRight() {
            crud.table.buttons().each(function(button) {
                if (button.node.className.indexOf('buttons-columnVisibility') == -1)
                {
                    button.node.className = button.node.className + " btn-sm";
                }
            })
            $(".dt-buttons").appendTo($('#datatable_button_stack' )).css('display', 'block');
        }

        crud.addFunctionToDataTablesDrawEventQueue('moveExportButtonsToTopRight');
    </script>
@endif