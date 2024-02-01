<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingImportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marketing_import', function (Blueprint $table) {
            $table->id();
            $table->string("ID_SUC")->nullable();
            $table->string("SUCURSAL")->nullable();
            $table->string("NO_ECO")->nullable();
            $table->string("NO_DOCUMENTO")->nullable();
            $table->string("TIPO_DOCUMENTO")->nullable();
            $table->string("NO_DOC_INV")->nullable();
            $table->string("MODULO")->nullable();
            $table->string("SERIE_FISCAL")->nullable();
            $table->string("FOLIO_FISCAL")->nullable();
            $table->string("TIPO_DE_VENTA")->nullable();
            $table->dateTime("FECHA_REQ")->nullable();
            $table->dateTime("FECHA_FACTURA")->nullable();
            $table->string("MES")->nullable();
            $table->string("ANIO")->nullable();
            $table->string("MARCA")->nullable();
            $table->string("MODELO")->nullable();
            $table->text("DESCRIPCION_PRODUCTO")->nullable();
            $table->string("NIP")->nullable();
            $table->string("CLAVE_CLIENTE")->nullable();
            $table->string("NOMBRE_CLIENTE")->nullable();
            $table->string("VENDEDOR")->nullable();
            $table->string("EMAIL")->nullable();
            $table->text("CALLE")->nullable();
            $table->string("CIUDAD")->nullable();
            $table->string("ESTADO")->nullable();
            $table->string("CP")->nullable();
            $table->string("RFC_COMPANIA")->nullable();
            $table->string("PAGO_EFECTIVO")->nullable();
            $table->string("PAGADO_TARJETA_CREDITO")->nullable();
            $table->string("PAGADO_CHEQUE")->nullable();
            $table->string("IMPUESTO_REG")->nullable();
            $table->string("MONEDA")->nullable();
            $table->double("TIPO_CAMBIO",8,4)->nullable();
            $table->double("VALOR_FORANEO",8,2)->nullable();
            $table->string("METODO_PAGO")->nullable();
            $table->double("PRECIO_VENTA",8,2)->nullable();
            $table->double("IMPUESTO_INVENTARIO",8,2)->nullable();
            $table->double("SUBTOTAL",8,2)->nullable();
            $table->double("IMPUESTO",8,2)->nullable();
            $table->double("TOTAL",8,2)->nullable();
            $table->double("TOTAL_COSTO",8,2)->nullable();
            $table->double("MARGEN",8,2)->nullable();
            $table->double("PORCENTAJE_MARGEN",8,2)->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('marketing_import');
    }
}
