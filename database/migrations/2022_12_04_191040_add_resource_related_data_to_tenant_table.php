<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use App\Facades\Tenant;

class AddResourceRelatedDataToTenantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->double('res_storage_size')->default(0)->after('s3storage');
            $table->double('res_db_size')->default(0)->after('res_storage_size');
            $table->dateTime('res_storage_size_last_update')->nullable()->after('res_db_size');
            $table->dateTime('res_db_size_last_update')->nullable()->after('res_storage_size_last_update');
        });
        
        $config = '{"storage_limit":0,"db_limit":0,"resource_limit":0}';

        // calculate existing tenant resource
        $tenantList = DB::table('tenants')->get();
        foreach ($tenantList as $value) {
            if($value->status)
                DB::table('tenants')->where('id',$value->id)->update([
                    'res_storage_size'=>Tenant::storageSize($value->id),
                    'res_db_size'=>Tenant::getDbSize($value->id),
                    'res_storage_size_last_update'=>now(),
                    'res_db_size_last_update'=>now(),
                    'config'=>$config
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
            $table->dropColumn(['res_storage_size','res_db_size','res_storage_size_last_update','res_db_size_last_update']);
        });
    }
}
