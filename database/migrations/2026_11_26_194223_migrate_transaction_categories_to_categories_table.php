<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $combinationsCategoryAndType = DB::table('transactions')
            ->select('category', 'type')
            ->distinct()
            ->get();

        foreach ($combinationsCategoryAndType as $combination) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $combination->category,
                'type' => $combination->type,
                'created_at' => now()->format('Y-m-d\TH:i:s'),
                'updated_at' => now()->format('Y-m-d\TH:i:s'),
            ]);

            DB::table('transactions')
                ->where('category', $combination->category)
                ->where('type', $combination->type)
                ->update(['category_id' => $categoryId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            //
        });
    }
};
