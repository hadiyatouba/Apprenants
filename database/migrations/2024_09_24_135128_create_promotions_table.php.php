<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePromotionsTable extends Migration
{
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique();
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->string('statut')->default('Inactif');
            $table->string('photo_couverture')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('promotions');
    }
}

