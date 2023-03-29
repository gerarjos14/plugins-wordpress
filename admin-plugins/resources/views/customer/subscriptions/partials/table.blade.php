<div class="col-md-4">
    <div class="card">        
        <div class="card-body">
            @if ($currentPlan)
                <table class="table">
                    @php
                        
                    @endphp
                    <h4>Plan actual</h4>
                    <tr>
                        <td>Plan:</td>
                        <td>{{ $plan->name != null ?  strtoupper($plan->name) : $plan->platform }}</td>
                    </tr>
                    <tr>
                        <td>Alta:</td>
                        <td>{{auth()->user()->subscription('main')->created_at->format('d/m/Y')}}</td>
                    </tr>
                    @if(auth()->user()->hasIncompletePayment('main'))
                        <tr>
                            <td>Estado</td>
                            <td class="text-center">{!! __("Pendiente de confirmación, pulsa <a href=':link'>aquí</a> para confirmar", [
                                    "link" => route('cashier.payment', auth()->user()->subscription('main')->latestPayment()->id)
                                ]) !!}
                            </td>
                        </tr>
                    @else
                        @if (auth()->user()->subscription('main')->ends_at)
                            <tr>
                                <td>Finaliza en:</td>
                                <td>{{auth()->user()->subscription('main')->ends_at->format('d/m/Y')}}</td>
                            </tr> 
                        @else
                            <tr>
                                <td>Estado</td>
                                <td>Suscripción activa</td>
                            </tr> 
                        @endif
                    @endif
                </table>   
                @if(auth()->user()->subscription('main')->ends_at)
                    @if( ! auth()->user()->subscribed('main'))
                        El plan ya no está vigente, ¡contrata uno nuevo!
                    @else
                        @if(auth()->user()->hasIncompletePayment('main'))
                            <button class="btn btn-info btn-block" disabled>Pendiente de confirmación</button>
                        @else
                            <form action="{{ route('customer.subscriptions.resume') }}" method="POST">
                                @csrf
                                <input type="hidden" name="plan" value="{{ auth()->user()->subscription('main')->name }}" />
                                <button class="btn btn-success btn-block shadow-sm">Reanudar</button>
                            </form>
                        @endif
                    @endif
                @else
                    @if(auth()->user()->hasIncompletePayment('main'))
                        <button class="btn btn-info btn-block shadow-sm" disabled>Pendiente de confirmación</button>
                    @else
                        <form action="{{ route('customer.subscriptions.cancel') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan" value="{{ auth()->user()->subscription('main')->name }}" />
                            <button class="btn btn-danger btn-block shadow-sm">Cancelar renovación automática</button>
                        </form>
                    @endif
                @endif       
            @else
                <p>No tienes un plan activo actualmente</p>
            @endif
             
        </div>
    </div>
</div>
