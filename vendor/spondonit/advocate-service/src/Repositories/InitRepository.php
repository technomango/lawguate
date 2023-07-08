<?php

namespace SpondonIt\AdvocateService\Repositories;

use Modules\RolePermission\Entities\Role;
use Modules\Setting\Model\DateFormat;
use Modules\Setting\Entities\Config;

class InitRepository {

    public function init() {
		config([
            'app.item' => '31004954',
            'spondonit.module_manager_model' => \Modules\ModuleManager\Entities\InfixModuleManager::class,
            'spondonit.module_manager_table' => 'infix_module_managers',

            'spondonit.settings_model' => \Modules\Setting\Entities\Config::class,
            'spondonit.module_model' => \Nwidart\Modules\Facades\Module::class,

            'spondonit.user_model' => \App\User::class,
            'spondonit.settings_table' => 'general_settings',
            'spondonit.database_file' => '',
            'spondonit.saas_module_name' => 'AdvSaas',
        ]);
    }

    public function config()
	{
        app()->singleton('permission_list', function() {
            return Role::with(['permissions' => function($query){
                $query->select('route','module_id','parent_id','role_id');
            }])->get(['id','name']);
        });


        $configs = Config::pluck('value', 'key')->toArray();

        $date_format_id = gv($configs, 'configs.date_format_id', 1);
        $dateFormat = DateFormat::find($date_format_id);

        config([
            'configs' => $configs,
            'date_format' => $dateFormat ? $dateFormat->format : 'Y-m-d'
        ]);

    }

}
