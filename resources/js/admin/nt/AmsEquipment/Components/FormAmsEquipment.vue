<template>
  <v-row dense>
    <v-col cols="12">
      <v-select
        v-model="form.category"
        label="Categoria"
        :items="equipment_types"
        outlined
        dense
        placeholder="Text"
      ></v-select>
    </v-col>
    <v-col cols="12">
      <v-text-field
        v-model="form.name"
        label="Nombre o Modelo"
        outlined
        dense
        placeholder="Text"
      ></v-text-field>
    </v-col>
    <v-col cols="12">
      <v-select
        v-model="form.unit"
        label="Unidad Medida"
        :items="units"
        outlined
        dense
        placeholder="Text"
      ></v-select>
    </v-col>
    <v-col cols="12">
      <v-text-field
        v-model="form.value"
        label="Valor"
        outlined
        dense
        placeholder="Text"
      ></v-text-field>
    </v-col>
    <v-col cols="12">
      <v-text-field
        v-model="form.price"
        label="Precio"
        outlined
        dense
        type="numeric"
        placeholder="Text"
        prefix="$"
      ></v-text-field>
    </v-col>
  </v-row>
</template>
<script>
export default {
  props: {
    form: {
      require: true,
      type: Object,
    },
  },
  data() {
    return {
      equipment_types: [
        "Arado",
        "Rastra",
        "Cultivadora",
        "Niveladora",
        "Sembradora",
        "Fumigadora",
      ],
      units: [
        "Discos",
        "Rejas",
        "Surcos",
        "Timones",
        "Cuchillas",
        "Cuerpo",
        "Aguilon",
        "Metros",
      ],
    };
  },
};
</script>
<style></style>
