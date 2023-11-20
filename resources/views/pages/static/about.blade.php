@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 aboutText">
                <h7 class="text-center">About Us</h7>
                <p>Welcome to the Antiquus website!</p>
                <p>Antiquus is a fictional online vintage clothing store, developed for the Databases and Web Applications Laboratory (LBAW) course in the year of 2023/2024.</p>
                <p>We are a team of 4 students currently working on this website with the goal to make it a fully functional online store (excluding the ordering and buying parts) while being visually appealing and granting the user a good experience.</p>
                <p>You can find our contacts and more information about us <a href="{{ route('contacts') }}">here</a>.</p> 
            </div>
        </div>
    </div>
@endsection
