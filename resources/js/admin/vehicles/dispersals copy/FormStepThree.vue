<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row class="mt-2 pt-4 overline" justify="center" align="center">
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.fuel_lts"
          autofocus
          outlined
          dense
          label="Litros"
          class="mx-auto text-wrap"
          @click:append-outer="form.fuel_lts++"
          @click:prepend="minusLts()"
          append-outer-icon="mdi-plus"
          prepend-icon="mdi-minus"
          style="max-width: 300px;"
          :hint="`${vehicle.max_lts_fuel} lts Cantidad Maxima del Tanque`"
          persistent-hint
        >
        </v-text-field>
      </v-col>
      <v-col
        cols="12"
        md="6"
        class="d-flex flex-column justify-center align-center text-h3"
      >
        {{ form.fuel_lts }} Lts
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {};
</script>
<style></style>
