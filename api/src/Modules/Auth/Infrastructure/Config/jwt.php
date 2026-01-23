<?php

/**
 *  This file is configuration for services jwt-auth
 */
return [

    /**  Algoritm use for sign tokens issued  */
    'alg' => env('JWT_ALG', 'HS256'),

    'secret' => env('JWT_SECRET'),

    'private_key' => env('JWT_PRIVATE_KEY'),

    'public_key' => env('JWT_PUBLIC_KEY'),

    'issuer' => env('JWT_ISSUER', 'scc.regionloreto.gob.pe'),

    /**
     *  ======================================================================
     *  JWT time to live 
     *  ======================================================================
     *  Specify the length of time (in seconds)  that token will be valid for.
    */

    /** One hour */
    'ttl_access' => env('JWT_TTL_ACCESS', 3600),

    /** Two Weeks */
    'ttl_refresh' => env('JWT_TTL_REFRESH', 1209600)
];