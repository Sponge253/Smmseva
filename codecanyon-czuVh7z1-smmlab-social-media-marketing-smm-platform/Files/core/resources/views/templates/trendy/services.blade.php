@extends($activeTemplate . 'layouts.frontend')
@section('content')
    <div class="py-60">
        <div class="container">
            <div class="row gy-4">
                <div class="col-12">
                    <div class="show-filter text-end mt-5 mb-3">
                        <button class="btn btn-base showFilterBtn btn-sm" type="button"><i class="las la-filter"></i>
                            @lang('Filter')</button>
                    </div>
                    <div class="card responsive-filter-card mb-4">
                        <div class="card-body">
                            <form action="">
                                <div class="d-flex flex-wrap align-items-center gap-4">
                                    <div class="flex-grow-1">
                                        <label class="form--label">@lang('Category')</label>
                                        <select class="form--control select2-basic" name="category_id">
                                            <option value="" selected>@lang('Select One')</option>
                                            @foreach ($listCategories as $category)
                                                <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>
                                                    {{ __(keyToTitle($category->name)) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-grow-1">
                                        <label class="form--label">@lang('Service')</label>
                                        <input class="form--control" name="search" type="text" value="{{ request()->search }}">
                                    </div>
                                    <div class="flex-grow-1 align-self-end">
                                        <button class="btn btn--base w-100 filter-btn"><i class="fas fa-filter"></i>
                                            @lang('Filter')</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="accordion custom--accordion services-accordion" id="accordionExample">
                        @forelse($categories as $category)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                    <button class="accordion-button @if (!$loop->first) collapsed @endif" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}" type="button" aria-expanded="@if ($loop->first) true @else false @endif" aria-controls="collapse{{ $loop->index }}">
                                        <span class="icon"><i class="las la-feather"></i></span> {{ __($category->name) }}
                                    </button>
                                </h2>
                                <div class="accordion-collapse collapse @if ($loop->first) show @endif" id="collapse{{ $loop->index }}" data-bs-parent="#accordionExample" aria-labelledby="heading{{ $loop->index }}">
                                    <div class="accordion-body">
                                        <table class="table table--responsive--md">
                                            <thead>
                                                <th>@lang('Service ID')</th>
                                                <th>@lang('Service')</th>
                                                <th>@lang('Price Per 1k')</th>
                                                <th>@lang('Min')</th>
                                                <th>@lang('Max')</th>
                                                <th>@lang('Make Order')</th>
                                            </thead>
                                            <tbody>
                                                @foreach ($category->services ?? [] as $service)
                                                    <tr>
                                                        <td>{{ $service->id }}</td>
                                                        <td class="break_line">
                                                            <span class="cursor-pointer  favoriteBtn @if (auth()->check() && in_array(@$service->id, @$myFavorites)) active @else text--secondary @endif" data-id="{{ $service->id }}" title="@lang('Favourite / Unfavourite')"><i class="la la-star"></i></span> {{ __($service->name) }}
                                                        </td>
                                                        <td>{{ showAmount($service->price_per_k) }}</td>
                                                        <td>{{ $service->min }}</td>
                                                        <td>{{ $service->max }}</td>
                                                        <td>
                                                            <div class="action-buttons">
                                                                <button class="action-btn details-btn detailsBtn" data-details="{{ $service->details }}" type="button" title="@lang('Details')" @disabled(!$service->details)> <i class="la la-desktop"></i></button>
                                                                <a class="action-btn order-btn" href="{{ auth()->user() ? route('user.order.overview', $service->id) : route('user.login') }}" title="@lang('Make Order')"><i class="las la-cart-plus"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                @include($activeTemplate . 'partials.empty', ['message' => 'Service not found!'])
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        {{-- Details MODAL --}}
        <div class="dashboard-modal modal" id="detailsModal" aria-labelledby="exampleModalLabel" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">@lang('Service Details')</h5>
                        <button class="btn-close" data-bs-dismiss="modal" type="button" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="col-12">
                            <p id="details"></p>
                        </div>
                    </div>
                    <div class="form-group buttons m-2 text-end">
                        <button class="btn btn--dark btn--sm" data-bs-dismiss="modal" type="button">@lang('Close')</button>
                    </div>
                </div>
            </div>
        </div>

        @if ($sections->secs != null)
            @foreach (json_decode($sections->secs) as $sec)
                @include($activeTemplate . 'sections.' . $sec)
            @endforeach
        @endif

    </div>

@endsection

@push('style-lib')
    <link href="{{ asset('assets/global/css/vendor/select2.min.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/vendor/select2.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container span {
            width: 100%;
        }

        .select2-container .select2-selection--single,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 45px;
            line-height: 45px;
        }

        .select2-dropdown {
            border: 1px solid #4634FF;
        }

        .filter-btn {
            padding: 12.52px 25px !important;
        }

        .select2-container {
            width: 100% !important;
            height: 49px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            // select-2 init
            // $('.select2-basic').select2();

            $('.select2-basic').select2({
                dropdownParent: $('.card-body')
            });

            $('.detailsBtn').on('click', function() {
                var modal = $('#detailsModal');
                var details = $(this).data('details');
                modal.find('#details').html(details);
                modal.modal('show');
            });

            $('.favoriteBtn').on('click', function() {

                var isAuthenticated = @json(auth()->check());
                if (!isAuthenticated) {
                    notify('error', 'Login required for manage favourite services!');
                    return 0;
                }
                var $this = $(this);
                var id = $(this).data('id');
                $.ajax({
                    type: "GET",
                    url: "{{ route('user.favorite.add') }}",
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response.action == 'add') {
                            $this.removeClass('text--secondary');
                            $this.addClass('text--warning');
                            notify('success', response.notification);
                        } else {
                            $this.removeClass('text--warning');
                            $this.addClass('text--secondary');
                            notify('success', response.notification);
                        }
                    }
                });
            });

        })(jQuery);
    </script>
@endpush
