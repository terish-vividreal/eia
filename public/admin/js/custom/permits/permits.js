"use strict";

var pageTitle 	  = $("#pageTitle").val();
var pageRoute 	  = $("#pageRoute").val();
var projectRoute  = $("#projectRoute").val();
var table;
var id;
var postId;
var PageForm;
var formMethod;
var validator;
var projectId;
var eiaId;

// $('#sortBy').select2({ placeholder: "Sort By", allowClear: true});
// $('#documentType').select2({ placeholder: "Please select Document Type", allowClear: true});
// $('#status').select2({ placeholder: "Please select Status", allowClear: true});
// $('#stage').select2({ placeholder: "Please select Stage", allowClear: true});
// $('#project_id').select2({ placeholder: "Please select a Project", allowClear: true});


// DataTable Initialization
var columns;
var formValue;

var table     = $('#data-table-eia');
var url       = table.data('url');
var form      = table.data('form');
var length    = table.data('length');
columns   = [];
formValue = [];

table.find('thead th').each(function () {
  var column = {'data': $(this).data('column')};
  columns.push(column);
});

table.DataTable({
  processing: true,
  serverSide: true,
  searching: false,
  bLengthChange: false,
  pageLength: 10,
  ajax: {
    "type": "GET",
    "url": url,
    "data": function (data) {
      data.form = formValue;
    }
  },
  columns: columns,
});
