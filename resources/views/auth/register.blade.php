@extends('layouts.auth')
@section('page-title')
    {{__('Register')}}
@endsection
@php
    $logo=asset(Storage::url('uploads/logo/'));
@endphp
@push('custom-scripts')
    @if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

@section('content')
    <div class="login-contain">
        <div class="login-inner-contain">
            <a class="navbar-brand" href="#">
                <img src="{{$logo.'/logo.png'}}" class="navbar-brand-img big-logo" alt="logo">
            </a>
            <div class="login-form">
                <div class="page-title"><h5>{{__('Inscrire')}}</h5></div>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-control-label">{{__('Nom Sociéte')}}</label>
                        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-control-label">{{__('Email')}}</label>
                        <input class="form-control @error('email') is-invalid @enderror" id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div class="invalid-feedback">
                            {{__('Merci de renseigner votre email')}}
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-control-label">{{__('Mot de passe')}}</label>
                        <input id="password" type="password" data-indicator="pwindicator" class="form-control pwstrength @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <div id="pwindicator" class="pwindicator">
                            <div class="bar"></div>
                            <div class="label"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="form-control-label">{{__('Confirmation mot de passe')}}</label>
                        <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    </div>

                {{-------------- ReCaptcha start---------}}
                    @if(env('RECAPTCHA_MODULE') == 'yes')
                        <div class="form-group col-lg-12 col-md-12 mt-3">
                            {!! NoCaptcha::display() !!}
                            @error('g-recaptcha-response')
                            <span class="small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    @endif

                    {{-------------- ReCaptcha end---------}}
                    <div class="custom-control custom-checkbox remember-me-text">
                        <input type="checkbox" class="custom-control-input" name="agree" id="agree">
                        <label class="custom-control-label" for="agree">{{__('Je suis daccord avec les termes et conditions')}}</label>
                    </div>
                    <button type="submit" class="btn-login">{{__('Inscrire')}}</button>
                    <div class="or-text">{{__('Ou')}}</div>
                    <small class="text-muted">{{__("Vous avez déjà un compte?")}}</small>
                    <a href="{{ route('login') }}" class="text-xs text-primary">{{__('Connexion')}}</a>
                </form>
            </div>

            <h5 class="copyright-text">
                {{(Utility::getValByName('footer_text')) ? Utility::getValByName('footer_text') :  __('Copyright EGest') }} {{ date('Y') }}
            </h5>
            <div class="all-select">
                <a href="#" class="monthly-btn">
                    <span class="monthly-text py-0">{{__('Changer de langue')}}</span>
                    <select class="select-box select2" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);" id="language">
                        @foreach(Utility::languages() as $language)
                            <option @if($lang == $language) selected @endif value="{{ route('register',$language) }}">{{Str::upper($language)}}</option>
                        @endforeach
                    </select>
                </a>
            </div>
        </div>
    </div>
@endsection
