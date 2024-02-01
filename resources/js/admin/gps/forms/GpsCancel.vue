<template>
  <v-container>
    <v-row dense>
      <v-col cols="12">
        <v-text-field
          v-model="form.cancellation_date"
          :rules="rules"
          label="Fecha de Cancelacion*"
          type="date"
          class="mx-2"
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.description"
          :rules="rules"
          label="Motivo de Cancelacion*"
          class="mx-2"
          type="date"
          outlined
          dense
        />
      </v-col>
    </v-row>
    <small>*Indica Campo requerido</small>
  </v-container>
</template>

<script>
export default {
  name: "GpsCancelForm",
  props: {
    form: {
      required: true,
    },
  },
  data() {
    return {
      rules: [(v) => !!v || "Campo Requerido"],
    };
  },
};
</script>

<style></style>
