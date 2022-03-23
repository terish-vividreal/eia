@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')

@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endsection


@section('content')

@section('breadcrumb')
<div class="col s12 m6 l6"><h5 class="breadcrumbs-title"><span>{{ Str::plural($page->title) ?? ''}}</span></h5></div>
<div class="col s12 m6 l6 right-align-md">
    <ol class="breadcrumbs mb-0">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Dashboard</a></li>        
        <li class="breadcrumb-item"><a href="{{ url('projects') }}">Projects</a></li>        
        <li class="breadcrumb-item"><a href="{{ url($page->projectRoute) }}">{{ Str::limit(strip_tags($project->name), 20) ?? 'Show' }}</a></li>
        <li class="breadcrumb-item active">Create {{ Str::plural($page->title) ?? ''}}</li>
    </ol>
</div>
@endsection
<div class="seaction">
    
  <!-- users view media object ends -->
    <!-- <div class="card">
        <div class="card-content">
            <p class="caption mb-0">Create {{ $page->title ?? ''}} page description.</p>
        </div>
    </div> -->
    <div class="card-panel">
        <div class="row">
        <div class="col s12 m12">
            <div class="display-flex media">
            <div class="media-body">
                <span>Project Title</span>
                <h6 class="media-heading"><span class="users-view-name"> {{ $project->name ?? ''}} </span></h6>
                <span>ID:</span>
                <span class="users-view-id">{{ $project->project_code_id ?? ''}}</span>
            </div>
            </div>
        </div>
        </div>
    </div>
    <!--Basic Form-->
    <!-- jQuery Plugin Initialization -->
    <div class="row">
        <!-- Form Advance -->
        <div class="col s12 m12 l12">
            <div id="Form-advance" class="card card card-default scrollspy">
                <div class="card-content">
                    <div class="card-title">
                        <div class="row right">
                            <div class="col s12 m12 ">
                                {!! App\Helpers\HtmlHelper::listLinkButton(url($page->projectRoute), 'Back') !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m6 ">
                                <h4 class="card-title"> {{ $page->title ?? ''}} Profile Form</h4>
                            </div>
                        </div>
                    </div>
                    @include('layouts.error') 
                    @include('layouts.success') 
                    {!! Form::open(['class'=>'ajax-submit','id'=> Str::camel($page->title).'Form']) !!}
                        {{ csrf_field() }}
                        {!! Form::hidden('pageTitle', Str::camel($page->title), ['id' => 'pageTitle'] ); !!} 
                        {!! Form::hidden('pageRoute', $page->route, ['id' => 'pageRoute'] ); !!}
                        {!! Form::hidden('projectRoute', $page->projectRoute, ['id' => 'projectRoute'] ); !!}
                        {!! Form::hidden('projectId', $project->id ?? '', ['id' => 'projectId'] ); !!}
                        {!! Form::hidden('eiaId', $eia->id ?? '', ['id' => 'eiaId'] ); !!}

                        <div class="row">
                            <div class="input-field col m6 s12">
                                {!! Form::text('codeId', $eia->code_id ?? '', array('id' => 'codeId')) !!}
                                <label for="codeId" class="label-placeholder active"> EIA Id <span class="red-text">*</span></label>
                            </div>
                            <div class="input-field col m6 s12">
                                {!! Form::text('dateOfEntry', $eia->date_of_entry ?? '', array('id' => 'dateOfEntry')) !!}
                                <label for="dateOfEntry" class="label-placeholder active"> Date of Entry <span class="red-text">*</span></label>
                            </div>   
                        </div>
                        <div class="row">
                            
                            <div class="input-field col m3 s12">
                                {!! Form::select('stage', $variants->stages, $eia->stage_id ?? '', ['id' => 'stage', 'class' => 'select2 browser-default', 'placeholder'=>'Please select Stage']) !!}
                            </div>
                            <div class="input-field col m3 s12">
                                {!! Form::select('status', $variants->documentStatuses, $eia->status ?? '', ['id' => 'status', 'class' => 'select2 browser-default', 'placeholder'=>'Please select Status']) !!}
                            </div>
                            <div class="input-field col m6 s12">
                                {!! Form::text('projectTeamLeader', $eia->project_team_leader ?? '', array('id' => 'projectTeamLeader')) !!}
                                <label for="projectTeamLeader" class="label-placeholder active"> Project Team Leader <span class="red-text">*</span></label>
                            </div> 
                        </div>
                        <div class="row">  
                            <div class="input-field col m6 s12">
                                {!! Form::text('costOfDevelop', $eia->cost_of_develop ?? '', array('id' => 'costOfDevelop')) !!}
                                <label for="costOfDevelop" class="label-placeholder active"> Cost Of Proposed Develop </label>    
                            </div>
                            <div class="input-field col m6 s12">
                                {!! Form::textarea('address', $eia->address ?? '',  ['id' => 'address', 'class' => 'materialize-textarea']) !!}
                                <label for="briefDescription" class="label-placeholder active"> Address </label>    
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col m6 s12">
                                {!! Form::text('latitude', $eia->latitude ?? '', array('id' => 'latitude')) !!}
                                <label for="latitude" class="label-placeholder active"> GPS: Latitude <span class="red-text">*</span></label>
                            </div>   
                            <div class="input-field col m6 s12">
                                {!! Form::text('longitude', $eia->longitude ?? '', array('id' => 'longitude')) !!}
                                <label for="longitude" class="label-placeholder active"> GPS: Longitude <span class="red-text">*</span></label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="input-field col s12">
                                {!! App\Helpers\HtmlHelper::submitButton('Submit', 'formSubmitButton') !!}
                                {!! App\Helpers\HtmlHelper::resetButton('Reset', 'formResetButton') !!}
                            </div>
                        </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div><!-- START RIGHT SIDEBAR NAV -->
@endsection


{{-- vendor scripts --}}
@section('vendor-script')

@endsection

@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/js/custom/eia/eia.js')}}"></script>
<script>

</script>
@endpush

