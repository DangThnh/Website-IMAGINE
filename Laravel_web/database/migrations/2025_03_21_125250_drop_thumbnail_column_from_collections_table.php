<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('collections', function (Blueprint $table) {
            // Kiểm tra xem cột thumbnail có tồn tại không trước khi xóa
            if (Schema::hasColumn('collections', 'thumbnail')) {
                $table->dropColumn('thumbnail');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('collections', function (Blueprint $table) {
            // Khôi phục cột thumbnail nếu cần
            $table->string('thumbnail')->nullable()->after('is_public');
        });
    }
};
