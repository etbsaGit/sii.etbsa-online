<template>
  <v-form ref="formProductInventory" lazy-validation>
    <v-row dense class="text-h5 text-uppercase">
      <v-col cols="12">
        <v-autocomplete
          v-model="form.product_id"
          :items="options.products"
          item-text="name"
          item-value="id"
          label="Seleccionar Producto"
          placeholder="SKU, Nombre, Categoria"
          hint="Busque un Producto del Catalogo"
          persistent-hint
          :filter="customFilter"
          :rules="[requiredValidator]"
          clearable
          outlined
          filled
        >
          <template v-slot:selection="{ item }">
            <div class="d-flex flex-column title">
              {{ item.name }}
              <div class="d-flex">
                <v-chip label color="secondary" dark>
                  {{ item.category.name }}
                </v-chip>
                <v-chip label color="primary" dark>
                  sku: {{ item.sku }}
                </v-chip>
              </div>
            </div>
          </template>
          <template #item="{ item }">
            <v-list-item-content>
              <v-list-item-subtitle>
                {{ item.category.name }}
              </v-list-item-subtitle>
              <v-list-item-title> {{ item.name }} </v-list-item-title>
              <v-list-item-subtitle> {{ item.sku }} </v-list-item-subtitle>
            </v-list-item-content>
          </template>
        </v-autocomplete>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.model"
          label="Modelo"
          outlined
          placeholder="Modelo"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.num_serie"
          label="Serie"
          outlined
          placeholder="Serie"
          :rules="[requiredValidator]"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.num_serie_motor"
          label="Serie Motor"
          outlined
          placeholder="Serie Motor"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.num_economico"
          label="Num. Economico"
          outlined
          placeholder="Numeor Economico"
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-currency-field
          v-model.number="form.costo_jd"
          :default-value="0"
          label="Costo JD"
          placeholder="0.00"
          prefix="$"
          :suffix="'MXN'"
          type="number"
          outlined
          :rules="[requiredValidator]"
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-currency-field
          v-model.number="form.costo_etbsa"
          :default-value="0"
          label="Costo ETBSA"
          placeholder="0.00"
          prefix="$"
          :suffix="'MXN'"
          type="number"
          outlined
          :rules="[requiredValidator]"
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.arrival_date"
          label="Fecha de Ingreso"
          type="date"
          outlined
          
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.invoice"
          label="Folio Factura"
          outlined
          placeholder="Folio Factura"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.invoice_date"
          label="Fecha Facturacion"
          type="date"
          outlined
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-select
          v-model="form.location"
          :items="options.agencies"
          item-value="id"
          item-text="title"
          label="Ubicacion Fisico"
          outlined
          filled
          :rules="[requiredValidator]"
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-select
          v-model="form.assigned_branch"
          :items="options.agencies"
          item-value="id"
          item-text="title"
          label="Sucursal Asignada"
          outlined
          filled
          :rules="[requiredValidator]"
        ></v-select>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
import { requiredValidator,integerValidator } from "@validator";
export default {
  name: "ProductInventoryForm",
  props: {
    options: {
      type: Object,
      require: true,
      validator: function (value) {
        return Array.isArray(value.products) && Array.isArray(value.agencies);
      },
      default: function () {
        return {
          products: [],
          agencies: [],
        };
      },
    },
    form: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      requiredValidator,
      integerValidator
    };
  },
  methods: {
    customFilter(item, queryText, itemText) {
      const words = queryText.toLowerCase().split(" ");

      return words.every((word) => {
        const nameMatch = item.name.toLowerCase().includes(word);
        const skuMatch = item.sku.toLowerCase().includes(word);
        const categoryNameMatch = item.category.name
          .toLowerCase()
          .includes(word);

        return nameMatch || skuMatch || categoryNameMatch;
      });
    },
  },
};
</script>
<style></style>
