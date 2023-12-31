<?php

namespace Modules\Localization\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Lang;
use Modules\Localization\Entities\Language;
use Modules\Localization\Repositories\LanguageRepositoryInterface;
use Modules\Setting\Entities\Config;

class LanguageController extends Controller
{
    protected $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->middleware(['auth']);
        $this->languageRepository = $languageRepository;
    }

    public function index(Request $request)
    {

        try{
            $search_keyword = null;
            if ($request->input('search_keyword') != null) {
                $search_keyword = $request->input('search_keyword');
                $languages = $this->languageRepository->serachBased($search_keyword);
            }
            else {
                $languages = $this->languageRepository->all();
            }
            return view('localization::languages.index', [
                "languages" => $languages,
                "search_keyword" => $search_keyword
            ]);

        }catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update_rtl_status(Request $request)
    {
        try{
            $language = Language::findOrFail($request->id);
            $language->rtl = $request->status;
            if($language->save()){
                Artisan::call('cache:clear');
                return 1;
            }
            return 0;
        }catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function update_active_status(Request $request)
    {
        try{
            $language = Language::findOrFail($request->id);
            $language->status = $request->status;
            if($language->save()){
                Cache::forget('languages');
                $languages = Language::where('status' ,1)->get();
                $output = '';
                if(session()->has('locale')){
                    $locale = session('locale', config('configs.language_name'));
                }
                else{
                    $locale = 'en';
                }

                foreach ($languages as $language)
                {


                    $output .= '<option value="'.$language->code.'" '.($locale ==  $language->code ? 'selected' : '').'>'.$language->name.'</option>';
                }
                return response()->json([
                    'success' => trans('common.Successfully Updated'),
                    'languages' => $output,
                ]);
            }
            return response()->json(['error' => trans('common.Something Went Wrong')]);

        }catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }

    }

    public function store(Request $request)
    {

        $validate_rules = [
            'name' => 'required', 'native' => 'required', 'code' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        try {
            $this->languageRepository->create($request->except("_token"));

            Toastr::success(__('setting.Language Added Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function edit(Request $request)
    {
        try {
            $language = $this->languageRepository->find($request->id);
            return view('localization::languages.edit_modal', [
                "language" => $language
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $language = $this->languageRepository->find($id);
            session()->put('locale', $language->code);
            $files = scandir(base_path('resources/lang/default/'));
            $module_files = getVar('module_lang');
            foreach ($module_files as $module => $module_file) {
                if (!moduleStatusCheck($module)){
                    $files = array_diff($files, $module_file);
                }
            }

            return view('localization::languages.translate_view', [
                "language" => $language,
                "files" => $files
            ]);
        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    public function update(Request $request, $id)
    {
        $validate_rules = [
            'name' => 'required', 'native' => 'required', 'code' => 'required'
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try {
            $language = $this->languageRepository->update($request->except("_token"), $id);

            Toastr::success(__('setting.Language Updated Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {
            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function key_value_store(Request $request)
    {
        $validate_rules = [
            'id' => 'required',
            'translatable_file_name' => 'required',
            'key' => 'required',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        try{
            $language = Language::findOrFail($request->id);

            if (!file_exists(base_path('resources/lang/'.$language->code))) {
                mkdir(base_path('resources/lang/'.$language->code));
            }
            
            if (file_exists(base_path('resources/lang/'.$language->code.'/'.$request->translatable_file_name))) {
                file_put_contents(base_path('resources/lang/'.$language->code.'/'.$request->translatable_file_name), '');
            }

            file_put_contents(base_path('resources/lang/'.$language->code.'/'.$request->translatable_file_name), '<?php return ' . var_export($request->key, true) . ';');

            Artisan::call('cache:clear');
            Toastr::success(__('common.Successfully Updated'));
            return back();

        }catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }
    }

    public function changeLanguage(Request $request)
    {
        try {

            $language = $this->languageRepository->findByCode($request->code);

            if(auth()->user()->role->type == 'system_user'){

                $lt = Config::where('key', 'ttl_rtl')->first();
                $lt->value = $language->rtl;
                $lt->save();

                $lan = Config::where('key', 'language_name')->first();
                $lan->value = $request->code;
                $lan->save();


                $user = Auth::user();
                $user->language = $request->code;
                $user->ttl_rtl = $language->rtl;
                $user->save();

            }else{
                $user = Auth::user();
                $user->language = $request->code;
                $user->ttl_rtl = $language->rtl;
                $user->save();
            }



            session()->put('locale', $request->code);
            App::setLocale($request->code);
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');
            return response()->json([
                'success' => trans('common.Successfully Updated')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => trans('common.Something Went Wrong')
            ]);
        }
    }

    public function get_translate_file(Request $request)
    {

        try{
            $language = $this->languageRepository->find($request->id);

            $file_name = explode('.', $request->file_name);
            $languages = Lang::get($file_name[0]);
            $translatable_file_name = $request->file_name;

            $file1 = base_path('resources/lang/default/'.$request->file_name);
            if (!file_exists(base_path('resources/lang/'.$language->code))) {
                mkdir(base_path('resources/lang/'.$language->code));
            }

            if (!file_exists(base_path('resources/lang/'.$language->code.'/'.$request->file_name))) {
                copy($file1,base_path('resources/lang/'.$language->code.'/'.$request->file_name));
                $url = base_path('resources/lang/'.$language->code.'/'.$request->file_name);
                $languages = include  "{$url}";
                return view('localization::modals.translate_modal', [
                    "languages" => $languages,
                    "language" => $language,
                    "translatable_file_name" => $translatable_file_name
                ]);

            }



            $file2 = base_path('resources/lang/'.$language->code.'/'.$request->file_name);
            // Count the number of lines on file
            $default_file_arr = include  "{$file1}";
            $lang_file_arr = include  "{$file2}";

            if (count($default_file_arr) > count($lang_file_arr)) {

                $languages = array_merge($default_file_arr, $lang_file_arr);

                return view('localization::modals.translate_modal', [
                    "languages" => $languages,
                    "language" => $language,
                    "translatable_file_name" => $translatable_file_name
                ]);
            } else{

                $url = base_path('resources/lang/'.$language->code.'/'.$request->file_name);
                $languages = include  "{$url}";
                return view('localization::modals.translate_modal', [
                    "languages" => $languages,
                    "language" => $language,
                    "translatable_file_name" => $translatable_file_name
                ]);
            }






        }catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back();
        }

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $language = $this->languageRepository->delete($id);

            Toastr::success(__('setting.Language has been deleted Successfully'));
            Artisan::call('cache:clear');
            return back();
        } catch (\Exception $e) {

            Toastr::error(__('common.Something Went Wrong'));
            return back()->with('message-danger', __('common.Something Went Wrong'));
        }
    }
}
