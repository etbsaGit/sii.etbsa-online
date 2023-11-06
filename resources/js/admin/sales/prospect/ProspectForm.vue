<template>
  <v-form ref="form" v-model="valid" lazy-validation @submit.prevent="submit()">
    <v-row align="start" align-content="start" dense>
      <v-col cols="12">
        <v-text-field
          v-model="form.full_name"
          label="Nombre del Contacto *"
          placeholder="Nombre completo"
          persistent-placeholder
          :rules="rules"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.company"
          label="Compañia o Razon Social "
          outlined
          filled
          dense
        />
      </v-col>

      <v-col cols="12">
        <v-text-field v-model="form.rfc" label="RFC" filled dense outlined />
      </v-col>

      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.email"
          label="Correo Electronico"
          type="email"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.phone"
          label="Telefono *"
          counter="10"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-textarea
          v-model="form.town"
          label="Domicilio:"
          placeholder="(Comunidad o Rancho)"
          height="100"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12" md="6">
        <v-autocomplete
          v-model="form.estate_id"
          :items="options.estates"
          item-text="name"
          item-value="id"
          label="Estado *"
          
          outlined
          filled
          dense
        />
      </v-col>

      <v-col cols="12" md="6">
        <v-autocomplete
          v-model="form.township_id"
          :items="options.townships"
          label="Municipio *"
          item-text="name"
          item-value="id"
          outlined
          filled
          dense
        />
      </v-col>
      <v-col cols="12" md="6">
        <!-- <p class="text-14 mb-1">Segmentacion Prospecto</p> -->
        <v-select
          v-model="form.segmentacion"
          :items="['Chico', 'Mediano', 'Grande', 'Agroindustrial', 'Jardinero']"
          label="Segmentacion Cliente"
          outlined
          filled
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="6">
        <!-- <p class="text-14 mb-1">Capacidad Tecnologia</p> -->
        <v-select
          v-model="form.capacidad_tech"
          :items="['Baja', 'Mediana', 'Alta', 'Experto']"
          label="Capacidad de Tecnologia"
          outlined
          filled
          dense
          ></v-select>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
          v-model="form.rating"
          :items="['AAA', 'AA', 'A', 'Lista Negra']"
          label="Calificacion"
          outlined
          filled
          dense
          ></v-select>

        </v-col>
    </v-row>

    <v-btn block type="submit" color="primary" :disabled="!valid">
      {{ textBtn }}
    </v-btn>
  </v-form>
</template>
<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  mixins: [mixinEstates],
  props: {
    form: {
      type: Object,
      required: true,
      default: () => {
        return {};
      },
    },
    textBtn: {
      type: String,
      required: false,
      default: "Guardar",
    },
  },
  data() {
    return {
      valid: true,
      rules: [(v) => !!v || "campo requerido."],
    };
  },
  mounted() {
    const _this = this;
    _this.loadEstates(() => {
      _this.estate_id = _this.form.estate_id ? _this.form.estate_id : null;
    });
  },
  methods: {
    submit() {
      if (!this.$refs.form.validate()) return;
      this.$eventBus.$emit("PROSPECT-FORM-SUBMIT");
      this.$emit("submit");
    },
  },
};
</script>
<style></style>
