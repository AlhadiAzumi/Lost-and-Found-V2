<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('campus_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->timestamps();
            $table->unique(['campus_id', 'name']);
        });

        Schema::table('items', function (Blueprint $table): void {
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
        });

        DB::table('items')
            ->select(['campus_id', 'location'])
            ->whereNotNull('location')
            ->distinct()
            ->orderBy('campus_id')
            ->orderBy('location')
            ->get()
            ->each(function (object $row): void {
                $now = now();
                $locationId = DB::table('locations')
                    ->where('campus_id', $row->campus_id)
                    ->where('name', $row->location)
                    ->value('id');

                if ($locationId === null) {
                    $locationId = DB::table('locations')->insertGetId([
                        'campus_id' => $row->campus_id,
                        'name' => $row->location,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('items')
                    ->where('campus_id', $row->campus_id)
                    ->where('location', $row->location)
                    ->update(['location_id' => $locationId]);
            });

        Schema::create('notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50)->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        Schema::table('items', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('location_id');
        });

        Schema::dropIfExists('locations');
    }
};
