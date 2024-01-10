<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row dense class="text-uppercase">
      <!-- <v-col cols="12">
        <div class="d-flex flex-row align-center mb-4">
          <v-avatar class="profile" color="grey" size="58">
            <v-img :src="PhotoPreview"></v-img>
          </v-avatar>
          <v-file-input
            v-model="form.photo"
            placeholder="Seleccionar imagen"
            persistent-placeholder
            label="Foto (optional)"
            outlined
            dense
            hide-details
          >
          </v-file-input>
        </div>
      </v-col> -->
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.name"
          label="Primer Nombre"
          hint="es requerido"
          :rules="[(v) => !!v || 'Valos Requerido']"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.second_name"
          label="Segundo Nombre (optional)"
          hint="es requerido"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.last_name"
          label="Apellido Paterno"
          hint="es requerido"
          :rules="[(v) => !!v || 'Valos Requerido']"
          outlined
          dense
        >
        </v-text-field>
      </v-col>
      <v-col cols="12" md="6">
        <v-text-field
          v-model="form.second_last_name"
          label="Apellido Materno"
          hint="es requerido"
          :rules="[(v) => !!v || 'Valos Requerido']"
          outlined
          dense
        >
        </v-text-field>
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
              :rules="[(v) => !!v || 'Valos Requerido']"
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
          label="Telefono Personal"
          hint="es requerido"
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field
          v-model="form.phone_company"
          label="Telefono Empresa"
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
    PhotoPreview() {
      const _this = this;
      return _this.form.photo ? URL.createObjectURL(_this.form.photo) : "";
    },
  },
  mounted() {
    const _this = this;
    // _this.loadOptions();
    _this.loadEstates(() => {
      _this.estate_id = _this.Estate;
    });
  },
  data() {
    return {
      PhotoInput: null,
      menu: false,
      menu2: false,
      activePicker: null,
      // options: {
      //   agencies: [],
      //   departments: [],
      //   direct_boss: [],
      //   users: [],
      // },
    };
  },
  methods: {
    save(date) {
      this.$refs.menu.save(date);
    },
    // loadOptions() {
    //   const _this = this;
    //   axios.get("/admin/employees/create").then((res) => {
    //     _this.options.agencies = res.data.data.agencies;
    //     _this.options.departments = res.data.data.departments;
    //     _this.options.direct_boss = res.data.data.direct_boss;
    //     _this.options.users = res.data.data.users;
    //   });
    // },
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
