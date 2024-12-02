@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light tabstyle--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>
                                        <label for="selectAll"><i class="th-check-all fa fa-stop"></i></label>
                                    </th>
                                    <th>@lang('ID')</th>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Min')</th>
                                    <th>@lang('Max')</th>
                                    <th>@lang('Dripfeed')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $item)
                                    <tr>
                                        <td>
                                            <input class="childCheckBox" name="checkbox_id" data-name="{{ @$item->name }}" data-api_provider_id={{ @$item->api_id }} data-category="{{ @$item->category }}" data-price_per_k="{{ getAmount(@$item->rate) }}" data-min="{{ @$item->min }}" data-max="{{ @$item->max }}" data-api_service_id="{{ @$item->service }}" type="checkbox">
                                        </td>
                                        <td><strong>{{ @$item->service }}</strong></td>
                                        <td class="break_line">{{ __(@$item->name) }}
                                        </td>
                                        <td class="break_line">{{ __(@$item->category) }}</td>
                                        <td>
                                            {{ gs('cur_sym') . showAmount(@$item->rate,currencyFormat:false) }}</td>
                                        <td>{{ @$item->min }}</td>
                                        <td>{{ @$item->max }}</td>
                                        <td>
                                            @if (@$item->dripfeed == Status::YES)
                                                <span class="badge badge--success"> @lang('Yes ')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No ')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary addBtn" data-original-title="@lang('Action')" data-toggle="tooltip" data-name="{{ @$item->name }}" data-api_provider_id={{ @$item->api_id }} data-category="{{ @$item->category }}" data-price_per_k="{{ getAmount(@$item->rate) }}" data-min="{{ @$item->min }}" data-max="{{ @$item->max }}" data-api_service_id="{{ @$item->service }}" data-dripfeed = "{{ @$item->dripfeed }}" type="button">
                                                <i class="las la-plus"></i>
                                                @lang('Add Service')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($services->hasPages())
                    <div class="card-footer">
                        {{ paginateLinks($services) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>

    <div class="modal fade" id="confirmServiceModal" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Enter how many Times You Incress Price')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                </div>
                <div class="modal-body">
                    <div class="col-auto">
                        <input class="form-control inputNumer" type="number" value="1" placeholder="@lang('Enter positive number')" min="1">
                    </div>
                </div>
                <div class="modal-footer">

                    <button class="btn btn--primary w-100 h-45" id="allService" type="button">@lang('Submit')</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Add MODAL --}}
    <div class="modal fade" id="addModal" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">@lang('Add New')</h4>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>

                    </button>
                </div>
                <form class="form-horizontal reset" method="post" action="{{ route('admin.service.api.store') }}">
                    @csrf
                    <input name="api_provider_id" type="hidden">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Category')</label>
                            <div class="col-sm-12">
                                <input class="form-control" name="category" type="text" required readonly>
                            </div>
                        </div>

                        <div class="form-row form-group">
                            <label>@lang('Name')</label>
                            <div class="col-sm-12">
                                <input class="form-control" id="code" name="name" type="text" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Price Per 1k')</label>
                            <div class="input-group">
                                <input class="form-control" name="price_per_k" type="text" required>
                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Min')</label>
                                <input class="form-control" name="min" type="text" required readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>@lang('Max')</label>
                                <input class="form-control" name="max" type="text" readonly required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Details')</label>
                            <textarea class="form-control" name="details" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>@lang('Service Id (If order process through API)')</label>
                            <input class="form-control" name="api_service_id" type="text" readonly required>
                        </div>
                        <div class="form-group">
                            <input class="form-control" name="dripfeed" type="hidden" readonly required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <div class="select-all">
        <div class="select-all__content d-flex align-items-center flex-wrap">
            <label class="select-all__text mb-0 me-2" for="selectAll">@lang('Select All')</label>
            <input class="checkAll mb-1" id="selectAll" type="checkbox">
        </div>
    </div>
    <div class="input-group w-auto search-form">
        <input class="form-control bg--white" name="search_table" type="text" placeholder="@lang('Search')...">
        <button class="btn btn--primary input-group-text"><i class="fa fa-search"></i></button>
    </div>
    <button class="btn btn-outline--info btn-sm d-none h-45 addService" data-bs-toggle="modal" data-bs-target="#confirmServiceModal">
        <i class="las la-plus"></i>
        @lang('Add Selected Service')
    </button>
    <x-back route="{{ route('admin.service.index') }}" />
@endpush

@push('style')
    <style>
        .select-all {
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 10px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(".childCheckBox").on('change', function(e) {
                let totalLength = $(".childCheckBox").length;
                let checkedLength = $(".childCheckBox:checked").length;
                if (totalLength == checkedLength) {
                    $('.checkAll').prop('checked', true);
                    $('.th-check-all').addClass('fa-check-square').removeClass('fa-stop');
                } else {
                    $('.checkAll').prop('checked', false);
                    $('.th-check-all').addClass('fa-stop').removeClass('fa-check-square');
                }
                if (checkedLength) {
                    $('.addService').removeClass('d-none')
                } else {
                    $('.addService').addClass('d-none')
                }
            });

            $('.checkAll').on('change', function() {
                if ($('.checkAll:checked').length) {
                    $('.childCheckBox').prop('checked', true);
                    $('.th-check-all').addClass('fa-check-square').removeClass('fa-stop');
                } else {
                    $('.childCheckBox').prop('checked', false);
                    $('.th-check-all').addClass('fa-stop').removeClass('fa-check-square');
                }
                $(".childCheckBox").change();
            });


            $('#allService').on('click', function() {
                let services = [];
                let inputNumber = parseInt($('.inputNumer').val());
                var checkBox = $('input:checkbox[name=checkbox_id]:checked');
                checkBox.each(function() {
                    let api_service_id = $(this).data('api_service_id')
                    let name = $(this).data('name');
                    let price_per_k = parseFloat($(this).data('price_per_k'));
                    let min = $(this).data('min');
                    let max = $(this).data('max');
                    let category = $(this).data('category');
                    let api_provider_id = $(this).data('api_provider_id');
                    services.push({
                        "api_service_id": api_service_id,
                        "name": name,
                        "price_per_k": price_per_k,
                        "min": min,
                        "max": max,
                        "category": category,
                        "incressTimes": inputNumber,
                        "api_provider_id": api_provider_id,
                    });

                })
                $.ajax({
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ route('admin.service.add') }}",
                    data: {
                        services: services
                    },
                    success: function(data) {
                        if (data.success) {
                            $('.childCheckBox').prop('checked', false);
                            $('.checkAll').prop('checked', false);
                            $('#confirmServiceModal').modal('hide');
                            notify('success', data.message);
                            window.location.href = "{{ route('admin.service.index') }}";
                        } else {
                            notify('error', `@lang('Something wrong please try again')`);
                        }

                    }

                })

            })

            $(document).on('click', '.addBtn', function() {
                var modal = $('#addModal');
                $('.reset').trigger("reset");
                var name = $(this).data('name');
                var price_per_k = $(this).data('price_per_k');
                var min = $(this).data('min');
                var max = $(this).data('max');
                var category = $(this).data('category');
                var api_provider_id = $(this).data('api_provider_id');
                var api_service_id = $(this).data('api_service_id');
                modal.find('input[name=name]').val(name);
                modal.find('input[name=price_per_k]').val(price_per_k);
                modal.find('input[name=min]').val(min);
                modal.find('input[name=max]').val(max);
                modal.find('input[name=api_provider_id]').val(api_provider_id);
                modal.find('input[name=api_service_id]').val(api_service_id);
                modal.find('input[name=category]').val(category);

                var dp = $(this).data('dripfeed') ? 1 : 0;
                modal.find('input[name=dripfeed]').val(dp);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
