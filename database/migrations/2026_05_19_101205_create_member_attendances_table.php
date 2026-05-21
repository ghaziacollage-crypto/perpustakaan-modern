<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Member::class)->constrained()->cascadeOnDelete();
            $table->timestamp('scanned_at')->useCurrent();
            $table->enum('status', ['active', 'left'])->default('active')->comment('active|left');
            $table->timestamps();

            $table->unique('member_id', 'unique_active_member')->where('status', 'active');
            $table->index(['status', 'scanned_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_attendances');
    }
};
