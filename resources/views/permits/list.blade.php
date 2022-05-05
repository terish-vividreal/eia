@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/flag-icon/css/flag-icon.min.css')}}">
  <!-- <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/jquery.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/data-tables/css/select.dataTables.min.css')}}"> -->
@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/custom/custom.css')}}">
@endsection

@section('content')

@section('breadcrumb')
<div class="col s12 m6 l6"><h5 class="breadcrumbs-title"><span>{{ $page->title ?? ''}}</span></h5></div>
<div class="col s12 m6 l6 right-align-md">
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ url($page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
    <li class="breadcrumb-item active">List</li>
  </ol>
</div>
@endsection
<div class="section">
  <div class="card">
    <div class="card-content">
      <p class="caption mb-0">Sample description area. Tables are a nice way to organize a lot of data. </p>
    </div>
  </div>
  <!-- Borderless Table -->
  <div class="row">
    <div class="col s12">
      <div id="borderless-table" class="card card-tabs">
        <div class="card-content data-table-container">
          <!-- <div class="card-title"> -->
          <div class="row right">
            <div class="col s12 m12 ">
              <!-- {!! App\Helpers\HtmlHelper::createLinkButton(url($page->route.'/create'), 'Create New ' .$page->title, ) !!} -->
              <a class="dropdown-settings btn mb-1 waves-effect waves-light cyan" href="#!" data-target="dropdown1" id="customerListBtn"><i class="material-icons hide-on-med-and-up">settings</i><span class="hide-on-small-onl">List {{Str::plural($page->title)}}</span><i class="material-icons right">arrow_drop_down</i></a>
              <ul class="dropdown-content" id="dropdown1" tabindex="0">
                <li tabindex="0"><a class="grey-text text-darken-2 listBtn" data-type="active" href="javascript:" > Active </a></li>
                <li tabindex="0"><a class="grey-text text-darken-2 listBtn" data-type="inactive" href="javascript:"> Inactive </a></li>
              </ul>
            </div>
          </div>
          <div class="row">
            <div class="col s12 m6 "><h4 class="card-title">{{ $page->title ?? ''}} List</h4></div>
          </div>
          <div class="row">
            <div class="card-content data-table-container">
              <form id="page-form" name="page-form">

              </form>
            </div>
          </div>
          <!-- </div> -->
          <div id="view-borderless-table">
            <div class="row">
              <div class="col s12">
                <table id="data-table-eia" class="display data-tables" data-url="{{ $page->route }}" data-form="page" data-length="10">
                  <thead>
                    <tr>
                      <th width="20px" data-orderable="false" data-column="DT_RowIndex"> No </th>
                      <th width="300px" data-orderable="false" data-column="permit_code">Permit ID</th>
                      <th width="150px" data-orderable="false" data-column="project_id">Project ID</th>                      
                      <th width="200px" data-orderable="true" data-column="certificate_number"> Environmental Approval/Certificate Number </th>
                      <th width="250px" data-orderable="false" data-column="status"> Status of the Permit</th>
                      <th width="200px" data-orderable="true" data-column="comment"> Remarks/Comments </th>
                      <th width="150px" data-orderable="false" data-column="date_of_approval"> Date of Approval </th>          
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div><!-- START RIGHT SIDEBAR NAV -->
@include('projects.full_name')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('admin/vendors/data-tables/js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js')}}"></script>
<script src="{{asset('admin/vendors/data-tables/js/dataTables.select.min.js')}}"></script>
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
@endsection

@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/js/custom/permits/permits.js')}}"></script>
@endpush

