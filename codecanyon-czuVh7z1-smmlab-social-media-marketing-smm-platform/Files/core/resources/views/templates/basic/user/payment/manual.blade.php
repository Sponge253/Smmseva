@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="row justify-content-center py-5">
        <div class="col-lg-10">
            <div class="deposit-card">
                <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <p class="text-center mt-2">@lang('You have requested') <b
                                   class="text--success">{{ showAmount($data['amount']) }}
                                </b> , @lang('Please pay')
                                <b class="text--success">{{ showAmount($data['final_amount']) . ' ' . $data['method_currency'] }}
                                </b> @lang('for successful payment')
                            </p>
                            <h4 class="text-center mb-4">@lang('Please follow the instruction below')</h4>

                            <p class="my-4 text-center">@php echo  $data->gateway->description @endphp</p>

                        </div>

                        <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn--base w-100 h-45" type="submit">@lang('Pay Now')</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection