@extends('layouts.guest', ['title' => 'Reset Password'])

@push('css_after')
    <link rel="stylesheet" href="{{ asset('public/css/custom/login.css') }}"/>
@endpush

@push('css')

<style>

.login-resistration-area .main-login-area.login-res-v2::before {
    background-image: url({{ asset(config('configs.login_backgroud_image')) }})
}

</style>
@endpush

@section('content')

<div class="main-content">
		<div class="logo_img">
			<a href="{{ route('home') }}">
				<img src="{{ asset(config('configs.site_logo')) }}" alt="Logo Image" class="img img-responsive">
			</a>
		</div>

			<h3 class="sho_web d-none d-md-block">{{ __('auth.Reset your password') }}</h3>

			<form  method="POST" action="{{ route('password.update') }}"  id="content_form" class="customer-input" >

			@csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">
                <input required name="email" type="text" placeholder="{{ __('auth.Enter email address') }}" autofocus class="" autocomplete="current-password">
				<input required name="password" type="password" placeholder="{{ __('auth.Password') }}" id="password" autofocus class="" autocomplete="current-password">
				<input required name="password_confirmation" type="password" placeholder="{{ __('auth.Confirm Password') }}" id="password_confirmation" autofocus class="" autocomplete="current-password">



				<div class="forgot-pass">
						<a href="{{ route('login') }}">
							{{ __('auth.Back to login') }}
						</a>
				</div>


				<button type="submit" class="login-res-btn submit">{{ __('auth.Reset Password') }}</button>
				<button type="button" class="login-res-btn submitting" style="display:none" disabled>{{ __('auth.Resetting Password') }}...</button>
			</form>

	</div>


@stop


@push('js_after')
    <script src="{{ asset('public/js/login.js') }}"></script>
@endpush


