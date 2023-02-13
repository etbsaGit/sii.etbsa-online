<template>
  <v-card flat>
    <v-card-text>
      <div class="text--h3">Formulario de Producto Proveedor</div>
      <pre>{{ form }}</pre>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  name: "SupplierProductForm",
  props: {
    form: {
      require: true,
      type: Object,
    },
  },
};
</script>
