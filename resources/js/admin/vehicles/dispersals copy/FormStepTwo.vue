<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense align="start">
      <v-col cols="12" md="4" lass="d-flex flex-column">
        <div class="mb-2 title">Motivo Dispercion:</div>
        <v-select
          v-model="form.reason"
          label="Razon  de Dispercion:"
          :items="reason_dispersal"
          outlined
          :rules="[(v) => !!v || 'es requerido']"
          dense
        ></v-select>
        <v-textarea
          v-model="form.reason_description"
          label="Motivo de Dispercion*"
          placeholder="Escribir Motivo de Dispercion"
          counter="255"
          outlined
          dense
        ></v-textarea>
      </v-col>
      <v-col cols="12" md="4" class="d-flex flex-column">
        <div class="mb-2 title">Con Cargo a:</div>
        <v-select
          v-model="form.agency_id"
          label="Sucursal:"
          prepend-icon="mdi-domain"
          :items="options.agencies"
          item-value="id"
          item-text="title"
          :rules="[(v) => !!v || 'es requerido']"
          outlined
          dense
        ></v-select>
        <v-select
          v-model="form.department_id"
          label="Departamento:"
          :items="options.departments"
          prepend-icon="mdi-vector-arrange-below"
          item-value="id"
          item-text="title"
          :rules="[(v) => !!v || 'es requerido']"
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="4" cl4ss="d-flex flex-column">
        <div class="mb-2 title">Info adicional:</div>
        <v-text-field
          v-model="form.dispersal_date"
          label="Fecha para dispersar"
          prepend-icon="mdi-calendar"
          type="date"
          outlined
          dense
        />
        <template v-if="form.reason == 'Servicio'">
          <div class="mb-2 subtitle">Relacionar con (OT, Factura o NE):</div>
          <v-text-field
            v-model="form.service_ref"
            label="Relacionar"
            prepend-icon="mdi-alpha-a-box"
            outlined
            dense
          />
        </template>
        <template v-if="form.reason == 'Viaje'">
          <div class="mb-2 subtitle">Indicar Origen y Destino del viaje:</div>
          <v-text-field
            v-model="form.origen"
            label="Origen"
            prepend-icon="mdi-arrow-collapse-right"
            outlined
            dense
          />
          <v-text-field
            v-model="form.destino"
            label="Destino"
            prepend-icon="mdi-arrow-collapse-left"
            outlined
            dense
          />
        </template>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  name: "DispersalStepTwo",
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    options: {
      type: Object,
    },
    form: {
      type: Object,
      require: true,
      default: () => {
        return {};
      },
    },
  },
  computed: {
    valid: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
  },
  data() {
    return {
      reason_dispersal: ["Solicitud Semanal", "Servicio", "Viaje", "Otro"],
    };
  },
  methods: {},
};
</script>
<style></style>
