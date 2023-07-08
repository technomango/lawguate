<?php
return [
    'employee_id.required_if' => 'The employee id field is required when role type is system user.',
    'name.required' => 'The name field is required.',
    'comments.required' => 'The comments field is required.',
    'phone.string' => 'The phone must be a string.',
    'email.required' => 'The email field is required.',
    'email.unique' => 'The email has already been taken.',
    'password.required' => 'The password field is required.',
    'domain.required' => 'The domain field is required.',
    'domain.alpha_dash' => 'The domain may only contain letters, numbers, dashes and underscores.',
    'domain.unique' => 'The domain has already been taken.',
    'domain.max' => 'The domain may not be greater than 20 characters.',
    'password.string' => 'The password must be a string.',
    'password.min' => 'The password must be at least 8 characters.',
    'role_id.required' => 'The role id field is required.',
    'date_of_birth.required_if' => 'The date of birth field is required when role type is system user.',
    'current_address.required_if' => 'The current address field is required when role type is system user.',
    'permanent_address.required_if' => 'The permanent address field is required when role type is system user.',
    'photo.mimes' => 'The photo must be a file of type: jpeg,jpg,png.',
    'signature_photo.mimes' => 'The signature photo must be a file of type: jpeg,jpg,png.',
    'attendance.required' => 'The attendance field is required.',
    'year.required' => 'The year field is required.',
    'month.required' => 'The month field is required.',
    'leave_type_id.required' => 'The leave type id field is required.',
    'reason.required' => 'The reason field is required.',
    'apply_date.required' => 'The apply date field is required.',
    'start_date.required' => 'The start date field is required.',
    'end_date.required' => 'The end date field is required.',
    'end_date.date' => 'The end date is not a valid date.',
    'end_date.after_or_equal' => 'The end date must be a date after or equal to start date.',
    'name.unique' => 'The name has already been taken.',
    'type.required' => 'The type field is required.',
    'favicon_logo.image' => 'The favicon logo must be an image.',
    'favicon_logo.mimes' => 'The favicon logo must be a file of type: jpg,png,jpeg.',
    'site_logo.image' => 'The site logo must be an image.',
    'site_logo.mimes' => 'The site logo must be a file of type: jpg,png,jpeg.',
    'name.max' => 'The name may not be greater than 191 characters.',
    'name.string' => 'The name must be a string.',
    'description.max' => 'The description may not be greater than 1500 characters.',
    'description.string' => 'The description must be a string.',
    'country_id.integer' => 'The country id must be an integer.',
    'state_id.integer' => 'The state id must be an integer.',
    'city_id.integer' => 'The city id must be an integer.',
    'court_category_id.integer' => 'The court category id must be an integer.',
    'location.max' => 'The location may not be greater than 191 characters.',
    'location.string' => 'The location must be a string.',
    'room_number.max' => 'The room number may not be greater than 15 characters.',
    'room_number.string' => 'The room number must be a string.',
    'title.required' => 'The title field is required.',
    'title.max' => 'The title may not be greater than 191 characters.',
    'title.string' => 'The title must be a string.',
    'contact_id.required' => 'The contact id field is required.',
    'contact_id.integer' => 'The contact id must be an integer.',
    'motive.required' => 'The motive field is required.',
    'date.required' => 'The date field is required.',
    'datenotes.max' => 'The datenotes may not be greater than 1500 characters.',
    'notes.max' => 'The notes may not be greater than 1500 characters.',
    'datenotes.string' => 'The datenotes must be a string.',
    'case_category_id.required' => 'The case category id field is required.',
    'case_category_id.integer' => 'The case category id must be an integer.',
    'case_no.string' => 'The case no must be a string.',
    'file_no.string' => 'The file no must be a string.',
    'file_no.max' => 'The file no may not be greater than 20 characters.',
    'acts*.required' => 'The acts* field is required.',
    'acts*.integer' => 'The acts* must be an integer.',
    'plaintiff.required' => 'The plaintiff field is required.',
    'plaintiff.integer' => 'The plaintiff must be an integer.',
    'opposite.required' => 'The opposite field is required.',
    'opposite.integer' => 'The opposite must be an integer.',
    'client_category_id.required' => 'The client category id field is required.',
    'client_category_id.integer' => 'The client category id must be an integer.',
    'court_category_id.required' => 'The court category id field is required.',
    'court_id.required' => 'The court id field is required.',
    'court_id.integer' => 'The court id must be an integer.',
    'lawyer_id.integer' => 'The lawyer id must be an integer.',
    'stage_id.integer' => 'The stage id must be an integer.',
    'receiving_date.date' => 'The receiving date is not a valid date.',
    'filling_date.date' => 'The filling date is not a valid date.',
    'hearing_date.date' => 'The hearing date is not a valid date.',
    'judgement_date.date' => 'The judgement date is not a valid date.',
    'file.*.mimes' => 'The file.* must be a file of type: jpg,bmp,png,doc,docx,pdf,jpeg,txt.',
    'country_id.required' => 'The country id field is required.',
    'country_id.exists' => 'The selected country id is invalid.',
    'state_id.required' => 'The state id field is required.',
    'state_id.exists' => 'The selected state id is invalid.',
    'email.email' => 'The email must be a valid email address.',
    'email.max' => 'The email may not be greater than 191 characters.',
    'mobile.string' => 'The mobile must be a string.',
    'mobile.max' => 'The mobile may not be greater than 191 characters.',
    'gender.string' => 'The gender must be a string.',
    'gender.max' => 'The gender may not be greater than 191 characters.',
    'address.string' => 'The address must be a string.',
    'address.max' => 'The address may not be greater than 191 characters.',
    'mobile_no.string' => 'The mobile no must be a string.',
    'contact_category_id.integer' => 'The contact category id must be an integer.',
    'hearing_date.required' => 'The hearing date field is required.',
    'stage_id.required' => 'The stage id field is required.',
    'judgement_date.required' => 'The judgement date field is required.',
    'judgement.required' => 'The judgement field is required.',
    'judgement.string' => 'The judgement must be a string.',
    'from_date.required' => 'The from date field is required.',
    'holiday_name.required' => 'The holiday name field is required.',
    'date.required_if' => 'The date field is required when type is 0.',
    'start_date.required_if' => 'The start date field is required when type is 1.',
    'end_date.required_if' => 'The end date field is required when day is 2.',
    'reason.max' => 'The reason may not be greater than 255 characters.',
    'attachment.mimes' => 'The attachment must be a file of type: jpeg,jep,png,docx,txt,pdf.',
    'day.required' => 'The day field is required.',
    'from_day.required_if' => 'The from day field is required when day is 0.',
    'total_days.required' => 'The total days field is required.',
    'max_forward.required_if' => 'The max forward field is required when balance forward is checked.',
    'native.required,==,1' => 'validation.required,==,1',
    'native.required' => 'The native field is required.',
    'code.required' => 'The code field is required.',
    'id.required' => 'The id field is required.',
    'translatable_file_name.required' => 'The translatable file name field is required.',
    'key.required' => 'The key field is required.',
    'code.max' => 'The code may not be greater than 15 characters.',
    'native.max' => 'The native may not be greater than 50 characters.',
    'lang_id.required' => 'The lang id field is required.',
    'lang_id.max' => 'The lang id may not be greater than 255 characters.',
    'module_id.required' => 'The module id field is required.',
    'language_universal.required' => 'The language universal field is required.',
    'net_salary.required' => 'The net salary field is required.',
    'module_id.array' => 'The module id must be an array.',
    'symbol.required' => 'The symbol field is required.',
    'site_title.required' => 'The site title field is required.',
    'site_title.string' => 'The site title must be a string.',
    'site_title.max' => 'The site title may not be greater than 30 characters.',
    'file_supported.string' => 'The file supported must be a string.',
    'copyright_text.string' => 'The copyright text must be a string.',
    'date_format_id.required' => 'The date format id field is required.',
    'currency_id.required' => 'The currency id field is required.',
    'time_zone_id.required' => 'The time zone id field is required.',
    'preloader.required' => 'The preloader field is required.',
    'phone.required' => 'The phone field is required.',
    'address.required' => 'The address field is required.',
    'assignee_id.required' => 'The assignee id field is required.',
    'assignee_id.integer' => 'The assignee id must be an integer.',
    'case_id.required' => 'The case id field is required.',
    'case_id.integer' => 'The case id must be an integer.',
    'due_date.required' => 'The due date field is required.',
    'login_backgroud_image.required' => 'The Login background image field is required.',
    'required' => 'This Value field is required',
    'min' => 'The value field must be at least min characters.',
    'max' => 'The value field may not be greater than max characters.',
    'date' => 'The value field is not a valid date',
    'email' => 'The value field is not a valid email',
    'file' => 'The value field is not a valid file',
    'case_charge.numeric' => 'The case charge must be a number.',
    'password.confirmed' => 'The password confirmation is not matched.',
    'phonecode.unique' => 'The Phone Code has already been taken.',
    'hearing_date_description.required_with' => 'The Hearing date description field is required with hearing date',
    'judgement.required_with' => 'The judgement field is required with hearing date',
    'layout.required' => 'The layout field is required',
    'title.unique' => 'The title is already taken',

    'payment_method.exists' => 'The selected payment method is invalid.',
    'payment_method.required' => 'The payment method field is required.',
    'integer' => "The field must be a integer",
    'mimes' => "The file type is not supported",


    'color_mode.required' => 'The color mode field is required.',
    'color_mode.max' => 'The color mode may not be greater than 191 characters.',
    'is_default.required' => 'The is default field is required.',
    'is_default.boolean' => 'The is default field must be true or false.',
    'background_color.string' => 'The background color must be a string.',
    'background_color.max' => 'The background color may not be greater than  20 characters.',
    'background_color.required_if' => 'The background color field is required when background type is color.',
    'background_image.required_if' => 'The background image field is required when background type is image.',
    'background_image.mimes' => 'The background image must be a file of type: jpg,jpeg,png.',
    'background_image.dimensions' => 'The background image has invalid image dimensions.',
    'color.*.required' => 'The color.* field is required.',
    'color.*.string' => 'The color.* must be a string.',
    'color.*.max' => 'The color.* may not be greater than 20 characters.',
    'updateFile.required' => 'The updateFile field is required.',
    'updateFile.mimes' => 'The updateFile must be a file of type: zip.',
    'details.string' => 'The details must be a string.',

];
