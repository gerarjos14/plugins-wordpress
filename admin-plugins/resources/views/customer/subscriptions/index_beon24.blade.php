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
                                    <button type="submit"
                                        class="btn btn-block btn-primary text-uppercase">Suscribirme</button>
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
                                    @else
                                        <button type="submit" class="btn btn-block btn-primary text-uppercase">{{ __('Suscribirme') }}</button>   
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
