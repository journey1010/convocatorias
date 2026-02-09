<?php

namespace Modules\Office\Controllers;

use Modules\Office\Models\Locale;
use Modules\Shared\Controllers\Controller;
use Modules\Office\Requests\CreateLocalesRequest;
use Modules\Office\Requests\UpdateLocalesRequest;

class LocalesController extends Controller
{
    public function creater(CreateLocalesRequest $request)
    {
        $locale = Locale::create([
            'name' => $request->input('name'),
        ]);

        return response()->json($locale, 201);
    }

    public function updater(UpdateLocalesRequest $request)
    {
        $locale = Locale::find($request->input('id'));
        $locale->name = $request->input('name');
        $locale->save();

        return response()->json($locale, 200);
    }

    public function lister()
    {
        $locales = Locale::all();
        return response()->json(['items' => $locales], 200);
    }
}
