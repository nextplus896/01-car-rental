<?php

namespace App\Http\Controllers\Admin\Cars;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Vendor\Cars\Car;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller
{
    public function index(){
        $page_title = __("Car Approval");
        $cars = Car::orderByDesc("id")->get();

        return view('admin.sections.cars.car.index',compact(
            'page_title',
            'cars',
        ));
    }

    public function statusUpdate(Request $request) {
        $validator = Validator::make($request->all(),[
            'data_target'       => 'required|numeric|exists:cars,id',
            'status'            => 'required|boolean',
        ]);

        if($validator->fails()) {
            $errors = ['error' => $validator->errors() ];
            return Response::error($errors);
        }
        $validated = $validator->validate();

        $cars = Car::find($validated['data_target']);

        try{
            $cars->update([
                'approval'        => ($validated['status']) ? false : true,
            ]);
        }catch(Exception $e) {
            $errors = ['error' => [__('Something went wrong! Please try again.')] ];
            return Response::error($errors,null,500);
        }

        $success = ['success' => [__('Car approval updated successfully!')]];
        return Response::success($success);
    }
}
