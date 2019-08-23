<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Tenant;
use App\Models\TenantGroupTenant;

use App\Base\BaseController;

class TenantController extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * initiate tenant app
     * 
     * @param Request $request *semua optional
     *      group_app : apps id / path nya
     * @return array
     *      tenant_list
     *      active_tenant
     *      active_tenant_group
     */
    public function readList(Request $request)
    {

        $tenant = ['tenant_list'=>'','active_tenant'=>false,'active_tenant_group'=>false];
        if($request->input('group_app')){
            $tenant['active_tenant'] = Tenant::where('group_app',$request->input('group_app'))->first();
            if($tenant['active_tenant']){
                $tenant['active_tenant_group'] = TenantGroupTenant::where('tenant_id',$tenant['active_tenant']->id)->get()->pluck('tenant_group_id');
                if($tenant['active_tenant_group']->count()<=0) $tenant['active_tenant_group'] = false;
            }else{
                $tenant['active_tenant'] = false;
            }
        }

        $tenant['tenant_list'] = Tenant::all();           
        
        return response()->json($tenant);
    }

}
