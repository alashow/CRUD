 {{-- DATA TABLES SCRIPT --}}
 <script src="{{ asset('vendor/adminlte/bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
 <script src="{{ asset('vendor/adminlte/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>

 <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
 <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
 <script src="{{ asset('vendor/backpack/crud/js/list.js') }}"></script>

  <script>
    var crud = {
             exportButtons: JSON.parse('{!! json_encode($crud->export_buttons) !!}'),
             functionsToRunOnDataTablesDrawEvent: [],
             addFunctionToDataTablesDrawEventQueue: function (functionName) {
                 if (this.functionsToRunOnDataTablesDrawEvent.indexOf(functionName) == -1) {
                     this.functionsToRunOnDataTablesDrawEvent.push(functionName);
                 }
             },
             executeFunctionByName: function(str, args) {
                var arr = str.split('.');
                var fn = window[ arr[0] ];
                for (var i = 1; i < arr.length; i++)
                { fn = fn[ arr[i] ]; }
                fn.apply(window, args);
             },
             dataTableConfiguration: {
                 drawCallback: function (row, data, start, end, display) {
                     this.api().columns('.has-sum').every(function () {
                         var column = this;

                         // extracts first integer/float from string (including decimals)
                         var intVal = function (i) {
                             return typeof i === 'string' ? parseFloat(i.match(/[-]{0,1}[\d.]*[\d]+/g)[0], 10) :
                                 typeof i === 'number' ? i : 0;
                         };

                         var sum = column
                             .data()
                             .reduce(function (a, b) {
                                 return intVal(a) + intVal(b);
                             }, 0);

                         // TODO: add option for custom prefix/suffix instead of adding to default header label
                         $(column.footer()).html(sum.toFixed(2))
                     });
                 },
                 autoWidth: false,
                 pageLength: {{ $crud->getDefaultPageLength() }},
                 lengthMenu: @json($crud->getPageLengthMenu()),
                 /* Disable initial sort */
                 aaSorting: [],
                 language: {
                     "emptyTable": "{{ trans('backpack::crud.emptyTable') }}",
                     "info": "{{ trans('backpack::crud.info') }}",
                     "infoEmpty": "{{ trans('backpack::crud.infoEmpty') }}",
                     "infoFiltered": "{{ trans('backpack::crud.infoFiltered') }}",
                     "infoPostFix": "{{ trans('backpack::crud.infoPostFix') }}",
                     "thousands": "{{ trans('backpack::crud.thousands') }}",
                     "lengthMenu": "{{ trans('backpack::crud.lengthMenu') }}",
                     "loadingRecords": "{{ trans('backpack::crud.loadingRecords') }}",
                     "processing": "<img src='{{ asset('vendor/backpack/crud/img/ajax-loader.gif') }}' alt='{{ trans('backpack::crud.processing') }}'>",
                     "search": "{{ trans('backpack::crud.search') }}",
                     "zeroRecords": "{{ trans('backpack::crud.zeroRecords') }}",
                     "paginate": {
                         "first": "{{ trans('backpack::crud.paginate.first') }}",
                         "last": "{{ trans('backpack::crud.paginate.last') }}",
                         "next": "<span class='hidden-xs hidden-sm'>{{ trans('backpack::crud.paginate.next') }}</span><span class='hidden-md hidden-lg'>></span>",
                         "previous": "<span class='hidden-xs hidden-sm'>{{ trans('backpack::crud.paginate.previous') }}</span><span class='hidden-md hidden-lg'><</span>"
                     },
                     "aria": {
                         "sortAscending": "{{ trans('backpack::crud.aria.sortAscending') }}",
                         "sortDescending": "{{ trans('backpack::crud.aria.sortDescending') }}"
                     },
                     "buttons": {
                         "copy": "{{ trans('backpack::crud.export.copy') }}",
                         "excel": "{{ trans('backpack::crud.export.excel') }}",
                         "csv": "{{ trans('backpack::crud.export.csv') }}",
                         "pdf": "{{ trans('backpack::crud.export.pdf') }}",
                         "print": "{{ trans('backpack::crud.export.print') }}",
                         "colvis": "{{ trans('backpack::crud.export.column_visibility') }}"
                     },
                 },
                 processing: true,
                 serverSide: true,
                 searching: JSON.parse('{!! json_encode($crud->hasAccess('search')) !!}'),
                 ajax: {
                     "url": "{!! url($crud->route.'/search').'?'.Request::getQueryString() !!}",
                     "type": "POST"
                 },
                 dom:
                 "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-4'i><'col-sm-4'B><'col-sm-4'p>>",
             }
         }
  </script>

  @include('crud::inc.export_buttons')

  <script type="text/javascript">
    jQuery(document).ready(function($) {

      crud.table = $("#crudTable").DataTable(crud.dataTableConfiguration);

      $("#crudTable").css('width', '100%');
      crud.table.columns.adjust();

      // override ajax error message
      $.fn.dataTable.ext.errMode = 'none';
      $('#crudTable').on('error.dt', function(e, settings, techNote, message) {
          new PNotify({
              type: "error",
              title: "{{ trans('backpack::crud.ajax_error_title') }}",
              text: "{{ trans('backpack::crud.ajax_error_text') }}"
          });
      });

      // make sure AJAX requests include XSRF token
      $.ajaxPrefilter(function(options, originalOptions, xhr) {
          var token = $('meta[name="csrf_token"]').attr('content');

          if (token) {
                return xhr.setRequestHeader('X-XSRF-TOKEN', token);
          }
      });

      // on DataTable draw event run all functions in the queue
      // (eg. delete and details_row buttons add functions to this queue)
      $('#crudTable').on( 'draw.dt',   function () {
         crud.functionsToRunOnDataTablesDrawEvent.forEach(function(functionName) {
            // console.log(functionName);
            crud.executeFunctionByName(functionName);
         });
      } ).dataTable();

    });
  </script>

  @include('crud::inc.details_row_logic')
