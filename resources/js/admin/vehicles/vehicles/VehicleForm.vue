<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.matricula"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Matricula"
          required
          filled
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.model"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Modelo"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.brand"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Marca"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.serie"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Serie"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.ticket_card"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Ticket Card"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-select
          v-model="form.fuel"
          :rules="[(v) => !!v || 'Valor Requerido']"
          :items="['MAGNA', 'DIESEL']"
          label="Tipo Combustible"
          required
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.last_mileage"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Ultimo Kilometraje"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.max_lts_fuel"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Capacidad max Tanque (lts)"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.mileage_last_service"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="KMS ultimo Servicio"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-text-field
          v-model="form.mileage_range_service"
          :rules="[(v) => !!v || 'Valor Requerido']"
          label="Rango KMS para Servicio"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="4">
        <v-autocomplete
          v-model="form.agency_id"
          :items="options.agencies"
          item-text="title"
          item-value="id"
          label="Agencia"
          placeholder="Agencia a cual correponde."
          persistent-placeholder
          filled
          clearable
          outlined
          dense
        ></v-autocomplete>
      </v-col>

      <v-col cols="12" md="4">
        <v-autocomplete
          v-model="form.user_id"
          :items="options.users"
          item-text="name"
          item-value="id"
          label="Responsable Unidad"
          placeholder="Usuario a quien correcponde la unidad."
          persistent-placeholder
          filled
          clearable
          outlined
          dense
        ></v-autocomplete>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  props: {
    value: {
      type: Boolean,
      default: true,
    },
    form: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
  },
  created() {
    this.loadOptions(() => {});
  },
  data() {
    return {
      options: {
        agencies: [],
        users: [],
      },
    };
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
  methods: {
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicles/resources/options`)
        .then(function (response) {
          let Data = response.data.data;
          _this.options.users = Data.users;
          _this.options.agencies = Data.agencies;
          (cb || Function)();
        });
    },
  },
};
</script>
<style lang=""></style>
