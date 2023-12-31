<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Stage;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StageController extends Controller {
    public function __construct()
    {
        $this->middleware('limit:case_stage')->only(['create', 'store']);
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index() {
		$models = Stage::all();
		return view('master.stage.index', compact('models'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create() {
        $quick_add = request()->quick_add;
        if (request()->ajax() and $quick_add == 1){
            return view('master.stage.create_modal');
        }
		return view('master.stage.create');
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
            'name' => 'required|max:191|string',
            'description' => 'sometimes|nullable|max:1500|string',
        ];

        $request->validate($validate_rules, validationMessage($validate_rules));

		$model = new Stage();
		$model->name = $request->name;
		$model->description = $request->description;
		$model->save();

		$response = [
			'model' => $model,
			'message' => __('case.Case Stage Added Successfully'),
		];

        if ($request->quick_add == 1){
            $response['appendTo'] = '#stage_id';
        }
        return response()->json($response);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function show($id) {
		$model = Stage::findOrFail($id);
		return view('master.stage.show', compact('model'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id
	 * @return Response
	 */
	public function edit($id) {
		$model = Stage::findOrFail($id);
		return view('master.stage.edit', compact('model'));
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
            'name' => 'required|max:191|string',
            'description' => 'sometimes|nullable|max:1500|string',
        ];

        $request->validate($validate_rules, validationMessage($validate_rules));

		$model = Stage::find($id);
		if (!$model) {
			throw ValidationException::withMessages(['message' => __('case.Case Stage Not Found')]);
		}

		$model->name = $request->name;
		$model->description = $request->description;
		$model->save();

		$response = [
			'message' => __('case.Case Stage Updated Successfully'),
			'goto' => route('master.stage.index'),
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

		$model = Stage::with('dates')->find($id);
		if (!$model) {
			throw ValidationException::withMessages(['message' => __('case.Case Stage Not Found')]);
		}
        if ($model->dates()->count()) {
            throw ValidationException::withMessages(['message' => __('case.Case Stage assign with hearing dates.')]);
        }
		//Check Client

		$model->delete();

		return response()->json(['message' => __('case.Case Stage Deleted Successfully'), 'goto' => route('master.stage.index')]);
	}
}
