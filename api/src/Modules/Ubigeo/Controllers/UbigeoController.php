<?php

namespace Modules\Ubigeo\Controllers;

use Modules\Shared\Controllers\Controller;
use Modules\Ubigeo\Requests\{DepartmentRequest, DistrictRequest, ProvinceRequest};
use Modules\Ubigeo\Models\{Department, Province, District};

class UbigeoController extends Controller {

    public function departments(DepartmentRequest $request)
    {
        return response()->json(
            Department::list($request->input('name'))
        );
    }

    public function provinces(ProvinceRequest $request)
    {
        return response()->json(
            Province::list(
                $request->input('department_id'), 
                $request->input('name')
            )
        );
    }

    public function districts(DistrictRequest $request)
    {
        return response()->json(
            District::list(
                $request->input('province_id'),
                $request->input('name')
            )
        );
    }
}