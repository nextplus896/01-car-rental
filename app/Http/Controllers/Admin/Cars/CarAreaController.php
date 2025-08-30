<?php

namespace App\Http\Controllers\Admin\Cars;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use App\Http\Helpers\Response;
use App\Models\Admin\Cars\CarArea;
use App\Models\Admin\Cars\CarType;
use App\Models\Admin\Cars\AreaHasType;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CarAreaController extends Controller
{
    public function index(){
        $page_title = __("Car Area");
        $car_area = CarArea::orderByDesc("id")->get();

        return view('admin.sections.cars.car-area.index',compact(
            'page_title',
            'car_area'
        ));
    }

    public function create(){
        $page_title = __("Car Area Create");
        $car_types  = CarType::where('status',true)->get();

        return view('admin.sections.cars.car-area.create',compact(
            'page_title',
            'car_types'
        ));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(),[
            'name'        => 'required|string|max:80|unique:car_areas,name',
            'types'       => 'required|array',
            'types.*'     => 'required|integer',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator);
        }

        $validated = $validator->validate();

        $slug  = Str::slug($request->name);

        $validated['slug']              = $slug;
        $validated['status']            = 1;
        $validated['last_edit_by']      = auth()->user()->id;

        try{
            $area = CarArea::create($validated);
            if(count($validated['types']) > 0) {
                $types = [];
                foreach($validated['types'] as $type_id) {
                    $types[] = [
                        'car_area_id'  => $area->id,
                        'car_type_id'  => $type_id,
                        'created_at'   => now(),
                    ];
                }
                AreaHasType::insert($types);
            }
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }

        return redirect()->route('admin.car.area.index')->with(['success' => [__('Car Area created successfully!')]]);

    }

    public function edit($id){
        $area = CarArea::find($id);
        $car_types   = CarType::where('status',true)->get();
        if(!$area) return back()->with(['error'=> [__('Area Not Found')]]);
        $page_title  = __("Car Area Edit");

        return view('admin.sections.cars.car-area.edit',compact(
            'area',
            'car_types',
            'page_title'
        ));

    }
    public function update(Request $request,$id)
    {
        $area     = CarArea::find($id);
        $validator = Validator::make($request->all(),[
            'name'        => 'required|string|max:80',
            'types'       => 'required|array',
            'types.*'     => 'required|integer',
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated     = $validator->validate();
        $request_types = $validated['types'];

        $validated     = Arr::except($validated,['types']);


        $slug                        = Str::slug($validated['name']);
        $validated['slug']           = $slug;
        $validated['last_edit_by']   = auth()->user()->id;

        try{
            $area_type_ids = $area->types->pluck('id');
            AreaHasType::whereIn('id',$area_type_ids)->delete();

            $area->update($validated);
            if(count($request_types) > 0) {

                $types = [];
                foreach($request_types as $type_id) {
                    $types[] = [
                        'car_area_id'      => $area->id,
                        'car_type_id'  => $type_id,
                        'created_at'   => now(),
                    ];
                }
                AreaHasType::insert($types);
            }


        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('admin.car.area.index')->with(['success' => [__('Car Area updated successfully!')]]);

    }
    public function delete(Request $request){
       $request->validate([
        'target'    => 'required|numeric|',
       ]);
       $car_area = CarArea::find($request->target);

       try {
            $car_area->delete();
       } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
       }
       return back()->with(['success' => [__('Car Area Deleted Successfully!')]]);

    }

    public function statusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'data_target'       => 'required|numeric|exists:car_areas,id',
            'status'            => 'required|boolean',
        ]);

        if($validator->fails()) {
            $errors = ['error' => $validator->errors() ];
            return Response::error($errors);
        }
        $validated = $validator->validate();

        $car_types = CarArea::find($validated['data_target']);

        try{
            $car_types->update([
                'status'        => ($validated['status']) ? false : true,
            ]);
        }catch(Exception $e) {
            $errors = ['error' => [__('Something went wrong! Please try again.')] ];
            return Response::error($errors,null,500);
        }

        $success = ['success' => [__('Car Area status updated successfully!')]];
        return Response::success($success);
    }
}
