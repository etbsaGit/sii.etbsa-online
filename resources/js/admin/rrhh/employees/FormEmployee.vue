<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="text-uppercase">
      <v-col cols="12">
        <v-text-field
          v-model="form.name"
          label="Nombre"
          hint="es requerido"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12">
        <v-text-field
          v-model="form.last_name"
          label="Apellidos"
          hint="es requerido"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.number_employee"
          label="Numero de Empleado"
          hint="es requerido"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.job_title"
          label="Puesto"
          hint="es requerido"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="6">
        <v-select
          v-model="form.agency_id"
          label="Sucursal"
          :items="options.agencies"
          item-text="title"
          item-value="id"
          outlined
          dense
          filled
        ></v-select>
      </v-col>
      <v-col cols="6">
        <v-select
          v-model="form.department_id"
          label="Departamento"
          :items="options.departments"
          item-text="title"
          item-value="id"
          outlined
          dense
          filled
        ></v-select>
      </v-col>
      <v-col cols="12">
        <v-menu
          ref="menu"
          v-model="menu"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="form.birthday_date"
              label="Fecha Nacimiento"
              prepend-icon="mdi-calendar"
              readonly
              dense
              outlined
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="form.birthday_date"
            :active-picker.sync="activePicker"
            :max="
              new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
                .toISOString()
                .substr(0, 10)
            "
            min="1921-01-01"
            @change="save"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.phone"
          label="Telefono"
          hint="es requerido"
          outlined
          dense
        ></v-text-field>
      </v-col>

      <v-col cols="12">
        <v-textarea
          v-model="form.address"
          label="Direccion"
          dense
          outlined
          filled
        ></v-textarea>
      </v-col>
      <v-col cols="8">
        <v-text-field
          v-model="form.colonia"
          label="Colonia"
          dense
          outlined
        ></v-text-field>
      </v-col>
      <v-col cols="4">
        <v-text-field
          v-model="form.code_postal"
          label="Codigo Postal"
          dense
          outlined
        ></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-autocomplete
          v-model="Estate"
          :items="options.estates"
          item-text="name"
          item-value="id"
          label="Estado:"
          filled
          outlined
          dense
        />
      </v-col>
      <v-col cols="6">
        <v-autocomplete
          v-model="form.town_id"
          :items="options.townships"
          label="Municipio"
          item-text="name"
          item-value="id"
          :rules="[(v) => !!v || 'El municipio es requerido']"
          filled
          outlined
          outline
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-menu
          ref="menu"
          v-model="menu2"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <v-text-field
              v-model="form.admission_date"
              label="Fecha de Ingreso"
              prepend-icon="mdi-calendar"
              readonly
              dense
              outlined
              v-bind="attrs"
              v-on="on"
            ></v-text-field>
          </template>
          <v-date-picker
            v-model="form.admission_date"
            :active-picker.sync="activePicker"
            :max="
              new Date(Date.now() - new Date().getTimezoneOffset() * 60000)
                .toISOString()
                .substr(0, 10)
            "
            min="1921-01-01"
            @change="save"
          ></v-date-picker>
        </v-menu>
      </v-col>
      <v-col cols="12">
        <v-autocomplete
          v-model="form.direct_boss"
          :items="options.direct_boss"
          item-text="full_name"
          item-value="id"
          label="Jefe directo:"
          filled
          outlined
          dense
        />
      </v-col>
      <v-col cols="12">
        <v-switch v-model="form.active" label="Activo"></v-switch>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  name: "FormEmployee",
  mixins: [mixinEstates],
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
  computed: {
    valid: {
      get() {
        return this.value;
      },
      set(value) {
        this.$emit("input", value);
      },
    },
    Estate: {
      get() {
        return !!this.form.town_id && this.form.township
          ? (this.estate_id = this.form.township.estate_id)
          : 0;
      },
      set(_estate) {
        this.estate_id = _estate;
      },
    },
  },
  mounted() {
    const _this = this;
    _this.loadOptions();
    _this.loadEstates(() => {
      _this.estate_id = _this.Estate;
    });
  },
  data() {
    return {
      menu: false,
      menu2: false,
      activePicker: null,
      options: {
        agencies: [],
        departments: [],
        direct_boss: [],
      },
    };
  },
  methods: {
    save(date) {
      this.$refs.menu.save(date);
    },
    loadOptions() {
      const _this = this;
      axios.get("/admin/employees/create").then((res) => {
        _this.options.agencies = res.data.data.agencies;
        _this.options.departments = res.data.data.departments;
        _this.options.direct_boss = res.data.data.direct_boss;
      });
    },
  },
  watch: {
    menu(val) {
      val && setTimeout(() => (this.activePicker = "YEAR"));
    },
    menu2(val) {
      val && setTimeout(() => (this.activePicker = "YEAR"));
    },
  },
};
</script>
<style></style>
