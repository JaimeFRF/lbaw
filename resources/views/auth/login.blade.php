@extends('layouts.app')

@section('css')
<link href="{{ url('css/login_register.css') }}" rel="stylesheet">
@endsection


@section('content')
    <section class="d-flex justify-content-center m-5" id = "content">
        <script src="{{ asset('js/script.js') }}"></script>
        <div class="card w-50 d-flex flex-column align-items-center">
            <form class = "card-body w-75"  id = "login-form" method="POST" action="{{ route('login') }}">
                {{ csrf_field() }}

                <div class="form-group d-flex flex-column mt-3">
                    <label for="email"> <h6>Email</h6> </label>
                    <input class = "form-control"  id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    @if ($errors->has('email'))
                        <span class="error">
                        {{ $errors->first('email') }}
                        </span>
                    @endif
                </div>

                <div class="form-group d-flex flex-column mt-3" id="pwd">
                    <label for="password" > <h6>Password</h6> </label>
                    <input  class = "form-control" id="password" type="password" name="password" required>
                    <span title="Show password">
                        <i class="bi bi-eye-slash" id="togglePassword"></i>
                    </span> 
                    @if ($errors->has('password'))
                        <span class="text-danger">{{ $errors->first('password') }}</span>
                    @endif
                </div>

                <div class="form-group d-flex justify-content-between align-items-center mt-2">
                    <label>
                        <input  class = "m-1" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                    </label>
                </div>

                <div class="form-group d-flex mt-3">
                    <button class = "btn btn-primary w-50 m-2" type="submit">Login</button>
                </div>
                <div class="form-group d-flex mt-3" >
                    <span>Don't have an account?</span>
                    <a class="btn p-0 btn-link mt-8 text-decoration-underline" href="{{route('register')}}" style="margin-left: 5px;">Sign up!</a>
                </div>
            </form>
        </div>
    </section>
@endsection