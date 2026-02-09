<?php

namespace Modules\Office\Controllers;

use Modules\Shared\Controllers\Controller;
use Modules\Office\Models\Locale;
use Modules\Office\Requests\CreateLocalesRequest;
use Modules\Office\Requests\UpdateLocalesRequest;
use Illuminate\Http\Request;

class LocalesController extends Controller
{
    public function creater(CreateLocalesRequest $request)
    {
        Locale::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Local creado correctamente'], 201);
    }

    public function updater(UpdateLocalesRequest $request)
    {
        $locale = Locale::find($request->input('id'));
        $locale->name = $request->input('name');
        $locale->save();

        return response()->json(['message' => 'Local actualizado correctamente'], 200);
    }

    public function lister(Request $request)
    {
        $locales = Locale::all();
        return response()->json($locales, 200);
    }
}
