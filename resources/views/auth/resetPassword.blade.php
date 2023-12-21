@extends('layouts.app')

@section('css')
<link href="{{ url('css/login_register.css') }}" rel="stylesheet">
@endsection

@section('title', 'Antiquus - Reset Password')

@section('content')

    <section class="d-flex justify-content-center m-5" id="content">
        <div class="card w-50 d-flex flex-column align-items-center">
            <div class="d-flex">
                <div class="login-tab p-3 active" data-tab="user">Reset Password</div>
            </div>
            <form method="POST" action="{{ route('new_password') }}">
                @csrf
                <div class="user-login-fields">
                        <div class="form-group d-flex flex-column mt-3">
                            <label for="email"><h6>Email</h6></label>
                            <input class="form-control" id="email" type="email" name="email" placeholder="{{ $email }}" value="{{ $email}}" readonly required autofocus></div>
                        <div class="form-group d-flex flex-column mt-3">
                            <label for="password"><h6>New Password</h6></label>
                            <input class="form-control" id="password" type="password" name="password" placeholder="New Password" required>
                        </div>

                        <div class="form-group d-flex flex-column mt-3">
                            <label for="password"><h6>New Password Confirmation</h6></label>
                            <input class="form-control" id="password-confirmation" type="password" name="password-confirmation" placeholder="New Password" required>
                        </div>
                        @if ($errors->has('password'))
                            <div class="alert alert-danger">
                                {{ $errors->first('password') }}
                            </div>
                        @endif
                </div>
                <div class="form-group d-flex justify-content-center mt-3">
                    <button class="btn btn-primary w-50 m-2" type="submit">Send</button>
                </div>

            </form>
        </div>
    </section>

@endsection