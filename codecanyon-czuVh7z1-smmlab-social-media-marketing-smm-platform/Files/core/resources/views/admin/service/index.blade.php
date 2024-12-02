@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table table--light tabstyle--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Price Per 1k')</th>
                                    <th>@lang('Min')</th>
                                    <th>@lang('Max')</th>
                                    <th>@lang('API Service ID')</th>
                                    <th>@lang('Dripfeed')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($services as $service)
                                    <tr>
                                        <td class="break_line">{{ __(@$service->name) }}
                                            @if (@$service->provider->short_name)
                                                <span class="badge badge--primary">{{ __(@$service->provider->short_name) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="break_line">{{ __(@$service->category->name) }}</td>
                                        <td>
                                            {{ showAmount(@$service->price_per_k) }}</td>
                                        <td>{{ @$service->min }}</td>
                                        <td>{{ @$service->max }}</td>
                                        <td>
                                            @if ($service->api_service_id)
                                                {{ @$service->api_service_id }}
                                            @else
                                                @
                                            @endif
                                        </td>
                                        <td>
                                            @if (@$service->dripfeed == Status::YES)
                                                <span class="badge badge--success"> @lang('Yes')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('No')</span>
                                            @endif
                                        </td>

                                        <td> @php echo $service->statusBadge; @endphp </td>
                                        <td>
                                            <div class="button--group">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.service.edit', $service->id) }}">
                                                    <i class="la la-pen"></i> @lang('Edit')
                                                </a>
                                                @if ($service->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-action="{{ route('admin.service.status', $service->id) }}" data-question="@lang('Are you sure to enable this service?')" type="button">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.service.status', $service->id) }}" data-question="@lang('Are you sure to disable this service?')" type="button">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
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
                    <div class="card-footer py-4">
                        {{ paginateLinks($services) }}
                    </div>
                @endif

            </div><!-- card end -->
        </div>
    </div>
    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here ..." />
    <a class="btn btn-outline--primary h-45" href="{{ route('admin.service.add') }}">
        <i class="las la-plus"></i>@lang('Add New')
    </a>
    <button class="btn h-45 btn-outline--info" id="actionButton" data-bs-toggle="dropdown">
        <i class="las la-ellipsis-v"></i>
        @lang('API Services')
    </button>
    <div class="dropdown-menu p-0">
        @foreach ($apiLists as $apiList)
            <a class="dropdown-item" href="{{ route('admin.service.api', $apiList->id) }}">
                <i class="las la-cloud-download-alt"></i>
                {{ __($apiList->name) }}
            </a>
        @endforeach
    </div>
@endpush
