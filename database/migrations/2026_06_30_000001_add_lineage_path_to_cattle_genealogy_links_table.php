<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cattle_genealogy_links', function (Blueprint $table) {
            $table->string('lineage_path', 20)->nullable()->after('relation_type');
            $table->index(['cattle_id', 'lineage_path'], 'cattle_genealogy_links_cattle_lineage_idx');
        });

        $legacyPaths = [
            'father' => 'F',
            'mother' => 'M',
            'paternal_grandfather' => 'FF',
            'paternal_grandmother' => 'FM',
            'maternal_grandfather' => 'MF',
            'maternal_grandmother' => 'MM',
        ];

        foreach ($legacyPaths as $relationType => $path) {
            DB::table('cattle_genealogy_links')
                ->where('relation_type', $relationType)
                ->whereNull('lineage_path')
                ->update(['lineage_path' => $path]);
        }
    }

    public function down(): void
    {
        Schema::table('cattle_genealogy_links', function (Blueprint $table) {
            $table->dropIndex('cattle_genealogy_links_cattle_lineage_idx');
            $table->dropColumn('lineage_path');
        });
    }
};
