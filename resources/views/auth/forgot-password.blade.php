@extends('layouts.auth')

@section('page-title')
       {{ __('Reset Password') }}
@endsection

@push('custom-scripts')
    @if(env('RECAPTCHA_MODULE') == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif
@endpush

 @section('content')
     <div class="login-contain">
         <div class="login-inner-contain">
             <div class="login-form">
        <div class="page-title"><h5>{{__('Réinitialiser le mot de passe')}}</h5></div>
        @if(session('status'))
            <div class="alert alert-primary">
                {{ session('status') }}
            </div>
        @endif
        <p class="text-xs text-muted">{{__('')}}</p>
        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <label for="email" class="form-control-label">{{ __('Merci de contacter cette email pour Réinitialiser le mot de passe :') }}</label>
                
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


            <button href="mailto:contact@egest.ma" class="btn-login">{{ __('contact@egest.ma') }}</button>
            <div class="or-text">{{__('OR')}}</div>
            <div class="text-xs text-muted text-center">
                {{__("Back to")}} <a href="{{route('login')}}">{{__('Login')}}</a>
            </div>
        </form>
    </div>
         </div>
     </div>
@endsection




