<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_configurations', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('type');

            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('translatable')->default(0);

            $table->string('group')->nullable();
            $table->integer('in_group_position')->default(1);

            $table->text('value')->nullable();
            $table->boolean('status')->default(1);

            $table->timestamps();
        });

        Schema::create('admin_configuration_translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->unsignedBigInteger('admin_configuration_id');

            $table->text('content')->nullable();

            $table->unique(['admin_configuration_id', 'locale'], 'a_conf_transl_admin_conf_locale');

            $table->foreign('admin_configuration_id')->references('id')->on('admin_configurations')
                ->cascadeOnDelete()->cascadeOnUpdate();
        });

        PermissionRelation::touch('admin_configurations')->addCustom()->all();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_configuration_translations');
        Schema::dropIfExists('admin_configurations');
    }
}
