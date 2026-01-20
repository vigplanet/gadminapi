<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('blog_categories')) {
            Schema::create('blog_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('meta_title')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->text('meta_description')->nullable();
                $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->unsignedBigInteger('category_id');
                $table->string('image')->nullable();
                $table->text('description');
                $table->string('meta_title')->nullable();
                $table->text('meta_keywords')->nullable();
                $table->text('meta_description')->nullable();
                $table->integer('views_count')->default(0)->comment('Total view count for this blog');
                $table->tinyInteger('status')->default(1)->comment('1=Active, 0=Inactive');
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('blog_categories')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('blog_views')) {
            Schema::create('blog_views', function (Blueprint $table) {
                $table->unsignedBigInteger('blog_id');
                $table->string('ip_address');

                $table->foreign('blog_id')->references('id')->on('blogs')->onDelete('cascade');
                $table->unique(['blog_id', 'ip_address']);
                $table->index(['blog_id', 'ip_address']);
            });
        }

        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'message')) {
                $table->text('message')->change();
            }
        });

        // Add door_step_mode column to sellers table
        if (Schema::hasTable('sellers') && !Schema::hasColumn('sellers', 'door_step_mode')) {
            Schema::table('sellers', function (Blueprint $table) {
                $table->tinyInteger('door_step_mode')->default(1)->comment('1=Enabled, 0=Disabled')->after('self_pickup_mode');
            });
        }

        if (!DB::table('settings')->where('variable', 'time_slot_setting')->exists()) {
            DB::table('settings')->insert([
                'variable' => 'time_slot_setting',
                'value' => 'true'
            ]);
        }
        if (!Schema::hasTable('user_product_requests')) {
        Schema::create('user_product_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
            });
        }

        // Add is_free_delivery column to time_slots table
        if (Schema::hasTable('time_slots') && !Schema::hasColumn('time_slots', 'is_free_delivery')) {
            Schema::table('time_slots', function (Blueprint $table) {
                $table->tinyInteger('is_free_delivery')->default(0)->after('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')->where('variable', 'time_slot_setting')->delete();
        DB::table('settings')->where('variable', 'door_step_mode')->delete();

        if (Schema::hasTable('blog_views')) {
            Schema::dropIfExists('blog_views');
        }
        if (Schema::hasTable('blogs')) {
            Schema::dropIfExists('blogs');
        }
        if (Schema::hasTable('blog_categories')) {
            Schema::dropIfExists('blog_categories');
        }
        if (Schema::hasTable('user_product_requests')) {
            Schema::dropIfExists('user_product_requests');
        }
    }
};
