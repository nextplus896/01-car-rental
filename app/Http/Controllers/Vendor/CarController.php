<?php

namespace App\Http\Controllers\Vendor;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Vendor\Cars\Car;
use App\Models\Admin\Cars\CarArea;
use App\Models\Admin\Cars\CarType;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use ParagonIE\Sodium\Compat;

class CarController extends Controller
{
    public function index()
    {
        $page_title = __("My Cars");
        $cars = Car::where('vendor_id',auth()->guard('vendor')->user()->id)->paginate(6);

        return view('vendor-end.sections.my-car.index',compact('page_title','cars'));
    }
    /**
     * Method for show car create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title = __("Car Create");
        $car_area = CarArea::orderBy('name', 'ASC')->get();
        $car_type = CarType::orderBy('name', 'ASC')->get();
        $languages = Language::get();
        return view('vendor-end.sections.my-car.add', compact(
            'page_title',
            'car_area',
            'car_type',
            'languages',
        ));
    }
    /**
     * Method for get all departments based on branch
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function getAreaTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area'  => 'required|integer',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }
        $area = CarArea::with(['types' => function ($type) {
            $type->with(['type'=> function($car_type){
                $car_type->where('status', true);
            }]);
        }])->find($request->area);
        if (!$area) return Response::error([__('Area Not Found')], 404);

        return Response::success([__('Data fetch successfully')], ['area' => $area], 200);
    }
    /**
     * Method for store car
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'area'        => 'required',
            'type'        => 'required',
            'car_model'   => 'required|string',
            'car_number'  => 'required|string|max:100',
            'seat'        => 'required|numeric',
            'experience'  => 'required|numeric',
            'fees'        => 'required|numeric',
            'image'       => 'required|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        $basic_field_name = [
            'car_title'       => "required",
        ];

        $car_title = $this->contentValidate($request, $basic_field_name);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated                   = $validator->validate();
        $validated['vendor_id']      = auth()->guard('vendor')->user()->id;
        $validated['slug']           = Str::uuid();
        $validated['car_area_id']    = $validated['area'];
        $validated['car_type_id']    = $validated['type'];
        $validated['car_title']      = $car_title;

        if (Car::where('car_number', $validated['car_number'])->exists()) {
            throw ValidationException::withMessages([
                'car_number'  => __("Car already exists!"),
            ]);
        }
        if ($request->hasFile("image")) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'site-section');
            $validated['image'] = $upload;
        }
        $validated = Arr::except($validated, ['area', 'type']);
        try {
            $car = Car::create($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
        }
        return redirect()->route('vendor.car.index')->with(['success' => [__("Car Created Successfully!")]]);
    }
    /**
     * Method for update car status
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target'  => 'required|numeric|exists:cars,id',
            'status'       => 'required|boolean',
        ]);

        if ($validator->fails()) {
            $errors = ['error' => $validator->errors()];
            return Response::error($errors);
        }

        $validated = $validator->validate();
        $cars = Car::find($validated['data_target']);
        try {
            $cars->update([
                'status'   => ($validated['status']) ? false : true,
            ]);
        } catch (Exception $e) {
            $errors = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($errors, null, 500);
        }
        $success = ['success' => [__('Car status updated successfully!')]];
        return Response::success($success);
    }
    /**
     * Method for show car edit page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function edit($id)
    {
        $page_title = __("Car Edit");
        $cars  = Car::find($id);
        if (!$cars) return back()->with(['error' => [__("Car Does not exists")]]);

        $car_area = CarArea::where('status', true)->orderBy('name', 'ASC')->get();
        $car_type = CarType::where('status', true)->get();
        $languages = Language::get();

        return view('vendor-end.sections.my-car.edit', compact(
            'page_title',
            'cars',
            'car_area',
            'car_type',
            'languages',
        ));
    }
    /**
     * Method for update car
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function update(Request $request, $id)
    {
        $car = Car::find($id);
        $validator = Validator::make($request->all(), [
            'area'        => 'required',
            'type'        => 'required',
            'car_model'   => 'required|string',
            'car_number'  => 'required|string|max:100',
            'seat'        => 'required|numeric',
            'experience'  => 'required|numeric',
            'fees'        => 'required|numeric',
            'image'       => 'nullable|image|mimes:png,jpg,jpeg,svg,webp',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $basic_field_name = [
            'car_title'       => "required",
        ];

        $car_title = $this->contentValidate($request, $basic_field_name);

        $validated                 = $validator->validate();
        $validated['vendor_id']    = auth()->guard('vendor')->user()->id;
        $validated['slug']         = Str::uuid();
        $validated['car_area_id']  = $validated['area'];
        $validated['car_type_id']  = $validated['type'];
        $validated['car_title']    = $car_title;

        if ($request->hasFile('image')) {
            $image = get_files_from_fileholder($request, 'image');
            $upload = upload_files_from_path_dynamic($image, 'site-section', $car->image);
            $validated['image'] = $upload;
        }
        $validated = Arr::except($validated, ['area', 'type']);
        try {
            $car->update($validated);
        } catch (Exception $e) {
            return back()->with(['error'  => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('vendor.car.index')->with(['success' => [__('Car Updated Successfully!')]]);
    }
    /**
     * Method for delete car
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function delete(request $request)
    {
        $request->validate([
            'target'    => 'required|numeric',
        ]);
        $cars = Car::find($request->target);
        try {
            delete_file(get_files_path('site-section').'/'. $cars->image);
            $cars->delete();
        } catch (Exception $e) {
            return back()->with(['error'  =>  [__("Something went wrong! Please try again.")]]);
        }
        return back()->with(['success'  => [__("Car Deleted Successfully!")]]);
    }
    /**
     * Method for image validate
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function imageValidate($request, $input_name, $old_image = null)
    {
        if ($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name), [
                $input_name => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();
            $image = get_files_from_fileholder($request, $input_name);
            $upload = upload_files_from_path_dynamic($image, 'site-section', $old_image);
            return $upload;
        }
        return false;
    }


    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = Language::get();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }
}
