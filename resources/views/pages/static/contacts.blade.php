@extends('layouts.app')

@section('css')
<link href="{{ url('css/contacts.css') }}" rel="stylesheet">
@endsection

@section('content')
    @include('partials.common.breadcrumbs', ['breadcrumbs' => $breadcrumbs , 'current' => $current ])

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h2 class="text-center title">Contacts</h2>

                <div class="row text-center contacts">
                    <div class="col-md-3 contact-person pad">
                        <img src="{{ asset('images/default-person.png') }}" alt="Carlos Daniel Rebelo">
                        <h4>Carlos Daniel Lopes Rebelo</h4>
                        <p>up202108885@up.pt</p>
                    </div>

                    <div class="col-md-3 contact-person pad">
                        <img src="{{ asset('images/default-person.png') }}" alt="Hélder Gabriel Silva Costa">
                        <h4>Hélder Gabriel Silva Costa</h4>
                        <p>up202108719@up.pt</p>
                    </div>

                    <div class="col-md-3 contact-person pad">
                        <img src="{{ asset('images/default-person.png') }}" alt="Jaime Francisco Rodrigues Fonseca">
                        <h4>Jaime Francisco Rodrigues Fonseca</h4>
                        <p>up202108789@up.pt</p>
                    </div>

                    <div class="col-md-3 contact-person last">
                        <img src="{{ asset('images/default-person.png') }}" alt="Manuel Maria Faria de Sousa e Silva">
                        <h4>Manuel Maria Faria de Sousa e Silva</h4>
                        <p>up202108874@up.pt</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
