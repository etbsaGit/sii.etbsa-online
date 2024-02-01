<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="text-uppercase">
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
        <!-- <v-text-field
          v-model="form.job_title"
          label="Puesto"
          hint="es requerido"
          :rules="[(v) => !!v || 'Valos Requerido']"
          outlined
          dense
        >
        </v-text-field> -->
        <v-select
          v-model="form.job_id"
          label="Puesto"
          :items="options.jobs"
          :rules="[(v) => !!v || 'Valos Requerido']"
          item-text="title"
          item-value="id"
          outlined
          dense
          filled
        ></v-select>
      </v-col>

      <v-col cols="6">
        <v-select
          v-model="form.agency_id"
          label="Sucursal"
          :items="options.agencies"
          :rules="[(v) => !!v || 'Valos Requerido']"
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
          :rules="[(v) => !!v || 'Valos Requerido']"
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
              :rules="[(v) => !!v || 'Valos Requerido']"
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
        <v-combobox
          v-model="form.user_id"
          :items="options.users"
          item-text="email"
          item-value="id"
          label="Email:"
          hint="Este correo sera asociado a un usuario"
          persistent-hint
          clearable
          filled
          outlined
          dense
        />
      </v-col>
      <!-- <v-col cols="12" v-permission="'superuser'">
        <v-autocomplete
          v-model="form.user_id"
          :items="options.users"
          item-text="email"
          item-value="id"
          label="Asociar Usuario:"
          clearable
          filled
          outlined
          dense
        />
      </v-col> -->
      <v-col cols="6">
        <v-switch v-model="form.is_matriz" label="Empleado Matriz"></v-switch>
      </v-col>
      <v-col cols="6">
        <v-switch v-model="form.active" label="Activo"></v-switch>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  name: "FormEmployeeTab2",
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
    // _this.loadEstates(() => {
    //   _this.estate_id = _this.Estate;
    // });
  },
  data() {
    return {
      menu2: false,
      activePicker: null,
      options: {
        agencies: [],
        departments: [],
        jobs: [],
        direct_boss: [],
        users: [],
      },
    };
  },
  methods: {
    save(date) {
      // this.$refs.menu.save(date);
    },
    loadOptions() {
      const _this = this;
      axios.get("/admin/employees/create").then((res) => {
        _this.options.agencies = res.data.data.agencies;
        _this.options.departments = res.data.data.departments;
        _this.options.jobs = res.data.data.jobs;
        _this.options.direct_boss = res.data.data.direct_boss;
        _this.options.users = res.data.data.users;
      });
    },
  },
  watch: {
    // menu(val) {
    //   val && setTimeout(() => (this.activePicker = "YEAR"));
    // },
    menu2(val) {
      val && setTimeout(() => (this.activePicker = "YEAR"));
    },
  },
};
</script>
<style></style>
