@extends('layouts.master', ['title' => __('contact.Create New Contact')])

@section('mainContent')


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header">
                        <div class="main-title d-flex justify-content-between w-100">
                            <h3 class="mb-0 mr-30">{{__('contact.Add Contact')}}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">

                        {!! Form::open(['route' => 'contact.store', 'class' => 'form-validate-jquery', 'id' => 'content_form', 'files' => false, 'method' => 'POST']) !!}
                        <div class="row">
                            <div class="primary_input col-md-12">

                                <div class="d-flex justify-content-between">
                                    {{Form::label('contact_category_id', __('contact.Category'))}}
                                    @if(permissionCheck('category.contact.store'))
                                        <label class="primary_input_label green_input_label" for="">
                                            <a href="{{ route('category.contact.create', ['quick_add' => true]) }}"
                                               class="btn-modal"
                                               data-container="contact_category_add_modal">{{ __('case.Create New') }}
                                                <i class="fas fa-plus-circle"></i></a></label>
                                    @endif
                                </div>
                                {{Form::select('contact_category_id', $contact_categories, null, ['class' => 'primary_select', 'data-parsley-errors-container' => '#contact_category_id_error'])}}
                                <span id="contact_category_id_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="primary_input col-md-12">
                                {{Form::label('name', __('contact.Name'), ['class' => 'required'])}}
                                {{Form::text('name', null, ['required' => '','class' => 'primary_input_field required', 'placeholder' => __('contact.Name')])}}
                            </div>
                            <div class="primary_input col-md-6">
                                {{Form::label('mobile_no', __('contact.Mobile No'))}}
                                {{Form::number('mobile_no', null, ['class' => 'primary_input_field ', 'placeholder' => __('contact.Mobile No')])}}
                            </div>
                            <div class="primary_input col-md-6">
                                {{Form::label('email', __('contact.Email'))}}
                                {{Form::email('email', null, ['class' => 'primary_input_field', 'placeholder' => __('contact.Email')])}}
                            </div>
                        </div>

                        @includeIf('customfield::fields', ['fields' => $fields, 'model' => null])
                        <div class="primary_input">
                            {{Form::label('description', __('contact.Description'))}}
                            {{Form::textarea('description', null, ['class' => 'primary_input_field summernote', 'placeholder' => __('contact.Contact Description'), 'rows' => 5, 'data-parsley-errors-container' => '#description_error' ])}}
                            <span id="description_error"></span>
                        </div>

                        <div class="text-center mt-3">
                            <button type="submit" class="primary-btn semi_large2 fix-gr-bg submit" id="submit"
                                    value="submit">{{ __
                                ('common.Create')
                                }}</button>

                            <button type="button" class="primary-btn semi_large2 fix-gr-bg submitting" id="submit"
                                    disabled style="display: none;">{{ __
                                ('common.Creating')
                                }}...
                            </button>
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade animated contact_category_add_modal infix_advocate_modal" id="remote_modal" tabindex="-1"
         role="dialog"
         aria-labelledby="remote_modal_label" aria-hidden="true" data-backdrop="static">
    </div>

@stop
@push('admin.scripts')
    <script>
        $(document).ready(function () {
            _formValidation();
        });
    </script>
@endpush
