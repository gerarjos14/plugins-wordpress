@extends('adminlte::page')
@include('components.logo')
@section('content_header')
    <h1>Planes</h1>
@endsection

@push('css')
    <style type="text/css">
        .pricing .card {
            border: none;
            border-radius: 1rem;
            transition: all 0.2s;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.1);
        }

        .pricing hr {
            margin: 1.5rem 0;
        }

        .pricing .card-title {
            margin: 0.5rem 0;
            font-size: 0.9rem;
            letter-spacing: .1rem;
            font-weight: bold;
        }

        .pricing .card-price {
            font-size: 3rem;
            margin: 0;
        }

        .pricing .card-price .period {
            font-size: 0.8rem;
        }

        .qty-documents {
            font-size: 0.9rem;
        }
    </style>
@endpush

@section('content')
    @include('components.alert')

    <div class="container">
        @if ($lifetime_plan)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5>Plan vitalicio activo</h5>
                            <p>Actualmente tienes un plan de por vida para {{ $lifetime_plan->platform }}</p>
                        </div>

                    </div>
                </div>
            </div>
        @else
            @if (session()->has('url_redirect'))
                @include('customer.subscriptions.index_beon24')
            @else
                @if (!auth()->user()->hasPaymentMethod())
                    <div class="m-3 alert alert-danger text-center">
                        <span class="fas fa-exclamation-circle"></span>
                        {{ __('Todavía no has vinculado ninguna tarjeta a tu cuenta') }} <a
                            href="{{ route('customer.billing.credit_card_form') }}">{{ __('Házlo ahora') }}</a>
                    </div>
                @endif
                <section class="pricing">
                    <div class="container">
                        <div class="row">
                            @foreach ($plans as $plan)
                                <div class="col-lg-4 mb-4">
                                    <div class="card mb-5 mb-lg-0">
                                        <form action="{{ route('customer.subscriptions.buy') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="plan" value="{{ $plan->id }}">
                                            <div class="card-body">
                                                @if ($plan->name != null)
                                                    <h5 class="card-title text-muted text-uppercase text-center"
                                                        style="max-width: 50%;">{{ $plan->name }} </h5>
                                                @else
                                                    <h5 class="card-title text-muted text-uppercase text-center"
                                                        style="max-width: 50%;">{{ $plan->platform }}</h5>
                                                @endif
                                                <h6 class="card-price text-center">${{ $plan->amount }}<span
                                                        class="period">/{{ $plan->period }}</span></h6>
                                                @if ($plan->qty_documents > 0)
                                                    <p class="text-muted m-0 ml-2 qty-documents text-uppercase">
                                                        {{ $plan->qty_documents }} documentos </p>
                                                @endif
                                                <hr>
                                                <p>{{ $plan->description }}</p>
                                                @if ($plan->interval === \App\Models\Plan::LIFETIME)
                                                    @if (auth()->user()->hasPaymentMethod())
                                                        <button type="submit"
                                                            class="btn btn-block btn-primary text-uppercase">Suscribirme</button>
                                                    @endif
                                                @else
                                                    @if (auth()->user()->hasPaymentMethod())
                                                        @if (!auth()->user()->hasIncompletePayment('main'))
                                                            @if (auth()->user()->subscribed('main'))
                                                                @if (auth()->user()->subscription('main')->stripe_plan === $plan->plan_id)
                                                                    <button type="button" disabled
                                                                        class="btn btn-block btn-primary text-uppercase">{{ __('Tu plan actual') }}</button>
                                                                @else
                                                                    @if ($priceCurrentPlan < $plan->amount)
                                                                        <button type="submit"
                                                                            class="btn btn-block btn-primary text-uppercase">{{ __('Cambiar de plan') }}</button>
                                                                    @else
                                                                        <button type="button" disabled
                                                                            class="btn btn-block btn-primary text-uppercase">{{ __('No es posible bajar') }}</button>
                                                                    @endif
                                                                @endif
                                                            @else
                                                                <button type="submit"
                                                                    class="btn btn-block btn-primary text-uppercase">{{ __('Suscribirme') }}</button>
                                                            @endif
                                                        @else
                                                            @if (auth()->user()->subscription('main')->stripe_plan === $plan->plan_id)
                                                                <a class="btn btn-block btn-info text-uppercase"
                                                                    href="{{ route('cashier.payment',auth()->user()->subscription('main')->latestPayment()->id) }}">
                                                                    {{ __('Confirma tu pago aquí') }}
                                                                </a>
                                                            @else
                                                                <button type="button" disabled
                                                                    class="btn btn-block btn-primary text-uppercase">{{ __('Esperando...') }}</button>
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif



                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

        @endif
    </div>
@endsection
