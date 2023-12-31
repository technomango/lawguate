@extends('layouts.master', ['title' => __('case.Case')])

@section('mainContent')


    <section class="admin-visitor-area up_st_admin_visitor">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="box_header common_table_header xs_mb_0">
                        <div class="main-title d-md-flex">
                            <h3 class="mb-0 mr-30 mb_xs_15px mb_sm_20px">{{ __('case.' . $page_title . ' Case') }}</h3>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="QA_section QA_section_heading_custom check_box_table">
                        <div class="QA_table ">
                            <!-- table-responsive -->
                            <div class="">
                                <table class="table Crm_table_active3">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('common.SL') }}</th>
                                            <th scope="col">{{ __('client.Client') }}</th>
                                            <th scope="col">{{ __('case.Category') }}</th>
                                            <th scope="col">{{ __('case.Ref Name') }}</th>
                                            <th scope="col">{{ __('case.Ref Phone') }}</th>
                                            <th scope="col">{{ __('common.Type') }}</th>
                                            <th scope="col">{{ __('case.Stage') }}</th>
                                            {{-- <th scope="col">{{ __('case.Details') }}</th> --}}
                                            <th scope="col">{{ __('common.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($models as $model)
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $model->client->name }}</td>
                                                <td>{{ $model->caseCategory->name }}</td>
                                                <td>{{ $model->ref_name }}</td>
                                                <td>{{ $model->ref_mobile }}</td>
                                                <td>{{ strtoupper($model->type) }}</td>
                                                <td>{{ $model->caseStage->name }}</td>
                                                {{-- <td> {!! $model->description !!} </td> --}}


                                                <td>


                                                    <div class="dropdown CRM_dropdown">
                                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                                            id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true"
                                                            aria-expanded="false">
                                                            {{ __('common.Select') }}
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right"
                                                            aria-labelledby="dropdownMenu2">

                                                            <a href="{{ route('clientCase.show', [$model->id]) }}"
                                                                class="dropdown-item"><i class="icon-file-eye"></i>
                                                                {{ __('common.View') }}</a>

                                                            <a href="{{ route('clientCase.convert', [$model->id]) }}"
                                                                target="_blank"
                                                                class="dropdown-item"><i class="icon-file-eye"></i>
                                                                {{ __('case.Convert To Case') }}</a>
                                                            
                                                            @if (permissionCheck('clientCase.destroy'))
                                                                <span id="delete_item" data-id="{{ $model->id }}"
                                                                    data-url="{{ route('clientCase.destroy', [$model->id]) }}"
                                                                    class="dropdown-item"><i class="icon-trash"></i>
                                                                    {{ __('common.Delete') }}
                                                                </span>
                                                            @endif

                                                        </div>
                                                    </div>


                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop
@push('admin.scripts')
    <script>
        $(document).ready(function() {

        });
    </script>
@endpush
