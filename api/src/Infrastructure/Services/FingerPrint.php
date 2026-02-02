<?php

namespace Infrastructure\Services;

use Illuminate\Http\Request;

class FingerPrint { 
    
    public static function generate(Request $request)
    {
        $components = [
            $request->ip(),
            $request->header('User-Agent', 'unknown'),
            $request->header('Accept-Language', 'unknown'),
            $request->header('Origin'), 
            $request->header('Sec-Ch-Ua', 'no-ch'),
        ];
        
        return hash('sha256', implode('|', array_filter($components)));
    }
}