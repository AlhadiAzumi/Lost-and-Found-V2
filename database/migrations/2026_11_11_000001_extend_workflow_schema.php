<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ITEM_STATUSES = [
        'pending',
        'approved',
        'rejected',
        'claimed',
        'claim_in_progress',
        'returned',
        'closed',
        'expired',
    ];

    private const CLAIM_STATUSES = ['pending', 'approved', 'rejected', 'completed'];

    public function up(): void
    {
        if (! Schema::hasTable('password_reset_tokens')) {
            Schema::create('password_reset_tokens', function (Blueprint $table): void {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        if (! Schema::hasColumn('items', 'security_answer')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->string('security_answer')->nullable();
            });
        }

        if (! Schema::hasColumn('items', 'rejection_reason')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->text('rejection_reason')->nullable();
            });
        }

        if (! Schema::hasColumn('items', 'storage_location')) {
            Schema::table('items', function (Blueprint $table): void {
                $table->string('storage_location')->nullable();
            });
        }

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE items DROP CONSTRAINT IF EXISTS items_status_check');
            DB::statement('ALTER TABLE claims DROP CONSTRAINT IF EXISTS claims_status_check');
        }

        $this->changeStatusColumn('items', self::ITEM_STATUSES);
        $this->changeStatusColumn('claims', self::CLAIM_STATUSES);

        if (! Schema::hasIndex('handovers', 'handovers_claim_id_unique')) {
            Schema::table('handovers', function (Blueprint $table): void {
                $table->unique('claim_id');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE items DROP CONSTRAINT IF EXISTS items_status_check');
            DB::statement('ALTER TABLE claims DROP CONSTRAINT IF EXISTS claims_status_check');
        }

        DB::table('items')->where('status', 'claim_in_progress')->update(['status' => 'approved']);
        DB::table('items')->where('status', 'returned')->update(['status' => 'claimed']);
        DB::table('items')->where('status', 'closed')->update(['status' => 'expired']);
        DB::table('claims')->where('status', 'completed')->update(['status' => 'approved']);

        $this->changeStatusColumn('items', ['pending', 'approved', 'rejected', 'claimed', 'expired']);
        $this->changeStatusColumn('claims', ['pending', 'approved', 'rejected']);

        if (Schema::hasIndex('handovers', 'handovers_claim_id_unique')) {
            Schema::table('handovers', function (Blueprint $table): void {
                $table->dropUnique('handovers_claim_id_unique');
            });
        }

        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn(['security_answer', 'rejection_reason', 'storage_location']);
        });

        Schema::dropIfExists('password_reset_tokens');
    }

    private function changeStatusColumn(string $tableName, array $statuses): void
    {
        Schema::table($tableName, function (Blueprint $table): void {
            $table->string('status')->default('pending')->change();
        });

        if (DB::getDriverName() === 'pgsql') {
            $values = "'".implode("', '", $statuses)."'";
            DB::statement("ALTER TABLE {$tableName} ADD CONSTRAINT {$tableName}_status_check CHECK (status IN ({$values}))");

            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        Schema::table($tableName, function (Blueprint $table) use ($statuses): void {
            $table->enum('status', $statuses)->default('pending')->change();
        });
    }
};
