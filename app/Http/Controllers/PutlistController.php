<?php

namespace App\Http\Controllers;

use App\Models\Cases;
use App\Models\Client;
use App\Traits\ImageStore;
use App\Models\HearingDate;
use App\Traits\CustomFields;
use Illuminate\Http\Request;
use App\Jobs\CaseDateUpdateMailJob;
use App\Http\Controllers\CaseController;
use Illuminate\Validation\ValidationException;

class PutlistController extends Controller {
    use ImageStore, CustomFields;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request) {
		$date = date("Y-m-d");
		if ($request->date) {
            $date = getFormatedDate($request->date, true);
		}
		$case_id = [];

		$putup_dates = HearingDate::whereDate('date', $date)->where('type', 'putlist')->get()->pluck('cases_id')->toArray();



		$models = Cases::with(['case_category','client_category'])->where('status', 'Open')->whereIn('id', $putup_dates)->get();

		return view('putlist.index', compact('models', 'date'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request) {
		if (!$request->case) {
			abort(404);
		}
		$case = $request->case;
		CaseController::authorizeToCase($case);
		$case_model = Cases::findOrFail($case);
        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('putup_date');
        }

		return view('putlist.create', compact('case', 'case_model', 'fields'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		if (!$request->json()) {
			abort(404);
		}

        $validate_rules = [
            'hearing_date' => 'required|date',
            'description' => 'required|string',
            'file.*' => 'sometimes|nullable|mimes:jpg,bmp,png,doc,docx,pdf,jpeg,txt',
        ];
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('putup_date'));
        }
        $request->validate($validate_rules, validationMessage($validate_rules));
		$case = Cases::find($request->case);
		if (!$case) {
			throw ValidationException::withMessages(['hearing_date' => __('case.Case Not Found')]);
		}
		$hearing_date = HearingDate::where('type', 'putlist')->where('cases_id', $request->case)->latest()->first();
		if($hearing_date)
		{
			if (strtotime($hearing_date->date) > strtotime($request->hearing_date)) {
				throw ValidationException::withMessages(['hearing_date' => __('case.New Date Must be after') . $hearing_date->date]);
			}
		}


		$model = new HearingDate();
		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->hearing_date);
		$model->description = $request->description;
		$model->type = 'putlist';
		$model->save();

		$case->update(['last_action'=>date('Y-m-d H:i:s')]);
		
        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 'putup_date');
        }
        if ($request->file){
            foreach($request->file as $file){
                $this->storeFile($file, $model->cases_id, $model->id);
            }
        }

		if ($case->layout==2) {
			$client = Client::findOrFail($case->client_id);
		} else {
			if($case->client == 'Plaintiff') {
				$client = Client::findOrFail($case->plaintiff);
			} elseif($case->client_id) {
				$client = Client::findOrFail($case->client_id);
			} else {
				$client = Client::findOrFail($case->opposite);
			}
		}

		if ($client->email) {
			dispatch(new CaseDateUpdateMailJob($client, $case, $model));
		}

		$response = [
			'goto' => route('case.show', $model->cases_id),
			'model' => $model,
			'message' => __('case.Date Added Successfully'),
		];

		return response()->json($response);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		abort(404);
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(Request $request, $id) {
		if (!$request->case) {
			abort(404);
		}
		$case = $request->case;
		CaseController::authorizeToCase($case);
		$model = HearingDate::where(['cases_id' => $case, 'id' => $id, 'type' => 'putlist'])->firstOrFail();
		$case_model = Cases::findOrFail($case);
        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('putup_date');
        }

		return view('putlist.edit', compact('case', 'model', 'case_model', 'fields'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		if (!$request->json()) {
			abort(404);
		}

		if (!$request->json()) {
			abort(404);
		}
        $validate_rules = [
            'hearing_date' => 'required|date',
            'description' => 'required|string'
        ];
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('putup_date'));
        }
        $request->validate($validate_rules, validationMessage($validate_rules));

		$case = Cases::find($request->case);
		$model = HearingDate::find($id);

		if (!$case) {
			throw ValidationException::withMessages(['hearing_date' => __('case.Case Not Found')]);
		}

		$hearing_date = HearingDate::where('type', 'putlist')->where('cases_id', $request->case)->latest()->first();
		if (strtotime($hearing_date->date) > strtotime($request->hearing_date)) {
			throw ValidationException::withMessages(['hearing_date' => __('case.New Date Must be after') . $hearing_date->date]);
		}


		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->hearing_date);
		$model->description = $request->description;
		$model->type = 'putlist';
		$model->save();

        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 'putup_date');
        }

		if ($case->layout==2) {
			$client = Client::findOrFail($case->client_id);
		} else {
			if($case->client == 'Plaintiff') {
				$client = Client::findOrFail($case->plaintiff);
			} elseif($case->client_id) {
				$client = Client::findOrFail($case->client_id);
			} else {
				$client = Client::findOrFail($case->opposite);
			}
		}

		if ($client->email) {
			dispatch(new CaseDateUpdateMailJob($client, $case, $model));
		}



		$response = [
			'goto' => route('case.show', $model->cases_id),
			'message' => __('case.Date Updated Successfully'),
		];

		return response()->json($response);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id) {
		if (!$request->json()) {
			abort(404);
		}

		$model = HearingDate::where('type', 'putlist')->find($id);

		if (!$model) {
			throw ValidationException::withMessages(['message' => __('case.Case Not Found')]);
		}

        if (moduleStatusCheck('CustomField')){
            $model->load('customFields');
            $model->customFields()->delete();
        }
		$model->delete();

		return response()->json(['message' => __('case.Date Deleted Successfully'), 'goto' => route('case.show', $model->cases_id)]);
	}
}
