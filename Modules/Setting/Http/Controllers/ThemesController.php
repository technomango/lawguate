<?php

namespace Modules\Setting\Http\Controllers;

use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Setting\Entities\Color;
use Modules\Setting\Entities\ColorTheme;
use Modules\Setting\Entities\Theme;

class ThemesController extends Controller
{
     public function __construct(){
         $this->middleware('limit:client')->only(['create', 'store', 'copy']);
     }
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try{
            $themes = Theme::with('colors')->get();
            return view('setting::themes.index', compact('themes'));
        }catch (\Exception $e) {
            Toastr::error('Operation Failed', 'Failed');
            return redirect()->back();
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $colors = Color::all();

        return view('setting::themes.create', compact('colors'));

    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validate_rules = [
            'title' => 'required|max:191',
            'color_mode' => 'required|max:191',
            'is_default' => 'sometimes|required|boolean',
            'background_color' => 'required_if:background_type,color|string|max: 20',
            'background_image' => 'required_if:background_type,image|mimes:jpg,jpeg,png|dimensions:1920,1400',
            'color.*' => 'required|string|max:20',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        $fileName = "";
        if ($request->file('background_image') != "") {

            $file = $request->file('background_image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/backgroundImage/', $fileName);
            $fileName = 'public/uploads/backgroundImage/' . $fileName;

        }

        $color_format = [];

        foreach ($request->color as $key => $color){
            $color_format[$key] = ['value' => $color];
        }

        if ($request->is_default){
            Theme::where('is_default', 1)->update(['is_default' => 0]);
        }

        $theme = new Theme;
        $theme->title = $request->title;
        $theme->color_mode = $request->color_mode;
        $theme->background_type = $request->background_type;
        $theme->background_image = $fileName;
        $theme->background_color = $request->background_color;
        $theme->is_default = $request->is_default ? 1 : 0;
        $theme->created_by = Auth::id();
        $theme->organization_id = Auth::user()->organization_id;
        $theme->save();

        $theme->colors()->sync($color_format);

        if ($theme->is_default) {
            session()->put('color_theme', $theme);
        }
        $response = [
            'goto' => route('themes.create'),
            'message' => __('setting.New Theme Created Successful'),
        ];

        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('setting::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit(Theme $theme)
    {
        abort_if($theme->is_default, 404);

        $theme->load('colors');
        return view('setting::themes.edit', compact('theme'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, Theme $theme)
    {
        abort_if($theme->is_default, 404);
        $validate_rules = [
            'title' => 'required|max:191',
            'color_mode' => 'required|max:191',
            'is_default' => 'sometimes|required|boolean',
            'background_color' => 'required_if:background_type,color|string|max: 20',
            'color.*' => 'required|string|max:20',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));
        $fileName = $theme->background_image;

        if ($request->file('background_image') != "") {
            $file = $request->file('background_image');
            $fileName = md5($file->getClientOriginalName() . time()) . "." . $file->getClientOriginalExtension();
            $file->move('public/uploads/backgroundImage/', $fileName);
            $fileName = 'public/uploads/backgroundImage/' . $fileName;

        }

        $color_format = [];

        foreach ($request->color as $key => $color){
            $color_format[$key] = ['value' => $color];
        }

        $theme->title = $request->title;
        $theme->color_mode = $request->color_mode;
        $theme->background_type = $request->background_type;
        $theme->background_image = $fileName;
        $theme->background_color = $request->background_color;
        $theme->save();

        $theme->colors()->sync($color_format);
        $theme->refresh()->load('colors');

        if ($theme->is_default) {
            session()->put('color_theme', $theme);
        }
        $response = [
            'goto' => route('themes.index'),
            'message' => __('setting.Theme Updated Successful'),
        ];

        return response()->json($response);

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Theme $theme)
    {
        if ($theme->is_default) {
            Toastr::error(__('setting.You can not permitted to delete system theme'), __('common.Operation failed'));
            return redirect()->back();
        }

        if ($theme->is_default) {
            Theme::find(1)->update(['is_default' => 1]);
        }

        $theme->delete();
        $response = [
            'goto' => route('themes.index'),
            'message' => __('setting.Theme Deleted Successful'),
        ];

        return response()->json($response);

    }

    public function copy(Theme $theme){

        $theme->load('colors');
        $color_format = [];
        foreach ($theme->colors as  $color){
            $color_format[$color->id] = ['value' => $color->pivot->value];
        }

        $newTheme = $theme->replicate();
        $newTheme->title = __('setting.Clone of') . ' '. $theme->title;
        $newTheme->created_at = Carbon::now();
        $newTheme->is_default = false;
        $newTheme->created_by = Auth::id();
        $newTheme->save();

        $newTheme->colors()->sync($color_format);

        saasPlanManagement('theme', 'create');
        Toastr::success(__('setting.Theme Cloned Successful'), __('common.success'));
        return redirect()->back();

    }

    public function default(Theme $theme){

        Theme::where('is_default', 1)->update(['is_default' => 0]);
        $theme->is_default = 1;
        $theme->save();

        session()->put('color_theme', $theme);

        Toastr::success(__('setting.Theme Set Default Successful'), __('common.success'));
        return redirect()->back();

    }


}
