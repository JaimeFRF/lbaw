@extends('layouts.app')

@section('content')
<section class="d-flex justify-content-center m-5">
  <div class="card w-50 d-flex flex-column align-items-center">
    <form class = "d-flex flex-column w-75" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="form-group m-2 d-flex flex-column">
          <label class = "mb-2" for="username">Username</label>
          <input class = "form-control" id="username" type="text" name="username" value="{{ old('username') }}" required autofocus>
          @if ($errors->has('username'))
            <span class="error">
                {{ $errors->first('username') }}
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
          {{-- <button class = "btn btn-outline-primary w-50 m-2">
            <a class="w-50 m-2" href="{{ route('login') }}">Login</a>
          </button> --}}
        </div>
        <div style="display: flex; align-items: center; margin: 1em" >
          <span>Already have an account? </span>
          <a class="btn p-0 btn-link mt-8 text-decoration-underline" href="{{route('login')}}" style="margin-left: 5px;">Login!</a>
        </div>
      </form>
    </div>
</section>
@endsection