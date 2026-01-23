<?php

namespace Modules\Office\Controllers;

use Infrastructure\Exceptions\JsonResponseException;
use Modules\Shared\Controllers\Controller;
use Modules\Office\Models\Office;
use Modules\Office\Requests\{
    ListerOfficesRequest,
    CreateOfficesRequest,
    UpdateOfficesRequest
};

class OfficeController extends Controller {
    
    public function creater(CreateOfficesRequest $request)
    {
        Office::create([
            'name' => $request->input('name'),
            'status' => 1,
            'level' => 1 

        ]);
        return response()->json(['message' => 'Oficina creada correctamente'], 200);
    }

    public function lister(ListerOfficesRequest $request)
    {
        $results = Office::lister(
            $request->attributes->get('level'),
            $request->input('itemsPerPage'), 
            $request->input('page'), 
            $request->input('name'), 
        );
        return response()->json($results, 200);
    }

    public function updater(UpdateOfficesRequest $request)
    {
        $office = Office::find($request->input('id'));
        if(!$office){
            throw new JsonResponseException('ID not found', 404);
        }
        $office->name = $request->input('name');
        $office->status = $request->input('status');
        $office->save();

        return response()->json(['message' => 'Registro actualizado'], 200);    
    }
}