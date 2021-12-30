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
        >
          <template #prepend>
            <div class="d-flex flex-column">
              <v-switch
                v-model="form.bidon_fuel"
                dense
                class="ma-0 overline"
                persistent-hint
                hint="Bidon"
                :readonly="!!form.id"
              ></v-switch>
            </div>
          </template>
        </v-text-field>
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
          v-model="form.model"
          :rules="[(v) => !!v || 'Valor Requerido']"
          :label="ModelLabel"
          required
          outlined
          dense
        ></v-text-field>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" md="6" v-if="!form.bidon_fuel">
        <v-card color="grey lighten-4">
          <v-subheader class="title">
            <v-icon class="mr-2">mdi-settings</v-icon> Configuracion Vehiculo
          </v-subheader>
          <v-card-text>
            <v-row dense>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.year"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Año"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.brand"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Marca"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-select
                  v-model="form.fuel_id"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  :items="options.combustible"
                  item-text="name"
                  item-value="id"
                  label="Tipo Combustible"
                  required
                  outlined
                  dense
                >
                </v-select>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.liters_max_fuel"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Capacidad Tanque:"
                  placeholder="Litros"
                  persistent-placeholder
                  type="number"
                  suffix="Lts"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.liters_per_week"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Litros por Semana"
                  placeholder="Litros"
                  persistent-placeholder
                  suffix="Lts"
                  type="number"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.mileage_last"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Ultimo Kilometraje"
                  type="number"
                  suffix="kms"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.mileage_actual"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Actual Kilometraje"
                  type="number"
                  suffix="kms"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="12">
                <v-text-field
                  v-model="form.performance_fuel"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Rendimiento"
                  type="number"
                  suffix="Kms/litro"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.mileage_last_service"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Kilometraje u. Servicio"
                  placeholder="kilometraje"
                  type="number"
                  suffix="kms"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
              <v-col cols="12" md="6">
                <v-text-field
                  v-model="form.mileage_range_service"
                  :rules="[(v) => !!v || 'Valor Requerido']"
                  label="Rango Kilometraje por Servicio "
                  placeholder="Kilometraje"
                  type="number"
                  suffix="kms"
                  required
                  outlined
                  dense
                ></v-text-field>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" md="6">
        <v-card>
          <v-subheader class="title">
            <v-icon>mdi-office-building</v-icon> Asignacion y Tarjeta
          </v-subheader>
          <v-card-text>
            <v-row dense>
              <v-col cols="12">
                <v-autocomplete
                  v-model="form.agency_id"
                  :items="options.agencies"
                  item-text="title"
                  item-value="id"
                  label="Agencia"
                  placeholder="Agencia a cual correponde."
                  persistent-placeholder
                  clear-icon="mdi-trash-can"
                  filled
                  clearable
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  v-model="form.user_id"
                  :items="options.employees"
                  item-text="name"
                  item-value="id"
                  label="Responsable Unidad"
                  hint="Empleado a quien corresponde la unidad."
                  persistent-hint
                  clear-icon="mdi-trash-can"
                  filled
                  clearable
                  outlined
                  dense
                ></v-autocomplete>
                <!-- <v-autocomplete
                  v-model="form.user_id"
                  :items="options.users"
                  item-text="name"
                  item-value="id"
                  label="Responsable Unidad"
                  placeholder="Usuario a quien corresponde la unidad."
                  persistent-placeholder
                  clear-icon="mdi-trash-can"
                  filled
                  clearable
                  outlined
                  dense
                ></v-autocomplete> -->
              </v-col>
              <v-col cols="12">
                <v-autocomplete
                  v-model="form.ticket_card"
                  :items="options.tickets"
                  item-text="ticket_card"
                  item-value="ticket_card"
                  label="Ticket Card"
                  placeholder="Asignar Tarjeta Disponibles"
                  type="number"
                  persistent-placeholder
                  clear-icon="mdi-trash-can"
                  filled
                  clearable
                  outlined
                  dense
                ></v-autocomplete>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
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
        combustible: [],
        tickets: [],
        employees: [],
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
    ModelLabel() {
      return this.form.bidon_fuel ? "Nombre Bidon" : "Modelo Vehiculo";
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
          _this.options.employees = Data.employees;
          _this.options.agencies = Data.agencies;
          _this.options.combustible = Data.combustible;
          _this.options.tickets = Data.tickets;
          (cb || Function)();
        });
    },
  },
};
</script>
<style lang=""></style>
