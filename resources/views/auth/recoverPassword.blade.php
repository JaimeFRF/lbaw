@extends('layouts.app')

@section('css')
<link href="{{ url('css/login_register.css') }}" rel="stylesheet">
@endsection

@section('content')

    <section class="d-flex justify-content-center m-5" id="content">
        <div class="card w-50 d-flex flex-column align-items-center">
            <div class="d-flex">
                <div class="login-tab p-3 active" data-tab="user">Recover Password</div>
            </div>
            <form method="POST" action="/send">
                @csrf
                <input type="hidden" name="type" value="0"> 
                <div class="form-group d-flex flex-column mt-3" id="username">
                    <label for="username"><h6>Username</h6></label>
                    <input class="form-control" id="username" type="text" name="username" placeholder="Username" required>
                </div>
                <div class="user-login-fields">
                        <div class="form-group d-flex flex-column mt-3">
                            <label for="email"><h6>Email</h6></label>
                            <input class="form-control" id="email" type="email" name="email" placeholder="Email" required autofocus>
                        </div>
                </div>
                @if ($errors->has('email'))
                    <div class="alert alert-danger">
                        {{ $errors->first('email') }}
                    </div>
                @endif
                <div class="form-group d-flex justify-content-center mt-3">
                    <button class="btn btn-primary w-50 m-2" type="submit">Send</button>
                </div>

            </form>
        </div>
    </section>

@endsection