@extends('layouts.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/vendors/quill/quill.snow.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/app-sidebar.css')}}">
  <link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/app-email.css')}}">
@endsection

@section('content')

@section('breadcrumb')
<div class="col s12 m6 l6"><h5 class="breadcrumbs-title"><span>{{ Str::plural($page->title) ?? ''}}</span></h5></div>
<div class="col s12 m6 l6 right-align-md">
  <ol class="breadcrumbs mb-0">
    <li class="breadcrumb-item"><a href="{{ url('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ url('projects') }}">Projects</a></li>
    <li class="breadcrumb-item"><a href="{{ url($page->projectRoute) }}">{{ Str::limit(strip_tags($document->eia->project->name), 20) ?? 'Show' }}</a></li>
    <li class="breadcrumb-item"><a href="{{ url($page->eiaRoute) }}">{{ Str::limit(strip_tags($document->eia->code_id), 20) ?? 'Show' }}</a></li>
    <li class="breadcrumb-item active">{{ Str::limit(strip_tags($document->document_number), 20) ?? 'Show' }}</li>
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
            <h6 class="media-heading"><span>Project: </span><span class="users-view-name">{{ $document->eia->project->name ?? ''}} </span></h6>
            <h5 class="media-heading"><span>EIA Id: </span><span class="users-view-name">{{ $document->eia->code_id ?? ''}} </span></h5>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- users view media object ends -->
  <!-- users view card data start -->
  <div class="card">
    <div class="card-content">
      <div class="row">
        <div class="col s12 m4">
          <h6 class="mb-2 mt-2"><i class="material-icons">info_outline</i> {{ Str::plural($page->title) ?? ''}} Details</h6>
          @isset($document->latestFile)
            <img src="{{$document->latestFile->file_name}}" class="responsive-img" style="max-width: 75% !important" alt="">
                <!-- <a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange z-depth-4 mt-2" href="{{ route('documents.file.download', ['document' => $document->latestFile->name])}}" style="margin-top: 10px; !important">Download</a>  -->
                @if($document->is_assigned == 0)
                  <p id=""><a class="waves-effect waves-light btn gradient-45deg-purple-deep-orange z-depth-4 mt-2 assign-task" id="" data-id="{{$document->id}}" href="javascript:" style="margin-top: 10px; !important">Assign Task</a></p>                  
                @else                
                <div class="col s12 m12 card-width" id="taskDetailsDiv">
                  <h6><a href="javascript:" class="mt-5">Task Details</a></h6>
                  <div class="card-panel mt-2">
                    <p>{{ $document->assign->details}}</p>
                    <div class="row mt-8">
                      <div class="col s2">
                        <img src="{{ $document->assign->assignedTo->profile}}" width="40" alt="news" class="circle responsive-img mr-3">
                      </div>
                      <div class="col s3 p-0 mt-1"><span class="pt-2">{{ $document->assign->assignedTo->name}}</span></div>
                      <div class="col s7 mt-1 right-align">
                        <a href="javascript:" class="assign-task"><span class="material-icons"> edit </span></a>
                      </div>
                    </div>
                  </div>
                </div>
                @endif
          @endisset
        </div>
        <div class="col s12 m4" style="margin-top: 40px">
          <table class="striped">
            <tbody>
              <tr>
                <td> Document Number: </td>
                <td>{{ $document->document_number ?? ''}}</td>
              </tr>
              <tr>
                <td>Title of Document: </td>
                <td>{{ $document->title ?? ''}}</td>
              </tr>
              <tr>
                <td>Uploaded by: </td>
                <td>{{ $document->uploadedBy->name ?? ''}}</td>
              </tr> 
              <tr>
                <td>Date of Entry: </td>
                <td>{{ $document->date_of_entry ?? ''}}</td>
              </tr>
              <tr>
                <td>Status: </td>
                <td>{!! App\Helpers\HtmlHelper::statusText($document->stage_id, $document->status) !!}</td>
              </tr>
              <tr>
                <td>Brief Description: </td>
                <td>{{ $document->brief_description ?? ''}}</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="col s12 m4" style="margin-top: 10px">
          <div class="row commentContainer">  
            <div class="input-field col m10 s12 commentArea">
              {!! Form::textarea('comment', '',  ['id' => 'documentComment', 'class' => 'materialize-textarea commentField']) !!}
              <label for="comment" class="label-placeholder active"> Comments </label>  
              <div id="documentComment-error-{{$document->id}}" class="error documentComment-error" style="display:none;"></div>
            </div>
            <div class="input-field col m2 s12" style="margin-top: 37px; ! important">
              <a href="javascript:" class="text-sub save-comment-btn" data-id="{{$document->id}}"><i class="material-icons mr-2"> send </i></a>
            </div>
          </div>
          <!-- Content Area Starts -->
          <div class="app-email" id="latestComment{{$document->id}}"></div>
          @forelse ($document->comments as $comment)
            <div class="app-email" id="docCommentsDiv{{$document->id}}">
              <div class="content-area">
                <div class="app-wrapper">
                  <div class="card card card-default scrollspy border-radius-6 fixed-width">
                    <div class="card-content p-0 pb-2">
                      <div class="collection email-collection">
                        <div class="email-brief-info collection-item animate fadeUp ">
                          <a class="list-content" href="javascript:">
                            <div class="list-title-area">
                              <div class="user-media">
                                <img src="{{$comment->commentedBy->profile}}" alt="" class="circle z-depth-2 responsive-img avtar">
                                <div class="list-title">{{$comment->commentedBy->name}}</div>
                              </div>
                            </div>
                            <div class="list-desc">{{$comment->comment}}</div>
                          </a>
                          <div class="list-right">
                            <div class="list-date">{{$comment->created_at->format('M d, h:i A')}} </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @empty
          @endforelse
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
            <!-- <a class="dropdown-settings btn mb-1 waves-effect waves-light cyan" href="#!" data-target="dropdown1" id="customerListBtn">
              <i class="material-icons hide-on-med-and-up">settings</i>
              <span class="hide-on-small-onl">Order By</span>
              <i class="material-icons right">arrow_drop_down</i></a>
              <ul class="dropdown-content" id="dropdown1" tabindex="0">
                <li tabindex="0"><a class="grey-text text-darken-2 listBtn" data-type="active" href="javascript:" > Latest </a></li>
                <li tabindex="0"><a class="grey-text text-darken-2 listBtn" data-type="inactive" href="javascript:"> Last Commented </a></li>
              </ul>
            </a> -->
            {!! App\Helpers\HtmlHelper::createLinkButton(url($page->route.'/'.$document->id.'/create'), 'Add New Document') !!}
          </div>
        </div>
        <div class="row">
          <div class="col s12 m6 ">
            <h4 class="card-title"> Documents List</h4>
          </div>
        </div>
      </div>
      <div class="row">
        {!! Form::open(['class'=>'ajax-submit', 'id' => Str::camel($page->title).'Form', "enctype" => "multipart/form-data"]) !!}
        {{ csrf_field() }}
        {!! Form::hidden('documentId', $document->id ?? '', ['id' => 'documentId']); !!}
        {{ Form::close() }}
        <div class="col s10" id="subDocumentsDiv"></div>
      </div>
    </div>
  </div>
  <!-- users view card details ends -->
</div>
<!-- users view ends -->
@include('documents.assign_task')
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<!-- <script src="{{asset('admin/vendors/sortable/jquery-sortable-min.js')}}"></script> -->
<!-- <script src="{{asset('admin/vendors/quill/quill.min.js')}}"></script> -->
@endsection

@push('page-scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="{{asset('admin/js/custom/documents/documents.js')}}"></script>
<script></script>
@endpush