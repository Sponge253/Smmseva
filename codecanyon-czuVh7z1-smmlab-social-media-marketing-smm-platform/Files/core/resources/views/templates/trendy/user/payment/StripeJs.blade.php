@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="row justify-content-center py-5">
        <div class="col-lg-8">
                <h5 class="deposit-card-title">@lang('Stripe Storefront')</h5>
                <div class="card-body">
                    <form action="{{ $data->url }}" method="{{ $data->method }}">
                        <ul class="list-group text-center">
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You have to pay '):
                                <strong>{{ showAmount($deposit->final_amount) }}
                                    {{ __($deposit->method_currency) }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                @lang('You will get '):
                                <strong>{{ showAmount($deposit->amount) }}</strong>
                            </li>
                        </ul>
                        <script src="{{ $data->src }}" class="stripe-button"
                                @foreach ($data->val as $key => $value)
                            data-{{ $key }}="{{ $value }}" @endforeach></script>
                    </form>
                </div>
        </div>
    </div>
@endsection
@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').removeClass().addClass("btn btn--base w-100 mt-3").text("Pay Now");
        })(jQuery);
    </script>
@endpush