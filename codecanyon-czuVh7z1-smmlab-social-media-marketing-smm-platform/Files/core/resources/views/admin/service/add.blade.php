@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form method="post" action="{{ route('admin.service.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>@lang('Category')</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="" selected disabled>--@lang('Select One')--</option>
                                    @foreach (@$categories as $category)
                                        <option value="{{ $category->id }}">{{ @$category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">@lang('Name') </label>
                                <input class="form-control " name="name" type="text" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-3">
                                <label>@lang('Price Per 1k') </label>
                                <div class="input-group">
                                    <input class="form-control" name="price_per_k" type="text" required>
                                    <div class="input-group-text">{{ __(gs("cur_text")) }}</div>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group">
                                    <label>@lang('Min')</label>
                                    <input class="form-control" name="min" type="number" required>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <div class="form-group">
                                    <label>@lang('Max')</label>
                                    <input class="form-control" name="max" type="number" required>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>@lang('Dripfeed')</label>
                                <div class="form-group">
                                    <input name="dripfeed" data-width="100%" data-size="large" data-onstyle="-success" data-offstyle="-danger" data-bs-toggle="toggle" data-height="35" data-on="@lang('Yes')" data-off="@lang('No')" type="checkbox">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>@lang('Details')</label>
                            <textarea class="form-control" name="details" rows="5"></textarea>
                        </div>
                        <div class="form-group api_service_id">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn--primary h-45 w-100" type="submit">@lang('Submit')</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
@endsection
@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.service.index') }}" />
@endpush
