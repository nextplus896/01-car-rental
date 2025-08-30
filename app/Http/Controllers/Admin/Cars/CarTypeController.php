<?php

namespace App\Http\Controllers\Admin\Cars;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Helpers\Response;
use App\Models\Admin\Cars\CarType;
use App\Models\Admin\Cars\AreaHasType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CarTypeController extends Controller
{
    public function index(){
        $page_title = __("Car Types");
        $car_types = CarType::orderByDesc("id")->paginate(11);

        return view('admin.sections.cars.car-type.index',compact(
            'page_title',
            'car_types'
        ));
    }
    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name'      => 'required|string|max:80|unique:car_types,name',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with("modal","add-car-types");
        }

        $validated = $validator->validate();


        $slug  = Str::slug($request->name);

        $validated['slug']              = $slug;
        $validated['status']            = 1;
        $validated['last_edit_by']      = auth()->user()->id;

        try{
            CarType::create($validated);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return back()->with(['success' => [__('Car Type created successfully!')]]);

    }
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'target'        => 'required|numeric|exists:car_types,id',
            'edit_name'     => 'required|string|max:80|',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with("modal","car-types-edit");
        }

        $validated = $validator->validate();

        $slug      = Str::slug($request->edit_name);
        $validated = replace_array_key($validated,"edit_");
        $validated = Arr::except($validated,['target']);
        $validated['slug']   = $slug;
        $validated['last_edit_by']   = auth()->user()->id;
        $car_types = CarType::find($request->target);

        try{
            $car_types->update($validated);
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Car Type updated successfully!')]]);

    }
    public function delete(Request $request){
       $request->validate([
        'target'    => 'required|numeric|',
       ]);
       $car_types = CarType::find($request->target);

       try {
            $car_types->delete();
       } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
       }
       return back()->with(['success' => [__('Car Type Deleted Successfully!')]]);

    }

    public function statusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'data_target'       => 'required|numeric|exists:car_types,id',
            'status'            => 'required|boolean',
        ]);

        if($validator->fails()) {
            $errors = ['error' => $validator->errors() ];
            return Response::error($errors);
        }

        $validated = $validator->validate();


        $car_types = CarType::find($validated['data_target']);

        try{
            $car_types->update([
                'status'        => ($validated['status']) ? false : true,
            ]);
        }catch(Exception $e) {
            $errors = ['error' => [__('Something went wrong! Please try again.')] ];
            return Response::error($errors,null,500);
        }

        $success = ['success' => [__('Car Type status updated successfully!')]];
        return Response::success($success);
    }
}
