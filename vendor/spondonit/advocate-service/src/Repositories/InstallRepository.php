<?php

namespace SpondonIt\AdvocateService\Repositories;
ini_set('max_execution_time', -1);

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Schema;
use SpondonIt\Service\Repositories\InstallRepository as ServiceInstallRepository;
use Modules\Setting\Entities\Config;
use Modules\Setting\Database\Seeders\ConfigTableSeeder;
use Throwable;

class InstallRepository {

    protected $installRepository;
	/**
	 * Instantiate a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(ServiceInstallRepository $installRepository) {
        $this->installRepository = $installRepository;
	}



	/**
	 * Install the script
	 */
	public function install($params) {

        try{
            $admin = $this->makeAdmin($params);
            Artisan::call('db:seed',['--class' => ConfigTableSeeder::class, '--force' => true]);
            $this->installRepository->seed(gbv($params, 'seed'));
            $this->postInstallScript($admin, $params);


            Artisan::call('key:generate', ['--force' => true]);

            envu([
                'APP_ENV' => 'production',
                'APP_DEBUG'     =>  'false',
            ]);



        } catch(\Exception $e){

            Storage::delete(['.user_email', '.user_pass']);

            throw ValidationException::withMessages(['message' => $e->getMessage()]);

        }
	}

	public function postInstallScript($admin, $params){
		Config::where('key', 'system_activated_date')->update(['value'=> date('Y-m-d')]);
		Config::where('key', 'system_domain')->update(['value'=> app_url()]);
	}




	/**
	 * Insert default admin details
	 */
	public function makeAdmin($params) {
        try{
            $user_model_name = config('spondonit.user_model');
            $user_class = new $user_model_name;
            $user = $user_class->find(1);
            if(!$user){
               $user = new $user_model_name;
            }
            $user->name = 'Super admin';
            $user->email = gv($params, 'email');
            if(Schema::hasColumn('users', 'role_id')){
                $user->role_id = 1;
            }
            if(\Illuminate\Support\Facades\Config::get('app.app_sync')){
                $user->password = bcrypt(12345678);

            }else{
                $user->password = bcrypt(gv($params, 'password', 'abcd1234'));
            }

            $user->save();
        } catch(\Exception $e){
            $this->installRepository->rollbackDb();
            throw ValidationException::withMessages(['message' => $e->getMessage()]);
        }


	}

}
