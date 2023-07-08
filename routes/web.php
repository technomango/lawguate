<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Modules\ModuleManager\Entities\Module;
use Modules\Setting\Model\EmailTemplate;

if (moduleStatusCheck('AdvSaas')) {
    Route::group(['middleware' => ['subdomain']], function ($routes) {
        require('tenant.php');
    });
} else {
    require('tenant.php');
}

//Route::get('/{page}', 'PageController@frontShow')->name('front.page.show');
Route::get('test', function () {
    $modules = Module::all();
    $infixModule = \Modules\ModuleManager\Entities\InfixModuleManager::first();

    foreach($modules as $module){
        $name = $module->name;
        if(!\Modules\ModuleManager\Entities\InfixModuleManager::where('name', $name)->first()){
            $infixModule = $infixModule->replicate();
            $infixModule->name = $name;
            $infixModule->save();

            $module->status = 1;
            $module->save();
        }

        $moduleCheck = \Nwidart\Modules\Facades\Module::find($name);
        $moduleCheck->enable();
    }

    Cache::forget('ModuleList');
    Cache::forget('ModuleManagerList');

    \Artisan::call('migrate', ['--force' => true]);



});
