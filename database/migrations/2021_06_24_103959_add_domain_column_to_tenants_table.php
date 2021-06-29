<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddDomainColumnToTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('domain')->nullable()->after('group_app');
            $table->integer('db')->default(0)->after('tenant_group_id');
        });
        // jika multitenant aktif dan detect mode subdomain/domain
        if(
            config('AppConfig.system.multitenant.active',false) && 
            config('AppConfig.system.multitenant.detect_mode',1) == 2
        ){
            DB::table('tenants')->update([
                'domain' => DB::raw('CONCAT(`group_app`,".'.config('AppConfig.system.multitenant.main_domain').'")')
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
            $table->dropColumn(['domain','db']);
        });
    }
}
