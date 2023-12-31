@extends('backEnd.master')
@section('mainContent')
    <link rel="stylesheet" href="{{asset('Modules/ModuleManager/Resources/assets/sass/manage_module.css')}}">
    <link rel="stylesheet" href="{{ asset('public/vendor/spondonit/css/parsley.css') }}">

    <section class="admin-visitor-area">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-9 col-xs-6 col-md-6 col-6 no-gutters ">
                            <div class="main-title sm_mb_20 sm2_mb_20 md_mb_20 mb-30 ">
                                <h3 class="mb-0"> {{__('setting.Module')}} {{__('setting.Manage')}}</h3>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-6 col-md-6 col-6 no-gutters ">
                            <a data-toggle="modal"
                               data-target="#add_module" href="#"
                               class="primary-btn fix-gr-bg small pull-right">  {{__('common.Add')}}
                                / {{__('common.Update')}} {{__('setting.Module')}}</a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">

                            <table class="display school-table school-table-style" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>{{__('setting.SL')}}</th>
                                    <th>{{__('setting.Module')}}{{__('setting.Name')}}</th>
                                    <th>{{__('setting.Description')}}</th>
                                    <th class="text-right"></th>
                                </tr>
                                </thead>

                                <tbody>
                                <input type="hidden" name="url" id="url" value="{{URL::to('/')}}">
                                @php
                                    $count=1;
                                @endphp
                                @foreach($is_module_available as $row)

                                    @php
                                        $is_module_available = base_path().'/'.'Modules/' . $row->name. '/Providers/' .$row->name. 'ServiceProvider.php';
                                        $configFile = 'Modules/' . $row->name. '/' .$row->name. '.json';
                                        $is_data =  \Modules\ModuleManager\Entities\InfixModuleManager::where('name', $row->name)->first();

                                    try {
                                        $config =file_get_contents(base_path().'/'.$configFile);
                                         }catch (\Exception $exception){
                                            $config =null;
                                         }
                                    @endphp
                                    <tr>
                                        <td>{{@$count++}}</td>
                                        <td>
                                            {{@$row->name}}
                                            @if(!empty($is_data->purchase_code)) <p class="text-success">
                                                {{__('setting.Verified')}}
                                                on {{date("F jS, Y", strtotime(@$is_data->activated_date))}}</p>
                                            <a href="#" class="module_switch" data-id="{{@$row->name}}"
                                               id="module_switch_label{{@$row->name}}"
                                               >
                                                {{ __('common.'.(!moduleStatusCheck($row->name ) ? 'Activate':'Deactivate')) }}
                                            </a>
                                            @includeIf('service::license.revoke-module', ['name' =>$row->name, 'row' => true, 'file' =>false])
                                            <div id="waiting_loader"
                                                 class="waiting_loader{{@$row->name}}">
                                                <img
                                                    src="{{asset('public/backEnd/img/demo_wait.gif')}}"
                                                    width="20" height="20"/><br>

                                            </div>

                                            @else<p class="text-danger">
                                                @if (! file_exists($is_module_available))
                                                @else

                                                    <a class=" verifyBtn"
                                                       data-toggle="modal" data-id="{{@$row->name}}"
                                                       data-target="#Verify"
                                                       href="#">   {{__('setting.Verify')}}</a>
                                                @endif
                                                @endif
                                            </p>
                                        </td>
                                        <td>
                                            @if(isset($config))
                                                @php
                                                    $name = $row->name;
                                                    $config= json_decode($config);
                                                    if (isset($config->$name->notes[0])){
                                                    echo $config->$name->notes[0];
                                                    echo '<br>';
                                                    echo 'Version: '.$config->$name->versions[0].' | Developed By <a href="https://spondonit.com/">SpondonIT</a>';

                                                    }
                                                @endphp
                                            @else
                                                @php
                                                    if (isset($row->details)){
                                                        echo $row->details;
                                                    }
                                                @endphp
                                            @endif
                                        </td>

                                        <td class="text-right">
                                            @if (! file_exists($is_module_available))
                                                <div class="row">
                                                    <div class="col-lg-12 ">
                                                        <a class="primary-btn fix-gr-bg" style="white-space: nowrap;"
                                                           href="mailto:support@spondonit.com">   {{__('common.Buy Now')}}</a>

                                                    </div>
                                                </div>
                                            @endif
                                            @if (file_exists($is_module_available))
                                                @php
                                                    $system_settings= \Modules\Setting\Entities\Config::where('key', $row->name)->first();
                                                     $is_moduleV= $is_data;
                                                     $configName = $row->name;
                                                     $availableConfig=0;

                                                @endphp

                                                @if(@$availableConfig==0 || @@$is_moduleV->purchase_code== null)
                                                    <input type="hidden" name="name" value="{{@$configName}}">


                                                @else
                                                    <div class="row">
                                                        <div class="col-lg-12 ">
                                                            @if('RolePermission' != $row->name && 'TemplateSettings' != $row->name )
                                                                <div id="waiting_loader"
                                                                     class="waiting_loader{{@$row->name}}">
                                                                </div>
                                                            @endif

                                                        </div>
                                                @endif
                                            @endif

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
    </section>

    <div class="modal fade admin-query" id="add_module">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add New / Update Module</h4>
                    <button type="button" class="close " data-dismiss="modal">
                        <i class="ti-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{route('modulemanager.uploadModule')}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-xl-12">
                                <div class="primary_input mb-35">

                                    <div class="primary_file_uploader">
                                        <input class="primary-input filePlaceholder" type="text"
                                               id="document_file_placeholder"
                                               placeholder="Select Module File"
                                               readonly="">
                                        <button class="" type="button">
                                            <label class="primary-btn small fix-gr-bg"
                                                   for="document_file">{{__('common.Browse')}}</label>
                                            <input type="file" class="d-none fileUpload" name="module"
                                                   id="document_file" onchange="getFileName(this.value, '#document_file_placeholder')">
                                        </button>
                                    </div>
                                </div>
                            </div>


                        </div>

                        <div class="col-lg-12 text-center pt_15">
                            <div class="d-flex justify-content-center">
                                <button class="primary-btn semi_large2  fix-gr-bg" id="save_button_parent"
                                        type="submit"><i
                                        class="ti-check"></i> {{__('common.Save')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade admin-query" id="Verify">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Module Verification</h4>
                    <button type="button" class="close" data-dismiss="modal"><i class="ti-close"></i>
                    </button>
                </div>

                <div class="modal-body">
                    {{ Form::open(['id'=>"content_form",'class' => 'form-horizontal', 'files' => true, 'route' => 'ManageAddOnsValidation', 'method' => 'POST']) }}
                    <input type="hidden" name="name" value="" id="moduleName">
                    <input type="hidden" name="row" value="1">

                    @csrf
                    <div class="form-group">
                        <label for="user">Envato Email Address :</label>
                        <input type="text" class="form-control " name="envatouser"
                               required="required"
                               placeholder="Enter Your Envato Email Address"
                               value="{{old('envatouser')}}">
                    </div>
                    <div class="form-group">
                        <label for="purchasecode">Envato Purchase Code:</label>
                        <input type="text" class="form-control" name="purchase_code"
                               required="required"
                               placeholder="Enter Your Envato Purchase Code"
                               value="{{old('purchasecode')}}">
                    </div>
                    <div class="form-group">
                        <label for="domain">Installation Path:</label>
                        <input type="text" class="form-control"
                               name="installationdomain" required="required"
                               placeholder="Enter Your Installation Domain"
                               value="{{url('/')}}" readonly>
                    </div>
                    <div class="row mt-40">
                        <div class="col-lg-12 text-center">
                            <button class="primary-btn fix-gr-bg submit">
                                <span class="ti-check"></span>
                                {{__('setting.Verify')}}
                            </button>
                            <button type="button" class="primary-btn fix-gr-bg submitting" style="display: none">
                                <i class="fas fa-spinner fa-pulse"></i>
                                Verifying
                            </button>
                        </div>
                    </div>

                    {{ Form::close() }}
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{asset('public/backEnd/js/module.js')}}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/parsley.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/function.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/vendor/spondonit/js/common.js') }}"></script>
    <script type="text/javascript">
        _formValidation('content_form');
    </script>
@endpush
