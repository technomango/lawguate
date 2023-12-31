<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Cases;
use App\Models\Client;
use App\Traits\ImageStore;
use App\Models\HearingDate;
use App\Traits\CustomFields;
use Illuminate\Http\Request;
use App\Jobs\CaseDateUpdateMailJob;
use Illuminate\Validation\ValidationException;

class JudgementController extends Controller {
    use ImageStore, CustomFields;
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {		
			$staffs = User::whereNotIn('role_id', [1, 0])
			->where('id', '!=', auth()->user()->id)->get()
			->pluck('name', 'id');
			return view('case.judgement', compact('staffs'));
	}

	public function closed() {
			$staffs = User::whereNotIn('role_id', [1, 0])
			->where('id', '!=', auth()->user()->id)->get()
			->pluck('name', 'id');
			return view('case.close', compact('staffs'));
	}

	public function reopen(Request $request) {
		if (!$request->case) {
			abort(404);
		}
		$case = $request->case;
		$model = Cases::findOrFail($case);
        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('re_open_case');
        }
		return view('judgement.reopen', compact('case', 'model', 'fields'));
	}


	public function reopen_store(Request $request) {
		if (!$request->json()) {
			abort(404);
		}

		$validate_rules = [
            'judgement_date' => 'required|date',
            'judgement' => 'required|string',
            'file.*' => 'sometimes|nullable|mimes:jpg,bmp,png,doc,docx,pdf,jpeg,txt',
        ];
		$request->validate($validate_rules, validationMessage($validate_rules));
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('re_open_case'));
        }

		$case = Cases::find($request->case);
		if (!$case) {
			throw ValidationException::withMessages(['judgement_date' => __('case.Case Not Found')]);
		}

		$case->judgement_status = 'Reopen';
		$case->save();

		$model = new HearingDate();
		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->judgement_date);
		$model->description = $request->judgement;
		$model->type = 'reopen';
		$model->save();

		$case->update(['last_action'=>date('Y-m-d H:i:s')]);
		
        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 're_open_case');
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

		if ($client->emial) {
			dispatch(new CaseDateUpdateMailJob($client, $case, $model));
		}


		$response = [
			'goto' => route('case.show', $case->id),
			'model' => $case,
			'message' => __('case.Case Re-open Successfully'),
		];

		return response()->json($response);
	}

	public function close(Request $request) {
		if (!$request->case) {
			abort(404);
		}
		$case = $request->case;
		$model = Cases::findOrFail($case);
        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('case_close');
        }
		return view('judgement.close', compact('case', 'model', 'fields'));
	}

	public function close_store(Request $request) {
		if (!$request->json()) {
			abort(404);
		}
        $validate_rules = [
            'judgement_date' => 'required|date',
            'judgement' => 'required|string',
            'file.*' => 'sometimes|nullable|mimes:jpg,bmp,png,doc,docx,pdf,jpeg,txt',
        ];
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('case_close'));
        }

        $request->validate($validate_rules, validationMessage($validate_rules));
		$case = Cases::find($request->case);
		if (!$case) {
			throw ValidationException::withMessages(['judgement_date' => __('case.Case Not Found')]);
		}

		$case->judgement_status = 'Close';
		$case->save();

		$model = new HearingDate();
		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->judgement_date);
		$model->description = $request->judgement;
		$model->type = 'close';
		$model->save();

        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 'case_close');
        }

        if ($request->file){
            foreach($request->file as $file){
                $this->storeFile($file, $model->cases_id, $model->id);
            }
        }

		$response = [
			'goto' => route('case.show', $case->id),
			'model' => $case,
			'message' => __('case.Case Close Successfully'),
		];

		return response()->json($response);
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
		$model = Cases::findOrFail($case);
        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('judgement_date');
        }
		return view('judgement.create', compact('case', 'model', 'fields'));
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
            'judgement_date' => 'required|date',
            'judgement' => 'required|string',
            'file.*' => 'sometimes|nullable|mimes:jpg,bmp,png,doc,docx,pdf,jpeg,txt',
        ];
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('judgement_date'));
        }
        $request->validate($validate_rules, validationMessage($validate_rules));
		$case = Cases::find($request->case);
		
		if (!$case) {
			throw ValidationException::withMessages(['judgement_date' => __('case.Case Not Found')]);
		}
		$case->judgement_date = getFormatedDate($request->judgement_date);
		$case->judgement_status = 'Judgement';
		$case->status = 'Judgement';
		$case->save();

		$hearing_date = HearingDate::where('type', 'judgement')->where('cases_id', $request->case)->latest()->first();

		$model = new HearingDate();
		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->judgement_date);
		$model->description = $request->judgement;
		$model->type = 'judgement';
		$model->save();

        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 'judgement_date');
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
			'goto' => route('case.show', $case->id),
			'model' => $case,
			'message' => __('case.Judgement Successfully'),
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

		$model = HearingDate::where(['cases_id' => $case, 'id' => $id])->firstOrFail();

        $fields = null;

        if (moduleStatusCheck('CustomField')){
            $fields = getFieldByType('judgement_date');
        }

		return view('judgement.edit', compact('case', 'model', 'fields'));
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
        $validate_rules = [
            'judgement_date' => 'required|date',
            'judgement' => 'required|string'
        ];
        if (moduleStatusCheck('CustomField')){
            $validate_rules = array_merge($validate_rules, $this->generateValidateRules('judgement_date'));
        }
        $request->validate($validate_rules, validationMessage($validate_rules));

		$case = Cases::find($request->case);
		if (!$case) {
			throw ValidationException::withMessages(['judgement_date' => __('case.Case Not Found')]);
		}

		$model = HearingDate::find($id);

		$case->judgement_date = getFormatedDate($request->judgement_date);
		$case->judgement_date = getFormatedDate($request->judgement_date);
		$case->stage_id = $request->stage_id;
		$case->save();


		$model->cases_id = $request->case;
		$model->date = getFormatedDate($request->judgement_date);
		$model->description = $request->judgement;
		$model->type = 'judgement';
		$model->save();

        if (moduleStatusCheck('CustomField')){
            $this->storeFields($model, $request->custom_field, 'judgement_date');
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
		if($client->email){
			dispatch(new CaseDateUpdateMailJob($client, $case, $model));
		}


		$response = [
			'goto' => route('case.show', $case->id),
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
		$judgement_date = HearingDate::find($id);
		$case_id = $judgement_date->cases_id;

		if($judgement_date){
			$case = Cases::find($judgement_date->cases_id);

			$lastJudgemnt = $case->hearing_dates()->where('type', 'judgement')->latest()->first();

			if($lastJudgemnt->id == $judgement_date->id)
			{
				$case->judgement_date = null;
				$case->judgement_status = 'Open';
				$case->status = 'Open';
				$case->save();
			}
            if (moduleStatusCheck('CustomField')){
                $judgement_date->load('customFields');
                $judgement_date->customFields()->delete();
            }
			$judgement_date->delete();
		}


		return response()->json(['message' => __('case.Date Deleted Successfully'), 'goto' => route('case.show', $case_id)]);
	}
}
