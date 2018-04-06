{{-- Master Layout --}}
@extends('cortex/foundation::frontarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ extract_title(Breadcrumbs::render()) }}
@endsection

@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Auth\B2B2C2\Http\Requests\Frontarea\AccountSettingsRequest::class)->selector('#frontarea-account-settings-form') !!}

    <script>
        window.countries = {!! $countries !!};
        window.selectedCountry = '{{ old('country_code', $currentUser->country_code) }}';
    </script>
@endpush

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row profile">
            <div class="col-md-3">
                @include('cortex/auth::frontarea.partials.sidebar')
            </div>
            <div class="col-md-9">
                <div class="profile-content">

                    {{ Form::model($currentUser, ['url' => route('frontarea.account.settings.update'), 'id' => 'frontarea-account-settings-form']) }}

                        <div class="row">

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('full_name') ? ' has-error' : '' }}">
                                    {{ Form::label('full_name', trans('cortex/auth::common.full_name')) }}
                                    {{ Form::text('full_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->full_name ?: trans('cortex/auth::common.full_name'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                    @if ($errors->has('full_name'))
                                        <span class="help-block">{{ $errors->first('full_name') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                    {{ Form::label('username', trans('cortex/auth::common.username')) }}
                                    {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => $currentUser->username, 'required' => 'required']) }}

                                    @if ($errors->has('username'))
                                        <span class="help-block">{{ $errors->first('username') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    {{ Form::label('email', trans('cortex/auth::common.email')) }}
                                    {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('cortex/auth::common.email'), 'required' => 'required']) }}

                                    @if ($currentUser->email_verified)
                                        <small class="text-success">{!! trans('cortex/auth::common.email_verified', ['date' => $currentUser->email_verified_at]) !!}</small>
                                    @elseif($currentUser->email)
                                        <small class="text-danger">{!! trans('cortex/auth::common.email_unverified', ['href' => route('frontarea.verification.email.request')]) !!}</small>
                                    @endif

                                    @if ($errors->has('email'))
                                        <span class="help-block">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                    {{ Form::label('title', trans('cortex/auth::common.title')) }}
                                    {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => $currentUser->title ?: trans('cortex/auth::common.title')]) }}

                                    @if ($errors->has('title'))
                                        <span class="help-block">{{ $errors->first('title') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('country_code') ? ' has-error' : '' }}">
                                    {{ Form::label('country_code', trans('cortex/auth::common.country')) }}
                                    {{ Form::hidden('country_code', '') }}
                                    {{ Form::select('country_code', [], null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/auth::common.select_country'), 'data-allow-clear' => 'true', 'data-width' => '100%']) }}

                                    @if ($errors->has('country_code'))
                                        <span class="help-block">{{ $errors->first('country_code') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('language_code') ? ' has-error' : '' }}">
                                    {{ Form::label('language_code', trans('cortex/auth::common.language')) }}
                                    {{ Form::hidden('language_code', '') }}
                                    {{ Form::select('language_code', $languages, null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/auth::common.select_language'), 'data-allow-clear' => 'true', 'data-width' => '100%']) }}

                                    @if ($errors->has('language_code'))
                                        <span class="help-block">{{ $errors->first('language_code') }}</span>
                                    @endif
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">

                                <div class="form-group has-feedback{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    {{ Form::label('phone', trans('cortex/auth::common.phone')) }}
                                    {{ Form::tel('phone', null, ['class' => 'form-control', 'placeholder' => $currentUser->phone ?: trans('cortex/auth::common.phone')]) }}

                                    @if ($currentUser->phone_verified)
                                        <small class="text-success">{!! trans('cortex/auth::common.phone_verified', ['date' => $currentUser->phone_verified_at]) !!}</small>
                                    @elseif($currentUser->phone)
                                        <small class="text-danger">{!! trans('cortex/auth::common.phone_unverified', ['href' => route('frontarea.verification.phone.request')]) !!}</small>
                                    @endif

                                    <span class="help-block hide">{{ trans('cortex/foundation::messages.invalid_phone') }}</span>
                                    @if ($errors->has('phone'))
                                        <span class="help-block">{{ $errors->first('phone') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                    {{ Form::label('gender', trans('cortex/auth::common.gender')) }}
                                    {{ Form::hidden('gender') }}
                                    {{ Form::hidden('gender', '') }}
                                    {{ Form::select('gender', $genders, null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/auth::common.select_gender'), 'data-allow-clear' => 'true', 'data-minimum-results-for-search' => 'Infinity', 'data-width' => '100%']) }}

                                    @if ($errors->has('gender'))
                                        <span class="help-block">{{ $errors->first('gender') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                <div class="form-group has-feedback{{ $errors->has('birthday') ? ' has-error' : '' }}">
                                    {{ Form::label('birthday', trans('cortex/auth::common.birthday')) }}

                                    {{ Form::text('birthday', null, ['class' => 'form-control datepicker', 'data-locale' => '{"format": "YYYY-MM-DD"}', 'data-single-date-picker' => 'true', 'data-show-dropdowns' => 'true', 'data-auto-apply' => 'true']) }}

                                    @if ($errors->has('birthday'))
                                        <span class="help-block">{{ $errors->first('birthday') }}</span>
                                    @endif
                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-4">

                                {{-- Profile Picture --}}
                                <div class="form-group has-feedback{{ $errors->has('profile_picture') ? ' has-error' : '' }}">
                                    {{ Form::label('profile_picture', trans('cortex/auth::common.profile_picture'), ['class' => 'control-label']) }}

                                    <div class="input-group">
                                        {{ Form::text('profile_picture', null, ['class' => 'form-control file-name', 'placeholder' => trans('cortex/auth::common.profile_picture'), 'readonly' => 'readonly']) }}

                                        <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file">
                                                        {{ trans('cortex/auth::common.browse') }}
                                                        {{ Form::file('profile_picture', ['class' => 'form-control']) }}
                                                    </span>
                                                </span>
                                    </div>

                                    @if ($currentUser->exists && $currentUser->getMedia('profile_picture')->count())
                                        <i class="fa fa-paperclip"></i>
                                        <a href="{{ $currentUser->getFirstMediaUrl('profile_picture') }}" target="_blank">{{ $currentUser->getFirstMedia('profile_picture')->file_name }}</a> ({{ $currentUser->getFirstMedia('profile_picture')->human_readable_size }})
                                        <a href="#" data-toggle="modal" data-target="#delete-confirmation" data-modal-action="{{ route('adminarea.admins.media.destroy', ['admin' => $currentUser, 'media' => $currentUser->getFirstMedia('profile_picture')]) }}" data-modal-title="{{ trans('cortex/foundation::messages.delete_confirmation_title') }}" data-modal-body="{{ trans('cortex/foundation::messages.delete_confirmation_body', ['resource' => 'media', 'identifier' => $currentUser->getFirstMedia('profile_picture')->file_name]) }}" title="{{ trans('cortex/foundation::common.delete') }}"><i class="fa fa-trash text-danger"></i></a>
                                    @endif

                                    @if ($errors->has('profile_picture'))
                                        <span class="help-block">{{ $errors->first('profile_picture') }}</span>
                                    @endif
                                </div>

                            </div>

                            <div class="col-md-4">

                                {{-- Cover Photo --}}
                                <div class="form-group has-feedback{{ $errors->has('cover_photo') ? ' has-error' : '' }}">
                                    {{ Form::label('cover_photo', trans('cortex/auth::common.cover_photo'), ['class' => 'control-label']) }}

                                    <div class="input-group">
                                        {{ Form::text('cover_photo', null, ['class' => 'form-control file-name', 'placeholder' => trans('cortex/auth::common.cover_photo'), 'readonly' => 'readonly']) }}

                                        <span class="input-group-btn">
                                                    <span class="btn btn-default btn-file">
                                                        {{ trans('cortex/auth::common.browse') }}
                                                        {{ Form::file('cover_photo', ['class' => 'form-control']) }}
                                                    </span>
                                                </span>
                                    </div>

                                    @if ($currentUser->exists && $currentUser->getMedia('cover_photo')->count())
                                        <i class="fa fa-paperclip"></i>
                                        <a href="{{ $currentUser->getFirstMediaUrl('cover_photo') }}" target="_blank">{{ $currentUser->getFirstMedia('cover_photo')->file_name }}</a> ({{ $currentUser->getFirstMedia('cover_photo')->human_readable_size }})
                                        <a href="#" data-toggle="modal" data-target="#delete-confirmation" data-modal-action="{{ route('adminarea.admins.media.destroy', ['admin' => $currentUser, 'media' => $currentUser->getFirstMedia('cover_photo')]) }}" data-modal-title="{{ trans('cortex/foundation::messages.delete_confirmation_title') }}" data-modal-body="{{ trans('cortex/foundation::messages.delete_confirmation_body', ['resource' => 'media', 'identifier' => $currentUser->getFirstMedia('cover_photo')->file_name]) }}" title="{{ trans('cortex/foundation::common.delete') }}"><i class="fa fa-trash text-danger"></i></a>
                                    @endif

                                    @if ($errors->has('cover_photo'))
                                        <span class="help-block">{{ $errors->first('cover_photo') }}</span>
                                    @endif
                                </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12 text-center profile-buttons">
                                {{ Form::button('<i class="fa fa-save"></i> '.trans('cortex/auth::common.update_settings'), ['class' => 'btn btn-primary btn-flat', 'type' => 'submit']) }}
                            </div>
                        </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

@endsection
