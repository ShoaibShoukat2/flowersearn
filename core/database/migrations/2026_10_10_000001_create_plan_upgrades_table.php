<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plan_upgrades')) {
            Schema::create('plan_upgrades', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('from_plan_id')->nullable();
                $table->unsignedBigInteger('to_plan_id');
                $table->decimal('amount', 28, 8)->default(0);
                $table->string('sender_name')->nullable();
                $table->string('sender_number')->nullable();
                $table->string('screenshot')->nullable();
                $table->unsignedInteger('method_code')->nullable();
                $table->string('trx', 40)->nullable();
                $table->tinyInteger('status')->default(2);
                $table->text('admin_feedback')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
                $table->index(['status', 'created_at']);
                $table->index('user_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_upgrades');
    }
};
