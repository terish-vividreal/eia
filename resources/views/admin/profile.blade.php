@extends('layouts.admin.app')

{{-- page title --}}
@section('seo_title', Str::plural($page->title) ?? '') 
@section('search-title') {{ $page->title ?? ''}} @endsection


{{-- vendor styles --}}
@section('vendor-style')
@endsection

{{-- page style --}}
@section('page-style')
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/css/pages/page-users.css')}}">
@endsection

@section('content')

@section('breadcrumb')
<div class="col s12 m6 l6"><h5 class="breadcrumbs-title"><span>{{ Str::plural($page->title) ?? ''}}</span></h5></div>
<div class="col s12 m6 l6 right-align-md">
    <ol class="breadcrumbs mb-0">
        <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url(ROUTE_PREFIX.'/'.$page->route) }}">{{ Str::plural($page->title) ?? ''}}</a></li>
        <li class="breadcrumb-item active">List</li>
    </ol>
</div>
@endsection
<!-- edit profile start -->
<div class="section users-edit">
    <div class="card">
        <div class="card-content">
            <!-- <div class="card-body"> -->
            <ul class="tabs mb-2 row">
                <li class="tab">
                    <a class="display-flex align-items-center active" id="account-tab" href="#account">
                        <i class="material-icons mr-1">person_outline</i><span>Profile</span>
                    </a>
                </li>
                <li class="tab">
                    <a class="display-flex align-items-center" id="information-tab" href="#information">
                        <i class="material-icons mr-2">settings</i><span>Account</span>
                    </a>
                </li>
            </ul>
            <div class="divider mb-3"></div>
            <div class="row">

                <div class="col s12" id="account">
                    
                    <!-- users edit media object start -->
                    <div class="media display-flex align-items-center mb-2">
                        <a class="mr-2" href="#">
                            {{ Auth::user()->profile_photo }}
                            <img src="{{auth()->user()->profile_url}}" alt="users avatar" class="z-depth-4 circle" height="64" width="64">
                        </a>
                        <div class="media-body">
                            <h5 class="media-heading mt-0">Profile</h5>
                            <div class="user-edit-btns display-flex">
                                <a href="#" class="btn-small indigo">Change</a>
                                <a href="#" class="btn-small btn-light-pink">Reset</a>
                            </div>
                        </div>
                    </div>
                    <!-- users edit media object ends -->
                    <!-- users edit account form start -->
                    @include('layouts.error') 
                    @include('layouts.success') 
                    {!! Form::open(['class'=>'ajax-submit','id'=> Str::camel($page->title).'Form']) !!}
                        {{ csrf_field() }}
                        {!! Form::hidden('user_id', $user->id ?? '', ['id' => 'user_id'] ); !!}
                        <div class="row">
                            <div class="col s12 m6">
                                <div class="row">
                                    <div class="col s12 input-field">
                                        {!! Form::text('name', $user->name ?? '', array('id' => 'name')) !!}
                                        <label for="name" class="label-placeholder active"> Name <span class="red-text">*</span></label>
                                    </div>
                                    <div class="col s12 input-field">
                                        {!! Form::text('email', $user->email ?? '', array('id' => 'email')) !!}
                                        <label for="name" class="label-placeholder active"> E-mail <span class="red-text">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12 m6">
                                <div class="row">
                                    <div class="col s4 input-field">
                                        {!! Form::select('phone_code', $variants->phonecode , $user->phone_code ?? '', ['id' => 'phone_code', 'class' => 'select2 browser-default', 'placeholder'=>'Please select phone code']) !!}
                                    </div>
                                    <div class="col s8 input-field">
                                        {!! Form::text('mobile', $user->mobile ?? '', array('id' => 'mobile')) !!}
                                        <label for="name" class="label-placeholder active"> Mobile</label>
                                    </div>
  
                                </div>
                            </div>
                            <!-- User Permissions  -->
                            <!-- <div class="col s12">
                                <table class="mt-1">
                                    <thead>
                                        <tr>
                                            <th>Module Permission</th>
                                            <th>Read</th>
                                            <th>Write</th>
                                            <th>Create</th>
                                            <th>Delete</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Users</td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Articles</td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Staff</td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" checked />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label>
                                                    <input type="checkbox" />
                                                    <span></span>
                                                </label>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> -->
                            <div class="col s12 display-flex justify-content-end mt-3">
                                <button type="submit" class="btn indigo" id="submit-btn"> Save changes</button>
                                <button type="button" class="btn btn-light" id="form-reset-btn">Reset</button>
                            </div>
                        </div>
                    {{ Form::close() }}
                    <!-- users edit account form ends -->
                </div>
                <div class="col s12" id="information">
                    <!-- users edit Info form start -->
                    @include('layouts.error') 
                    @include('layouts.success')
                    {!! Form::open(['class'=>'ajax-submit', 'id'=>'changePasswordForm']) !!}
                        <div class="row">
                            <div class="col s12 m8">
                                <div class="row">
                                    <div class="col s12"><h6 class="mb-4"><i class="material-icons mr-1">lock</i>Manage Password</h6></div>
                                    <div class="col s12 input-field">
                                        {!! Form::password('old_password',  ['id' => 'old_password']) !!}
                                        <label for="old_password"> Old Password <span class="red-text">*</span></label>
                                    </div>
                                    <div class="col s12 input-field">
                                        {!! Form::password('new_password',  ['id' => 'new_password']) !!}
                                        <label for="new_password"> New Password <span class="red-text">*</span></label>
                                    </div>
                                    <div class="col s12 input-field">
                                        {!! Form::password('new_password_confirmation',  ['id' => 'new_password_confirmation']) !!}
                                        <label for="new_password_confirmation"> Confirm Password <span class="red-text">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col s12 m12">
                                <div class="row">
                                    <div class="col s12 display-flex justify-content-end mt-3">
                                        <button type="submit" class="btn indigo" id="password-submit-btn"> Save changes</button>
                                        <button type="button" class="btn btn-light" id="password-form-reset-btn" onclick="resetChangepasswordForm()">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {!! Form::close() !!}
                    <!-- users edit Info form ends -->
                </div>
            </div>
            <!-- </div> -->
        </div>
    </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')

@endsection

@push('page-scripts')
<!-- <script src="{{ asset('admin/js/scripts/page-users.js') }}"></script> -->

<script>
$('#phone_code').select2({ placeholder: "Please select phone code", allowClear: true});

if ($("#{{Str::camel($page->title)}}Form").length > 0) {
  var validator = $("#{{Str::camel($page->title)}}Form").validate({ 
    rules: {
        name: { 
            required: true, 
            maxlength: 200
        },
        mobile: { 
            minlength:3, 
            maxlength:15, 
            // emailFormat:true, Needs confirmation from Sukhesh regarding number format
            remote: { url: "{{ url(ROUTE_PREFIX.'/common/is-unique-mobile') }}", type: "POST",
                data: {
                    user_id: function () { return $('#user_id').val(); }
                }
            },
        },
        email: { 
            required: true, 
            email: true, 
            emailFormat:true,
            remote: { url: "{{ url(ROUTE_PREFIX.'/common/is-unique-email') }}", type: "POST",
                data: {
                    user_id: function () { return $('#user_id').val(); }
                }
            },
        },
    },
    messages: { 
      name: {
        required: "Please enter name",
        maxlength: "Length cannot be more than 200 characters",
      },
      mobile: {
        maxlength: "Length cannot be more than 15 numbers",
        minlength: "Length must be 3 numbers",
        remote: "The Mobile has already been taken"
      },
      email: {
        required: "Please enter E-mail",
        email: "Please enter a valid E-mail address",
        emailFormat: "Please enter a valid E-mail address",
        remote: "The E-mail has already been taken"
      },
    },
    submitHandler: function (form) {
      disableBtn("submit-btn");
      id            = $("#user_id").val();
      user_id       = "" == id ? "" : "/" + id;
      formMethod    = "" == id ? "POST" : "PUT";
      var forms = $("#{{Str::camel($page->title)}}Form");
      $.ajax({ url: "{{ url(ROUTE_PREFIX.'/'.$page->route) }}" + user_id, type: formMethod, processData: false, 
      data: forms.serialize(), dataType: "html",
      }).done(function (a) {
        enableBtn("submit-btn");
        var data = JSON.parse(a);
        if (data.flagError == false) {
          showSuccessToaster(data.message);
          setTimeout(function () { 
            window.location.href = "{{ url(ROUTE_PREFIX.'/'.$page->route) }}";                
          }, 2000);
        } else {
          showErrorToaster(data.message);
          printErrorMsg(data.error);
        }
      });
    },
    errorElement : 'div',
  })
}

jQuery.validator.addMethod("emailFormat", function (value, element) {
    return this.optional(element) || /[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/igm.test(value);
}, "Please enter a valid email address");  

$("#form-reset-btn").click(function (){
    validator.resetForm();
    $("#{{Str::camel($page->title)}}Form").find("input[type=text]").val("");
    $("#phone_code").val('').trigger('change');
});

if ($("#changePasswordForm").length > 0) {
    var changePasswordFormValidator = $("#changePasswordForm").validate({ 
      rules: {
        old_password: {
          required: true,
        },
        new_password: {
          required: true,
          minlength: 6,
          maxlength: 10,
        },
        new_password_confirmation: {
          equalTo: "#new_password"
        },
      },
      messages: { 
        old_password: {
          required: "Please enter old password",
        },
        new_password: {
          required: "Please enter new password",
          minlength: "Passwords must be at least 6 characters in length",
          maxlength: "Length cannot be more than 10 characters",
        },
        new_password_confirmation: {
          equalTo: "Passwords are not matching",
        }
      },
      submitHandler: function (form) {
        disableBtn('password-submit-btn');
        var forms   = $("#changePasswordForm");
        $.ajax({ url: "{{ url(ROUTE_PREFIX.'/update-password') }}", type: 'POST', processData: false, 
        data: forms.serialize(), dataType: "html",
        }).done(function (a) {
          var data = JSON.parse(a);
            resetChangePasswordForm();
            enableBtn('password-submit-btn');
          if (data.flagError == false) {
            showSuccessToaster(data.message);
            // setTimeout(function () { 
            //     window.location.href = "{{ url('admin/stores')}}";                    
            // }, 3000);
          } else {
            showErrorToaster(data.message);
            printErrorMsg(data.error);
          }
        });
      },
      errorElement : 'div',
    })
  }

  function resetChangePasswordForm() {
    changePasswordFormValidator.resetForm();
    $('#changePasswordForm').find("input[type=password], textarea, hidden").val("");
    $("#changePasswordForm label").removeClass("error");
    $("#changePasswordForm .label-placeholder").addClass('active');
  }
</script>
@endpush

