<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarcasTable extends Migration
{
    // Migration que cria a tabela marcas no banco de dados
    public function up()
    {
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 30)->unique();
            $table->string('imagem', 100)->comment('Logo marca');
            $table->timestamps();
        });
    }

    /**
     * Reverter as ações da migrations.
     */
    public function down()
    {
        Schema::dropIfExists('marcas');
    }
}
