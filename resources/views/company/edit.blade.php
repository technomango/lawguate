@extends('layouts.master', ['title' => __('client.Update Company')])

@section('mainContent')

    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex justify-content-between w-100">
                            <h3 class="mb-0 mr-30">{{__('client.Update Company')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">
                        {!! Form::model($model, ['route' => ['company.update', $model->id], 'class' => 'form-validate-jquery', 'id' => 'content_form', 'method' => 'PUT']) !!}
                        <div class="row">
                            <div class="primary_input col-md-6">
                                {{Form::label('name', __('client.Company Name'), ['class' => 'required'])}}
                                {{Form::text('name', null, ['required' => '', 'class' => 'primary_input_field', 'placeholder' => __('client.Company Name')])}}
                            </div>
                            <div class="primary_input col-md-6">
                                {{Form::label('mobile', __('client.Company Mobile'))}}
                                {{Form::text('mobile', null, ['class' => 'primary_input_field', 'placeholder' => __('client.Company Mobile')])}}
                            </div>
                        </div>

                        <div class="row">
                            <div class="primary_input col-md-6">
                                {{Form::label('email', __('client.Company Email'), ['class' => ($enable_login ? 'required' : '')])}}
                                {{Form::email('email', null, ['class' => 'primary_input_field', 'placeholder' => __('client.Company Email'), ($enable_login ? 'required' : '')])}}
                            </div>
                            @if($enable_login)
                                <div class="primary_input col-md-6">
                                    {{Form::label('password', __('client.Password'), ['class' => (($enable_login and !$model->user) ? 'required' : '')])}}
                                    {{Form::text('password', null, ['class' => 'primary_input_field', 'placeholder' => __('client.Password'), (($enable_login and !$model->user) ? 'required' : ''), 'minlength' => 8])}}
                                </div>
                            @endif

                        </div>


                        <div class="row">
                            <div class="primary_input col-md-6">
                                {{Form::label('gender', __('client.Gender'))}}
                                {{Form::select('gender', ['Male' => 'Male', 'FeMale' => 'FeMale'], null, ['class' => 'primary_select', 'data-placeholder' => __('client.Select Gender'), 'data-parsley-errors-container' => '#gender_error'])}}
                                <span id="gender_error"></span>
                            </div>
                            <div class="primary_input col-md-6">
                                <div class="d-flex justify-content-between">
                                    {{Form::label('client_category_id', __('client.Company Category'))}}
                                    @if(permissionCheck('category.company.store'))
                                        <label class="primary_input_label green_input_label" for="">
                                            <a href="{{ route('category.company.create', ['quick_add' => true]) }}"
                                               class="btn-modal"
                                               data-container="company_category_add_modal">{{ __('case.Create New') }}
                                                <i class="fas fa-plus-circle"></i></a></label>
                                    @endif
                                </div>
                                {{Form::select('client_category_id', $company_categories, null, ['class' => 'primary_select', 'data-placeholder' => __('client.Select Division'),  'data-parsley-errors-container' => '#client_category_id_error'])}}
                                <span id="client_category_id_error"></span>
                            </div>

                        </div>

                        <div class="row">

                            <div class="primary_input col-md-4">
                                {{Form::label('country_id', __('client.Country'))}}
                                {{Form::select('country_id', $countries, config('configs.country_id'), ['class' => 'primary_select', 'id' => 'country_id', 'data-placeholder' => __('client.Select country'),  'data-parsley-errors-container' => '#country_id_error'])}}
                                <span id="country_id_error"></span>
                            </div>

                            <div class="primary_input col-md-4">
                                {{Form::label('state_id', __('client.State'))}}
                                {{Form::select('state_id', $states, null, ['class' => 'primary_select','id' => 'state_id', 'data-placeholder' => __('client.Select state'), 'data-parsley-errors-container' => '#state_id_error'])}}
                                <span id="state_id_error"></span>
                            </div>

                            <div class="primary_input col-md-4">
                                {{Form::label('city_id', __('client.City'))}}
                                {{Form::select('city_id',$cities, null, ['class' => 'primary_select','id' => 'city_id', 'data-placeholder' => __('client.Select city'), 'data-parsley-errors-container' => '#city_id_error'])}}
                                <span id="city_id_error"></span>
                            </div>

                        </div>

                        <div class="primary_input">
                            {{Form::label('address', __('client.Company Address'))}}
                            {{Form::textarea('address', null, [ 'class' => 'primary_input_field', 'placeholder' => __('client.Company Address'), 'rows' => 3])}}
                        </div>
                        @includeIf('customfield::fields', ['fields' => $fields, 'model' => $model])
                        <div class="primary_input">
                            {{Form::label('description', __('client.Description'))}}
                            {{Form::textarea('description', null, ['class' => 'primary_input_field summernote', 'placeholder' => __('client.Client Description'), 'rows' => 5, 'maxlength' => 1500, 'data-parsley-errors-container' =>
                            '#description_error' ])}}
                            <span id="description_error"></span>
                        </div>
                        <div class="text-center mt-3">
                            <button class="primary_btn_large submit" type="submit"><i
                                    class="ti-check"></i>{{ __('common.Update') }}
                            </button>

                            <button class="primary_btn_large submitting" type="submit" disabled style="display: none;">
                                <i class="ti-check"></i>{{ __('common.Updating') . '...' }}
                            </button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal fade animated company_category_add_modal infix_advocate_modal" id="remote_modal" tabindex="-1"
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
