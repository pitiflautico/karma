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
        Schema::table('groups', function (Blueprint $table) {
            // Añadir columnas nuevas si no existen
            if (!Schema::hasColumn('groups', 'avatar')) {
                $table->string('avatar')->nullable()->after('description');
            }
            if (!Schema::hasColumn('groups', 'color')) {
                $table->string('color')->default('#8B5CF6')->after('avatar');
            }
            if (!Schema::hasColumn('groups', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('color')->constrained('users')->onDelete('set null');
            }
        });

        // Renombrar group_user a group_members si existe
        if (Schema::hasTable('group_user') && !Schema::hasTable('group_members')) {
            Schema::rename('group_user', 'group_members');
        }

        // Actualizar group_members table
        if (Schema::hasTable('group_members')) {
            Schema::table('group_members', function (Blueprint $table) {
                if (!Schema::hasColumn('group_members', 'role')) {
                    $table->enum('role', ['admin', 'member'])->default('member')->after('user_id');
                }
                if (!Schema::hasColumn('group_members', 'joined_at')) {
                    $table->timestamp('joined_at')->useCurrent()->after('role');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groups', function (Blueprint $table) {
            if (Schema::hasColumn('groups', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
            if (Schema::hasColumn('groups', 'color')) {
                $table->dropColumn('color');
            }
            if (Schema::hasColumn('groups', 'avatar')) {
                $table->dropColumn('avatar');
            }
        });

        if (Schema::hasTable('group_members')) {
            Schema::table('group_members', function (Blueprint $table) {
                if (Schema::hasColumn('group_members', 'joined_at')) {
                    $table->dropColumn('joined_at');
                }
                if (Schema::hasColumn('group_members', 'role')) {
                    $table->dropColumn('role');
                }
            });
        }
    }
};
