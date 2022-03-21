@extends('auth.auth_app')

@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('admin/css/pages/forgot.css')}}">
@endsection

@section('content')
    <div id="login-page" class="row">
        
        <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
            <form method="POST" action="{{ route('forget.password.post') }}" id="resetPasswordmailForm" >
                @csrf
                <div class="row">
                    <div class="input-field col s12">
                        <h5 class="ml-4">Forgot Password</h5>
                        @include('layouts.success')
                        @include('layouts.error')
                   </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                    <i class="material-icons prefix pt-2">person_outline</i>
                    <input id="email" type="email"  name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <label for="email" class="center-align">Email</label>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s12">
                    <button type="submit" class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12 mb-1" id="submit-btn"> E-mail me reset instructions</button>
                    </div>
                </div>
                <div class="row">
                    <div class="input-field col s6 m6 l6">
                    <p class="margin medium-small"><a href="{{url('login')}}">Never mind, go back</a></p>
                    </div>
                    <div class="input-field col s6 m6 l6">
                    <!-- <p class="margin right-align medium-small"><a href="">Register</a></p> -->
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('page-scripts')
<script type="text/javascript">

$("#submit-btn").on("click", function(event){
    event.preventDefault();
    $('#submit-btn').html('Please Wait...');
    $("#submit-btn"). attr("disabled", true);
    $( "#resetPasswordmailForm" ).submit();
});
// $(".print-error-msg").delay(1000).addClass("in").toggle(true).fadeOut(3000);
$(".card-alert .close").click(function(){$(this).closest(".card-alert").fadeOut("slow")});
</script>
@endpush


 
