@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="card b-radius--10">
        <div class="card-body">
            <form class="dashboard-form" id="orderConfirmation" action="{{ route('user.dripfeed.create', @$service->id) }}" method="POST">
                @csrf
                <input class="form-control" name="api_provider_id" type="hidden">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <h4><i class="las la-shopping-cart"></i> @lang('New Dripfeed Order')</h4>
                        <hr class="mb-4">
                        <div class="form-group">
                            <label class="form--label">@lang('Category')</label>
                            <select class="form--control category-select-box" name="category_id" required>
                                <option data-title="@lang('Select One')" data-service_from="@lang('N/A')" value="">
                                    @foreach ($categories as $category)
                                <option data-title="{{ __($category->name) }} ({{ $category->services_count }})" data-service_from="{{ showAmount(($category->price_per_k / 1000) * $category->service_min_start) }}" data-services='@json($category->services)' value="{{ $category->id }}" @selected($category->id == @$service->category_id)>{{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Select Service')</label>
                            <select class="form--control service-select-box" name="service_id" required> </select>
                        </div>

                        <div class="form-group">
                            <label class="form--label">@lang('Link')</label>
                            <input class="form--control" name="link" type="url" value="{{ old('link') }}" placeholder="https://www.example.com" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Quantity')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="dripfeed_quantity" type="number" value="{{ old('dripfeed_quantity') }}" required>
                                        <div class="input-group-text">@lang('QTY')</div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Runs')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="runs" type="number" value="{{ old('runs') }}" required>
                                        <div class="input-group-text">@lang('Times')</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Intervals')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="intervals" type="number" value="{{ old('intervals') }}" required>
                                        <div class="input-group-text">@lang('Minutes')</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Total Quantity')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="quantity" type="number" value="{{ old('quantity') }}" required readonly>
                                        <div class="input-group-text">@lang('QTY')</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form--label">@lang('Price') <small>(@lang('Per 1K'))</small></label>
                            <div class="input-group">
                                <input class="form-control form--control" name="price" type="number" value="{{ old('price') }}" required readonly>
                                <div class="input-group-text">{{ __(gs("cur_text")) }}</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form--check">
                                <input class="form-check-input" id="orderConfirmationCheck" type="checkbox" value="1" required>
                                <label class="form-check-label" for="orderConfirmationCheck">
                                    @lang('Yes! I confirm the order.')
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0">
                            <button class="btn btn--base w-100" type="submit"> @lang('Place Order')</button>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <h4><i class="las la-luggage-cart"></i> @lang('Order Resume')</h4>
                        <hr class="mb-4">
                        <div class="form-group">
                            <label class="form--label">@lang('Service Name')</label>
                            <input class="form--control" name="service_name" type="text" readonly>
                        </div>
                        <div class="row">
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Minimum Quantity')</label>
                                    <div class="input-group">
                                        <input class="form-control form--control" name="minimum" type="number" readonly>
                                        <div class="input-group-text"><i class="las la-sort-numeric-down"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Maximum Quantity')</label>

                                    <div class="input-group">
                                        <input class="form-control form--control" name="maximum" type="number" readonly>
                                        <div class="input-group-text"><i class="las la-sort-numeric-up"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Price Per 1K')</label>
                                    <div class="input-group">
                                        <div class="input-group-text">{{ gs("cur_sym") }}</div>
                                        <input class="form-control form--control" name="price_per_k" type="number" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group ">
                            <label class="form--label">@lang('Description')</label>
                            <textarea class="form-control form--control" name="details" rows="10" disabled></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        // start-old-value//
        @if (old() && old('category_id'))
            $('[name=category_id]').val(@json(old('category_id')));
        @endif

        var oldServiceId = "{{ old('service_id') }}";
        //End-ol- value//

        //Start category //
        var categoryOptions = $('.category-select-box').find('option');

        var categoryHtml = `
            <div class="category-select">
                <div class="selected-category d-flex justify-content-between align-items-center">
                    <p class="category-title">Facebook Page Like (1000)</p>
                    <div class="icon-area">
                        <i class="las la-angle-down"></i>
                    </div>
                </div>
                <div class="category-list d-none">
        `;
        $.each(categoryOptions, function(key, option) {

            option = $(option);
            if (option.data('title') && option.data('service_from') != 'N/A') {
                categoryHtml += `<div class="single-category" data-value="${option.val()}">
                            <p class="category-title">${option.data('title')}</p>
                            <p class="category-charge">Service start from: ${option.data('service_from')}</p>
                        </div>`;
            } else {
                categoryHtml += `<div class="single-category" data-value="${option.val()}">
                            <p class="category-title">${option.data('title')}</p>
                            </div>`;

            }
        });
        categoryHtml += `</div></div>`;
        $('.category-select-box').after(categoryHtml);
        var selectedcategory = $('.category-select-box :selected');
        $(document).find('.selected-category .category-title').text(selectedcategory.data('title'))



        $('.selected-category').on('click', function() {
            $('.category-list').toggleClass('d-none');
            $(this).toggleClass('focus');
            $(this).find('.icon-area').find('i').toggleClass('la-angle-up');
            $(this).find('.icon-area').find('i').toggleClass('la-angle-down');
        });

        $(document).on('click', '.single-category', function() {
            $('.selected-category').find('.category-title').text($(this).find('.category-title').text());
            $('.category-list').addClass('d-none');
            $('.selected-category').removeClass('focus');
            $('.selected-category').find('.icon-area').find('i').toggleClass('la-angle-up');
            $('.selected-category').find('.icon-area').find('i').toggleClass('la-angle-down');
            $('.category-select-box').val($(this).data('value'));
            $('.category-select-box').trigger('change');
        });

        function selectPostType(whereClick, whichHide) {
            if (!whichHide) return;

            $(document).on("click", function(event) {
                var target = $(event.target);
                if (!target.closest(whereClick).length) {
                    $(document).find('.icon-area i').addClass("la-angle-down").removeClass('la-angle-up');
                    whichHide.addClass("d-none");
                    whereClick.removeClass('focus');
                }
            });
        }
        selectPostType(
            $('.selected-category'),
            $(".category-list")
        );
        //end-category operation//


        (function($) {
            "use strict";
            var serviceId = `{{ @$service->id }}`
            let services = $('select[name="category_id"]').find(`option:selected`).data(`services`);;

            getService(services);
            $('select[name="category_id"]').on('change', function() {
                getService(services)
            }).change();

            function getService(services) {
                services = $('select[name="category_id"]').find(`option:selected`).data(`services`);
                let html = `<option value="">@lang('Select One')</option>`;
                $.each(services, function(i, service) {
                    let isSelected = serviceId == service.id || oldServiceId == service.id ? 'selected' : '';
                    let serviceDataAttr = `data-service_data="${JSON.stringify(service).replace(/"/g, '&quot;')}"`;
                    html += `<option ${serviceDataAttr} value="${service.id}" ${isSelected}>${service.name}</option>`;
                });
                $(`select[name=service_id]`).html(html);

                let serviceData = $('select[name="service_id"]').find(`option:selected`).data('service_data');

                if (serviceData) {
                    updateService(serviceData)
                }
            }

            $('select[name="service_id"]').on('change', function() {
                let data = $(this).find('option:selected').data('service_data');
                updateService(data);

                var newAction = "{{ route('user.dripfeed.create', ':id') }}";
                newAction = newAction.replace(':id', data.id);
                $('#orderConfirmation').attr('action', newAction);

                $('[name="quantity"]').val('');
            });


            @if(old('service_id'))
                $('select[name="service_id"]').change();
            @endif

            $('[name="runs"]').on('input', function() {
                var dripfeed = $('[name="dripfeed_quantity"]').val();
                var quantity = $(this).val() * dripfeed;
                dripCalculate(quantity);
            });

            $('[name="dripfeed_quantity"]').on('input', function() {
                var runs = $('[name="runs"]').val();
                var quantity = $(this).val() * runs;
                dripCalculate(quantity);
            });

            function updateService(serviceData) {
                $('[name ="api_provider_id"]').val(serviceData.api_provider_id);
                //order-resume//
                $('[name="service_name"]').val(serviceData.name);
                $('[name="minimum"]').val(serviceData.min);
                $('[name="maximum"]').val(serviceData.max);
                $('[name="price_per_k"]').val(parseFloat(serviceData.price_per_k).toFixed(2));
                if (serviceData.details) {
                    $('[name="details"]').val(serviceData.details);
                } else {
                    $('[name="details"]').val(`@lang('No description')`).addClass('text--secondary');
                }
            }

            //Calculate total price
            function dripCalculate(qty) {
                var pricePerK = $('[name="price_per_k"]').val();
                var quantity = parseInt(qty);
                var totalPrice = parseFloat((pricePerK / 1000) * quantity);
                $('[name="quantity"]').val(qty);
                $('[name="price"]').val(totalPrice.toFixed(2));
            }

        })(jQuery);
    </script>
@endpush
