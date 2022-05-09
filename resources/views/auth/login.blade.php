@extends('auth.auth_app')

@section('content')
    <div id="login-page" class="row">
        
        <div class="col s12 m6 l4 z-depth-4 card-panel border-radius-6 login-card bg-opacity-8">
            <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="row">
                <div class="input-field col s12" style="text-align:center">
                    <img src="{{ asset('admin/images/logo/logo.png') }}" alt="materialize logo">
                </div>
                <div class="input-field col s12">
                <h5 class="ml-4" style="text-align:center">Sign in </h5>
                </div>
            </div>
            @include('layouts.success')
            @include('layouts.error')
            <div class="row margin">
                <div class="input-field col s12">
                <i class="material-icons prefix pt-2">person_outline</i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" class="validate" autocomplete="off">
                <label for="email" class="label-placeholder">User ID </label>
                </div>
            </div>
            <div class="row margin">
                <div class="input-field col s12">
                <i class="material-icons prefix pt-2">lock_outline</i>
                <input id="password" type="password" name="password" class="validate" autocomplete="off">
                <label for="password" class="label-placeholder">Password </label>
                </div>
            </div>
            <div class="row">
                <div class="col s12 m12 l12 ml-2 mt-1">
                <p>
                    <label>
                    <input type="checkbox" name="remember_me" id="remember_me" />
                    <span>Remember Me</span>
                    </label>
                </p>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s12">
                <button class="btn waves-effect waves-light border-round gradient-45deg-purple-deep-orange col s12" type="submit" name="action">Login </button>
                </div>
            </div>
            <div class="row">
                <div class="input-field col s6 m6 l6">
                <!-- <p class="margin medium-small"><a href="javascript:">Register Now!</a></p> -->
                </div>
                <div class="input-field col s6 m6 l6">
                <p class="margin right-align medium-small"><a href="{{ url('forget-password') }}">Forgot password ?</a></p>
                </div>
            </div>
            </form>
        </div>
    </div>
@endsection

@push('page-scripts')
<script type="text/javascript">
    $(".alert-danger").delay(1000).addClass("in").toggle(true).fadeOut(3000);
    $(".card-alert .close").click(function(){$(this).closest(".card-alert").fadeOut("slow")});
</script>
@endpush


 
