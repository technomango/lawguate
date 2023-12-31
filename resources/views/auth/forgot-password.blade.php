@extends('layouts.guest', ['title' => 'Forgot Password'])

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

			<h3 class="sho_web d-none d-md-block">{{ __('auth.Forgot Your Password?') }}</h3>

            <p> {{ __('auth.No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}</p>





			<form  method="POST" action="{{ route('password.email') }}"  id="content_form" class="customer-input" >

			@csrf

				<input required name="email" type="text" placeholder="{{ __('auth.Enter email address') }}" id="email" autofocus class="" autocomplete="current-password">



				<div class="forgot-pass">
						<a href="{{ route('login') }}">
							{{ __('auth.Back to login') }}
						</a>
				</div>


				<button type="submit" class="login-res-btn submit">{{ __('auth.Send Instruction') }}</button>
				<button type="button" class="login-res-btn submitting" style="display:none" disabled>{{ __('auth.Sending Instructions') }}...</button>
			</form>


	</div>



@stop


@push('js_after')

@endpush

