@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')

@endsection

{{-- page style --}}
@section('page-style')
    <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/page-users.css')}}">
@endsection


@section('content')

@section('breadcrumb')
<div class="col s12 m6 l6"><h5 class="breadcrumbs-title"><span>{{ Str::plural($page->title) ?? ''}}</span></h5></div>
<div class="col s12 m6 l6 right-align-md">
    <ol class="breadcrumbs mb-0">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ url('projects') }}">Projects</a></li>
        <li class="breadcrumb-item"><a href="{{ url($page->projectRoute) }}">{{ Str::limit(strip_tags($eia->project->name), 20) ?? 'Show' }}</a></li>
        <li class="breadcrumb-item active">{{ Str::limit(strip_tags($eia->code_id), 20) ?? 'Show' }}</li>
    </ol>
</div>
@endsection
<!-- users view start -->
<div class="section users-view">
  <!-- users view media object start -->
  <div class="card-panel">
      
    <div class="row">
      <div class="col s12 m7">
        <div class="display-flex media">
          <div class="media-body">
            <h6 class="media-heading"><span>Project: </span><span class="users-view-name">{{ $eia->project->name ?? ''}} </span></h6>
            <h5 class="media-heading"><span>EIA Id: </span><span class="users-view-name">{{ $eia->code_id ?? ''}} </span></h5>
          </div>
        </div>
      </div>
      <!-- <div class="col s12 m5 quick-action-btns display-flex justify-content-end align-items-center pt-2">
        <a href="{{ url($page->route.'/'.$eia->id.'/edit')}}" class="btn-small indigo">Edit </a>
        <a href="{{ url($page->route)}}" class="btn-small indigo">Back </a>
      </div> -->
    </div>
  </div>
  <!-- users view media object ends -->
  <!-- users view card data start -->
  <div class="card">
    <div class="card-content">
      <div class="row">
        <div class="col s12 m6">
            <h6 class="mb-2 mt-2"><i class="material-icons">info_outline</i> {{ Str::plural($page->title) ?? ''}} Details</h6>
            <table class="striped">
                <tbody>
                <tr>
                    <td>EIA ID:</td>
                    <td>{{ $eia->code_id ?? ''}}</td>
                </tr>
                <tr>
                    <td>EIA Status:</td>
                    <td> {!! App\Helpers\HtmlHelper::statusText($eia->stage_id, $eia->status) !!}</td>
                </tr>
                <tr>
                    <td>Project Team Leader :</td>
                    <td>{{ $eia->project_team_leader ?? ''}}</td>
                </tr> 
                </tbody>
            </table>
        </div>
        <div class="col s12 m6" style="margin-top: 55px">
            <table class="striped">
                <tbody>
                <tr>
                    <td>Project ID:</td>
                    <td>{{ $eia->project->project_code_id ?? ''}}</td>
                </tr>
                <tr>
                    <td>Date of Entry:</td>
                    <td>{{ $eia->formatted_date_of_entry ?? ''}}</td>
                </tr>
                <tr>
                    <td>Cost Of Proposed Develop:</td>
                    <td>{{ number_format($eia->cost_of_develop) ?? ''}}</td>
                </tr>
                </tbody>
            </table>
        </div>
      </div>
    </div>
  </div>
  <!-- users view card data ends -->

  <!-- users view card details start -->
  <div class="card">
    <div class="card-content">
        <div class="card-title">
            <div class="row right">
            <div class="col s12 m12 ">
                {!! App\Helpers\HtmlHelper::createLinkButton(url($page->route.'/'.$eia->id.'/documents/create'), 'Add New Document') !!}
            </div>
            </div>
        </div>




      <div class="row">
        <div class="col s12">

        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
  <!-- users view card details ends -->

</div>
<!-- users view ends -->
@endsection


{{-- vendor scripts --}}
@section('vendor-script')

@endsection

@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/js/custom/project/project.js')}}"></script>
<script>

</script>
@endpush

