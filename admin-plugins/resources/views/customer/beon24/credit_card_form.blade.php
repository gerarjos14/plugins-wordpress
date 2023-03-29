@extends('adminlte::page')
@include('components.logo')
@section('content')
    @include('components.alert')
    @if (true)
        <div class="container">
            <div class="row">
                <div class="col-lg-7 mx-auto">
                    <div class="bg-white rounded-lg shadow-sm p-5">
                        <!-- Credit card form content -->
                        <div class="tab-content">
                            <!-- credit card info-->
                            <div id="nav-tab-card" class="tab-pane fade show active">
                                <form role="form" action="{{ route('customer.cards.process_card') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="card_number">{{ __('Número de la tarjeta') }}</label>
                                        <div class="input-group">
                                            <input type="text" name="card_number"
                                                placeholder="{{ __('Número de la tarjeta') }}" class="form-control"
                                                required />
                                            <div class="input-group-append">
                                                <span class="input-group-text">
                                                    <i class="fab fa-cc-visa mx-1"></i>
                                                    <i class="fab fa-cc-mastercard mx-1"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-8">
                                            <div class="form-group">
                                                <label><span class="hidden-xs">{{ __('Fecha expiración') }}</span></label>
                                                <div class="input-group">
                                                    <input type="number" placeholder="{{ __('MM') }}"
                                                        name="card_exp_month" class="form-control" required max="99">
                                                    <input type="number" placeholder="{{ __('YY') }}"
                                                        name="card_exp_year" class="form-control" required max="99">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="form-group mb-4">
                                                <label data-toggle="tooltip"
                                                    title="{{ __('Introduce los 3 digitos de seguridad de tu tarjeta') }}">{{ __('CVC') }}
                                                    <i class="fa fa-question-circle"></i>
                                                </label>
                                                <input type="text" name="cvc" required class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="card_number">{{ __('Activar tarjeta') }}</label>
                                        <div class="form-check">
                                            <input name="is_active" type="checkbox" class="form-check-input" id="exampleCheck1">
                                            <label  class="form-check-label" for="exampleCheck1">Activar tarjeta</label>
                                        </div>
                                    </div>
                                    <button type="submit"
                                        class="subscribe btn btn-primary btn-block shadow-sm">{{ __('Guardar tarjeta') }}</button>
                                </form>
                            </div>
                            <!-- End -->
                        </div>
                        <!-- End -->
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <p class="m-0"></p>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
@section('js')
    <script>
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
    </script>
@endsection
