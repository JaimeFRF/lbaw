@extends('layouts.app')

@section('css')
    <link href="{{ url('css/login_register.css') }}" rel="stylesheet">
    <link href="{{ url('css/contextual_help.css') }}" rel="stylesheet">
@endsection

@section('title', 'Antiquus - Login')

@section('content')
    <section class="d-flex justify-content-center m-5" id="content">
        <div class="card w-50 d-flex flex-column align-items-center">
            <div class="d-flex">
                <div class="login-tab p-3 active" data-tab="user">User Login</div>
                <div class="login-tab p-3 ml-2" data-tab="admin">Admin Login</div>
            </div>

            <form class=" admin-login-fields card-body w-75" id="user-login-form" method="POST" action="{{ route('login') }}" data-current-tab="user">
                {{ csrf_field() }}

                <div class="user-login-fields">
                    <div class="form-group d-flex flex-column mt-3">
                        <label for="email"><h6>Email *</h6></label>
                        <input class="form-control"type="email" name="email" value="{{ old('email') }}" required autofocus onmouseover="getContextualHelp('password', 'email-help').show()" onmouseout="getContextualHelp('password', 'email-help').hide()">
                        @if ($errors->has('email'))
                            <span class="error">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>
                    <div id="email-help" class="help-message">Choose the email you want associate with your account.</div>


                    <div class="form-group d-flex flex-column mt-3" id="pwd">
                        <label for="password"><h6>Password *</h6></label>
                        <div class="input-group">
                            <input class="form-control" type="password" name="password" required onmouseover="getContextualHelp('password', 'password-help').show()" onmouseout="getContextualHelp('password', 'password-help').hide()">
                            <span title="Show password" class="toggle-password input-group-text">
                            <i class="bi bi-eye-slash" id="togglePassword"></i>
                            </span>
                        </div>
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>
                    <div id="password-help" class="help-message">Your password should be at least 10 characters long.</div>


                    <div class="form-group d-flex justify-content-between align-items-center mt-2">
                        <label>
                            <input class="m-1" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                        <a class="btn p-0 btn-link mt-8 text-decoration-underline mb-2 ml-2" href="{{route('recover_password')}}">Forgot your password?</a>
                    </div>

                    <div class="form-group d-flex justify-content-center mt-3">
                        <button class="btn btn-primary w-50 m-2" type="submit">Login</button>
                    </div>

                    <div class="form-group d-flex justify-content-center mt-3">
                        <span>Don't have an account? </span>
                        <a class="btn p-0 btn-link mt-8 text-decoration-underline mb-2 ml-2" href="{{ route('register') }}"> Sign up!</a>
                    </div>
                </div>
            </form>

            <form class="admin-login-fields card-body w-75" id="admin-login-form" method="POST" action="{{ route('admin-login') }}" style="display: none;">
                {{ csrf_field() }}
                <div class="user-login-fields">
                    <div class="form-group d-flex flex-column mt-3">
                        <label for="email"><h6>Email</h6></label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus>
                        @if ($errors->has('email'))
                            <span class="error">
                                {{ $errors->first('email') }}
                            </span>
                        @endif
                    </div>

                    <div class="form-group d-flex flex-column mt-3" id="pwd">
                        <label for="password"><h6>Password</h6></label>
                        <div class="input-group">
                            <input class="form-control" type="password" name="password" required>
                            <span title="Show password" class="toggle-password input-group-text">
                                <i class="bi bi-eye-slash" id="togglePassword"></i>
                            </span>
                        </div>
                        @if ($errors->has('password'))
                            <span class="text-danger">{{ $errors->first('password') }}</span>
                        @endif
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center mt-2">
                        <label>
                            <input class="m-1" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me
                        </label>
                        <a class="btn p-0 btn-link mt-8 text-decoration-underline mb-2 ml-2" href="{{route('recover_password')}}">Forgot your password?</a>
                    </div>

                    <div class="form-group d-flex justify-content-center mt-3">
                        <button class="btn btn-primary w-50 m-2" type="submit">Login</button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var userTab = document.querySelector('.login-tab[data-tab="user"]');
            var adminTab = document.querySelector('.login-tab[data-tab="admin"]');
            var userForm = document.getElementById('user-login-form');
            var adminForm = document.getElementById('admin-login-form');
        
            userTab.addEventListener('click', function() {
                userForm.style.display = 'block';
                adminForm.style.display = 'none';
                userTab.classList.add('active');
                adminTab.classList.remove('active');
            });
        
            adminTab.addEventListener('click', function() {
                adminForm.style.display = 'block';
                userForm.style.display = 'none';
                adminTab.classList.add('active');
                userTab.classList.remove('active');
            });
        });

    </script>
    <script src="{{asset('js/login.js')}}"defer></script>
    <script src="{{asset('js/contextual-help.js')}}"defer></script>

        

@endsection