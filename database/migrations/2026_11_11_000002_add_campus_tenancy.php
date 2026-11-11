<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TENANT_TABLES = [
        'users',
        'items',
        'claims',
        'handovers',
        'activity_logs',
        'item_matches',
    ];

    public function up(): void
    {
        Schema::create('campuses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $campusId = DB::table('campuses')->insertGetId([
            'name' => config('campus.default_name'),
            'slug' => config('campus.default_slug'),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach (self::TENANT_TABLES as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('campus_id')->nullable()->constrained()->restrictOnDelete();
            });

            DB::table($tableName)->whereNull('campus_id')->update(['campus_id' => $campusId]);

            Schema::table($tableName, function (Blueprint $table): void {
                $table->unsignedBigInteger('campus_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse(self::TENANT_TABLES) as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropConstrainedForeignId('campus_id');
            });
        }

        Schema::dropIfExists('campuses');
    }
};
