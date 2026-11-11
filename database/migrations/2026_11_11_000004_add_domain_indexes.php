<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $indexes = [
        ['items', ['campus_id', 'status', 'date_event'], 'items_campus_status_date_event_idx'],
        ['items', ['campus_id', 'type'], 'items_campus_type_idx'],
        ['items', ['campus_id', 'location'], 'items_campus_location_idx'],
        ['items', ['user_id'], 'items_user_id_idx'],
        ['items', ['category_id'], 'items_category_idx'],
        ['claims', ['campus_id', 'status'], 'claims_campus_status_idx'],
        ['claims', ['item_id', 'status'], 'claims_item_status_idx'],
        ['claims', ['user_id'], 'claims_user_id_idx'],
        ['handovers', ['item_id'], 'handovers_item_id_idx'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as [$tableName, $columns, $indexName]) {
            if (collect(Schema::getIndexes($tableName))->contains(
                fn (array $index): bool => ($index['name'] ?? null) === $indexName
            )) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($columns, $indexName): void {
                $table->index($columns, $indexName);
            });
        }
    }

    public function down(): void
    {
        foreach (array_reverse($this->indexes) as [$tableName, , $indexName]) {
            if (! collect(Schema::getIndexes($tableName))->contains(
                fn (array $index): bool => ($index['name'] ?? null) === $indexName
            )) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($indexName): void {
                $table->dropIndex($indexName);
            });
        }
    }
};
