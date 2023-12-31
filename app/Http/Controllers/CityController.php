<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CityController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $country_id = request()->country_id;
        if (!$country_id){
            $country_id = config('configs.country_id');
        }
        $states = State::where('country_id', $country_id)->pluck('name', 'id');
        $state_id = request()->state_id;
        if (!$state_id){
            $state_id = array_key_first($states->toArray());
        }


        $models = City::where('state_id', $state_id)->get();
        $countries = Country::all()->pluck('name', 'id')->prepend(__('setting.Select country'), '');

        return view('city.index', compact('models', 'countries', 'states', 'country_id', 'state_id'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $country_id = request()->country_id;
        if (!$country_id){
            $country_id = config('configs.country_id');
        }
        $states = State::where('country_id', $country_id)->pluck('name', 'id');
        $state_id = request()->state_id;
        if (!$state_id){
            $state_id = array_key_first($states->toArray());
        }

        $countries = Country::all()->pluck('name', 'id')->prepend(__('setting.Select country'), '');
        return view('city.create', compact('countries','states', 'country_id', 'state_id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    public function store(Request $request) {
        if (!$request->json()) {
            abort(404);
        }
        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));

        $model = new City();
        $model->name = $request->name;
        $model->state_id = $request->state_id;
        $model->save();

        $response = [
            'model' => $model,
            'message' => __('setting.City Added Successfully'),
        ];

        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show($id) {
        abort(404);
        $model = City::findOrFail($id);
        return view('city.show', compact('model'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function edit($id) {
        $model = City::findOrFail($id);
        $countries = Country::all()->pluck('name', 'id')->prepend(__('Select country'), '');
        $states = State::where('country_id', $model->state->country_id)->pluck('name', 'id')->prepend(__('Select country'), '');
        return view('city.edit', compact('model', 'countries', 'states'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws ValidationException
     */
    public function update(Request $request, $id) {
        if (!$request->json()) {
            abort(404);
        }

        $validate_rules = [
            'country_id' => ['required', Rule::exists('countries', 'id')],
            'state_id' => ['required', Rule::exists('states', 'id')],
            'name' => 'required|max:191|string',
        ];
        $request->validate($validate_rules, validationMessage($validate_rules));


        $model = City::find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('City Not Found')]);
        }

        $model->name = $request->name;
        $model->state_id = $request->state_id;
        $model->save();

        $response = [
            'message' => __('setting.City Updated Successfully'),
            'goto' => route('setup.city.index', ['country_id' => $request->country_id, 'state_id' => $request->state_id]),
        ];

        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return void
     * @throws ValidationException
     */
    public function destroy(Request $request, $id) {
        if (!$request->json()) {
            abort(404);
        }

        $model = City::with('courts', 'clients')->find($id);
        if (!$model) {
            throw ValidationException::withMessages(['message' => __('setting.City Not Found')]);
        }


        if ($model->courts->count()) {
            throw ValidationException::withMessages(['message' => __('setting.City is assigned with courts')]);
        }

        if ($model->clients->count()) {
            throw ValidationException::withMessages(['message' => __('setting.City is assigned with clients')]);
        }

        $model->delete();

        return response()->json(['message' => __('setting.City Deleted Successfully'), 'goto' => route('setup.city.index')]);
    }
}
