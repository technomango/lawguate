@extends('layouts.master', ['title' => __('case.Create New Case Stage')])

@section('mainContent')

<section class="admin-visitor-area up_st_admin_visitor">
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="box_header">
                    <div class="main-title d-flex justify-content-between w-100">
                        <h3 class="mb-0 mr-30">{{__('case.Add Case stage')}}</h3>

                    </div>
                </div>
            </div>
                <div class="col-12">
                    <div class="white_box_50px box_shadow_white">

                        {!! Form::open(['route' => 'master.stage.store', 'class' => 'form-validate-jquery', 'id' =>
                        'content_form', 'files' => false, 'method' => 'POST']) !!}
                        <div class="primary_input">
                            {{Form::label('name', __('case.Name'), ['class' => 'required'])}}
                            {{Form::text('name', null, ['required' => '', 'class' => 'primary_input_field', 'placeholder' => __('case.Case Stage Name')])}}
                        </div>

                        <div class="primary_input">
                            {{Form::label('description', __('case.Description'))}}
                            {{Form::textarea('description', null, ['class' => 'primary_input_field', 'placeholder' =>
                            __('case.Case Stage Description'), 'rows' => 5, 'maxlength' => 1500,
                            'data-parsley-errors-container' =>
                            '#description_error' ])}}
                            <span id="description_error"></span>
                        </div>

                        <div class="text-center mt-3">
                        <button class="primary-btn semi_large2 fix-gr-bg submit" type="submit"><i class="ti-check"></i>{{ __('common.Create') }}
                                    </button>

                                    <button class="primary-btn semi_large2 fix-gr-bg submitting" type="submit" disabled style="display: none;"><i class="ti-check"></i>{{ __('common.Creating') . '...' }}
                                    </button>

                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@stop

@push('admin.scripts')

    <script>
        $(document).ready(function () {
            _formValidation();

        });
    </script>
@endpush

