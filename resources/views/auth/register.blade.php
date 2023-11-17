@extends('layouts.app')

@section('content')
<section class="d-flex justify-content-center m-5">
  <div class="card w-50 d-flex flex-column align-items-center">
    <form class = "d-flex flex-column w-75" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="form-group m-2 d-flex flex-column">
          <label class = "mb-2" for="name">Name</label>
          <input class = "form-control" id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
          @if ($errors->has('name'))
            <span class="error">
                {{ $errors->first('name') }}
            </span>
          @endif
        </div>  
        

        <div class="form-group m-2 d-flex flex-column">
          <label  class = "mb-2" for="email">Email</label>
          <input class = "form-control" id="email" type="email" name="email" value="{{ old('email') }}" required>
          @if ($errors->has('email'))
            <span class="error">
                {{ $errors->first('email') }}
            </span>
          @endif
        </div>  

        <div class="form-group m-2 d-flex flex-column">
          <label class = "mb-2" for="password">Password</label>
          <input class = "form-control" id="password" type="password" name="password" required>
          @if ($errors->has('password'))
            <span class="error">
                {{ $errors->first('password') }}
            </span>
          @endif
        </div>

        <div class="form-group m-2 d-flex flex-column">
          <label  class = "mb-2" for="password-confirm">Confirm Password</label>
          <input  class = "form-control"  id="password-confirm" type="password" name="password_confirmation" required>
        </div>

        <div class="form-group d-flex mt-3">
          <button class = "btn btn-primary w-50 m-2" type="submit"> Register</button>
          <button class = "btn btn-outline-primary w-50 m-2">
            <a class="w-50 m-2" href="{{ route('login') }}">Login</a>
          </button>
        </div>

      </form>
    </div>
</section>
@endsection