<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::table('iaccounting__purchases', function (Blueprint $table) {
      $table->integer('accountingaccount_id')->unsigned()->nullable();
      $table->foreign('accountingaccount_id')->references('id')->on('iaccounting__accountingaccounts')->onDelete('cascade');

      $table->integer('provider_id')->unsigned();
      $table->foreign('provider_id')->references('id')->on('iaccounting__providers')->onDelete('cascade');
    });

    if(Schema::hasColumn('iaccounting__purchases', 'provider_name')) {
      // Remove unused columns from purchase
      Schema::table('iaccounting__purchases', function ($table) {
        $table->dropColumn('provider_name');
        $table->dropColumn('provider_id_type');
        $table->dropColumn('provider_id_number');
      });
    }
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('iaccounting__purchases', function (Blueprint $table) {
      $table->dropForeign(['accountingaccount_id']);
      $table->dropForeign(['provider_id']);
    });
  }
};
