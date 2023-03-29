@extends('adminlte::page')
@include('components.logo')
@section('content')
    <link rel="stylesheet" href="{{ asset('css/beon24/cards.css') }}">

    <div class="container">

        <div class="row">
            <div class="col-md-12">
                <a href="{{ route('customer.cards.show_new_card') }}" class="btn btn-primary btn-new-cards">
                    Registrar nueva tarjeta
                </a>
            </div>
        </div>

        <div class="row">
            @if (isset($user_cards))
                @foreach ($user_cards as $item)
                    <div class="col-md-4">

                        <div class="card card-data-cards">
                            @php
                                # Se busca si la misma se encuentra activa o no
                                $is_active = false;
                                $data_active = App\Models\UserCardsActive::where('card_id', $item->id)->first();
                                
                                if ($data_active) {
                                    $is_active = true;
                                }
                            @endphp
                            <div class="card-body">
                                <div class="row row-logo-cards">
                                    <div class="col-sm-6">
                                        <img class="logo-card-{{ $item->card_brand == 'American Express' ? 'american_express' : $item->card_brand }}"
                                            src="{{ asset($item->card_brand == 'American Express' ? 'img/american_express.png' : 'img/' . $item->card_brand . '.png') }}"
                                            alt="">
                                    </div>
                                </div>
                                <h5 class="card-title">
                                    Terminada en {{ $item->card_last_four }}
                                    @if ($is_active)
                                        <b>(en uso)</b>
                                    @endif
                                </h5>
                                <p class="card-text">Tarjeta {{ $item->card_brand }}</p>
                                <p class="card-text">
                                    Vencimiento: {{ base64_decode($item->month) }}/20{{ base64_decode($item->year) }}
                                </p>
                            </div>
                            <div class="card-footer footer-cards-bc">
                                @if (!($is_active))
                                    <a href="{{route('customer.cards.active_card', ['card_number' => $item->number_card])}}" class="btn btn-primary">Activar tarjeta</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {!! $user_cards->render() !!}
            @endif

        </div>
    </div>
@endsection
