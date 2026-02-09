<?php

namespace Modules\User\Applications;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use Modules\User\Models\OfficeUser;
use Illuminate\Support\Facades\DB;
use Modules\Auth\Infrastructure\Context\RequestContextResolver;

class StoreUserCase
{

    public function exec(Request $request)
    {
        $ctx = RequestContextResolver::fromRequest($request);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' =>  $request->input('name'),
                'last_name' => $request->input('last_name'),
                'dni' => $request->input('dni'),
                'nickname' => $request->input('nickname'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'password' => $request->input('password'),
                'status' => 1,
                'token_version' => 1,
                'number_login_device' => 1,
                'level' => 1,
                'created_by' => $ctx->userId
            ]);

            if ($request->input('roles')) {
                $user->syncRoles($request->input('roles'));
            }

            if ($request->input('permissions')) {
                $user->syncPermissions($request->input('permissions'));
            }

            OfficeUser::updateData($user->id, $request->input('office_id'));

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
