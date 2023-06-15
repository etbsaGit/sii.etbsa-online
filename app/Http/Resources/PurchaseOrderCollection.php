<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        // {"id":1,"name":"Equipos",
        //     "concept_product":[],
        //     "usocfdi":[{"id":10,"clave":"I04","description":"Equipo de computo y accesorios"}]
        // }
        return [
            'id' => $this->id,
            'estatus' => $this->estatus->only('id', 'key', 'title'),
            'created_by' => $this->created_by,
            'supplier' => $this->supplier->only('id', 'code_equip', 'business_name', 'rfc', 'credit_days', 'isActive'),
            'amounts' => $this->only(
                'subtotal',
                'discount',
                'with_tax',
                'tax',
                'with_isr',
                'tax_isr',
                'with_iva_retenido',
                'tax_iva_retenido',
                'with_retencion_cedular',
                'tax_retencion_cedular',
                'with_retencion_125',
                'tax_retencion_125',
                'with_flete',
                'tax_flete',
                'total',
            ),
            'products' => $this->products,
            'charges' => $this->charges,
            'invoice' => [
                'metodo_pago' => $this->metodopago,
                'uso_cfdi' => $this->usocfdi,
                'forma_pago' => $this->formapago,
            ],
            'purchase_concept' => $this->purchase_concept->load('usocfdi:id,clave,description'),
            'purchase_type_id' => $this->purchase_type_id,
            'purchase_type' => $this->purchaseType->only('id', 'name'),
            'agency_id' => $this->agency_id,
            'observation' => $this->observation,
            'note' => $this->note,
            'payment_condition' => $this->payment_condition,
            'file' => $this->files->map->only('id', 'name', 'file_path', 'file_type', 'created_at'),
            'invoice_info' => $this->invoice
        ];
    }
}