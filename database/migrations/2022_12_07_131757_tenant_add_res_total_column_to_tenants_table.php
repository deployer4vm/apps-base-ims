<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class TenantAddResTotalColumnToTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->double('res_total')->default(0)->after('res_db_size');
        });
        
        // calculate existing tenant resource
        $tenantList = DB::table('tenants')->get();
        foreach ($tenantList as $value) {
            if($value->status)
                DB::table('tenants')->where('id',$value->id)->update([
                    'res_total'=>$value->res_db_size + $value->res_storage_size,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['res_total']);
        });
    }
}
