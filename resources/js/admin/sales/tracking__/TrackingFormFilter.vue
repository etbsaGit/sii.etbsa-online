<template>
  <v-container fluid>
    <v-row dense>
      <v-col cols="12">
        <v-text-field
          label="id"
          placeholder=" Buscar por ID"
          outlined
          dense
          hide-details
          filled
          prepend-icon="mdi-magnify"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          label="id"
          placeholder=" Buscar por ID"
          outlined
          dense
          hide-details
          filled
          prepend-icon="mdi-magnify"
        ></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-select
          label="id"
          placeholder=" Buscar por ID"
          outlined
          dense
          hide-details
          filled
          prepend-icon="mdi-filter-variant"
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-select
          label="id"
          placeholder=" Buscar por ID"
          outlined
          dense
          hide-details
          filled
          prepend-icon="mdi-filter-variant"
        ></v-select>
      </v-col>
    </v-row>
  </v-container>
</template>
<script>
export default {
  name: "TrackinFormFilter",
};
</script>
