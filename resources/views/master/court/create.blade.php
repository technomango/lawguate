@extends('layouts.master', ['title' => __('court.Create New Court')])

@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex justify-content-between w-100">
                            <h3 class="mb-0 mr-30">{{ __('court.New Court') }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">


                        {!! Form::open(['route' => 'master.court.store', 'class' => 'form-validate-jquery', 'id' => 'content_form', 'files' => false, 'method' => 'POST']) !!}
                        <div class="row">
                            <div class="primary_input col-md-4">
                                {{Form::label('country_id', __('court.Country'))}}
                                {{Form::select('country_id', $countries, config('configs.country_id'), ['class' => 'primary_select', 'id' => 'country_id', 'data-placeholder' => __('court.Select country'),  'data-parsley-errors-container' => '#country_id_error'])}}
                                <span id="country_id_error"></span>
                            </div>

                            <div class="primary_input col-md-4">
                                {{Form::label('state_id', __('court.State'))}}
                                {{Form::select('state_id', $states, null, ['class' => 'primary_select','id' => 'state_id', 'data-placeholder' => __('court.Select state'), 'data-parsley-errors-container' => '#state_id_error'])}}
                                <span id="state_id_error"></span>
                            </div>

                            <div class="primary_input col-md-4">
                                {{Form::label('city_id', __('court.City'))}}
                                {{Form::select('city_id',[''=> __('common.Select State First')], null, ['class' => 'primary_select','id' => 'city_id', 'data-placeholder' => __('court.Select city'), 'data-parsley-errors-container' => '#city_id_error'])}}
                                <span id="city_id_error"></span>
                            </div>

                        </div>
                        <div class="row">

                            <div class="primary_input col-md-6">

                                <div class="d-flex justify-content-between">
                                    {{Form::label('court_category_id', __('court.Court Category'), ['class' => 'required'])}}
                                    @if(permissionCheck('category.court.store'))
                                        <label class="primary_input_label green_input_label" for="">
                                            <a href="{{ route('category.court.create', ['quick_add' => true]) }}"
                                               class="btn-modal"
                                               data-container="court_category_add_modal">{{ __('case.Create New') }}
                                                <i class="fas fa-plus-circle"></i></a></label>
                                    @endif
                                </div>
                                {{Form::select('court_category_id', $court_categories, null, ['class' => 'primary_select', 'data-placeholder' => __('court.Court Category'), 'data-parsley-errors-container' => '#court_category_error', 'required'])}}
                                <span id="court_category_error"></span>
                            </div>
                            <div class="primary_input col-md-6">
                                {{Form::label('location', __('court.Location/ Police Station'))}}
                                {{Form::text('location', null, ['class' => 'primary_input_field', 'placeholder' => __('court.Location/ Police Station')])}}
                            </div>
                        </div>
                        <div class="row">
                            <div class="primary_input col-md-6">
                                {{Form::label('name', __('court.Court Name'),['class' => 'required'])}}
                                {{Form::text('name', null, ['required' => '', 'class' => 'primary_input_field', 'placeholder' => __('court.Court Name')])}}
                            </div>
                            <div class="primary_input col-md-6">
                                {{Form::label('room_number', __('court.Court Room Number'))}}
                                {{Form::text('room_number', null, ['class' => 'primary_input_field', 'placeholder' => __('court.Court Room Number')])}}
                            </div>
                        </div>
                        @if(moduleStatusCheck('EmailtoCL'))
                            <div class="row">

                                <div class="primary_input col-12">
                                    {{Form::label('email', __('case.Email'))}}
                                    {{Form::email('email', null, ['class' => 'primary_input_field', 'placeholder' => __('case.Email')])}}
                                </div>

                            </div>
                        @endif
                        @includeIf('customfield::fields', ['fields' => $fields, 'model' => null])
                        <div class="primary_input">
                            {{Form::label('description', __('court.Description'))}}
                            {{Form::textarea('description', null, ['class' => 'primary_input_field summernote', 'placeholder' => __('court.Court  Description'), 'rows' => 5, 'maxlength' => 1500, 'data-parsley-errors-container' =>
                            '#description_error' ])}}
                            <span id="description_error"></span>
                        </div>

                        <div class="text-center mt-3">
                            <button class="primary-btn semi_large2 fix-gr-bg submit" type="submit"><i
                                    class="ti-check"></i>{{ __('common.Create') }}
                            </button>

                            <button class="primary-btn semi_large2 fix-gr-bg submitting" type="submit" disabled
                                    style="display: none;"><i class="ti-check"></i>{{ __('common.Creating') . '...' }}
                            </button>

                        </div>
                        {!! Form::close() !!}


                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade animated court_category_add_modal infix_advocate_modal" id="remote_modal" tabindex="-1"
         role="dialog"
         aria-labelledby="remote_modal_label" aria-hidden="true" data-backdrop="static">
    </div>


@stop
@push('admin.scripts')
    <script>
        $(document).ready(function () {
            _componentAjaxChildLoad('#content_form', '#country_id', '#state_id', 'state')
            _componentAjaxChildLoad('#content_form', '#state_id', '#city_id', 'city')
            _formValidation();
        });
    </script>
@endpush
