<template>
  <v-container fluid>
    <v-card flat>
      <v-toolbar>
        <v-toolbar-title>
          <v-icon>mdi-account-plus</v-icon> Solicitar Personal
        </v-toolbar-title>
        <v-spacer> </v-spacer>
      </v-toolbar>
      <v-divider class="mb-3"></v-divider>
      <v-card-text class="pa-0">
        <v-form ref="form" v-model="valid" lazy-validation>
          <form-step ref="RecruitmentForm" v-model="valid" :form.sync="form">
          </form-step>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn
          :disabled="!valid"
          color="success"
          class="mr-4"
          @click="save"
          block
        >
          Guardar
          <v-icon right>mdi-check</v-icon>
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script>
import FormStep from "./FormStep.vue";
export default {
  components: { FormStep },

  data() {
    return {
      valid: true,
      form: {
        puesto: null,
        sucursal_solcitante: null,
        area_solicitante: null,
        tipo_vacante: "nueva_creacion",
        motivo_vacante: null,
        especificacion_vacante: null,
        sexo_puesto: ["M"],
        escolaridad_puesto: null,
        rango_edad_puesto: [18, 35],
        idiomas_puesto: null,
        manejo_puesto: false,
        comision_puesto: false,
        sueldo_puesto: null,
        habilidades_puesto: null,
        competencias: [],
        areas_experiencia: null,
        tiempo_experiencia: null,
        actividades_experiencia: null,
        equipo_proporcionar: [],
        // created_by: null,
        // estatus_id: null,
        // fecha_limite: null,
        // fecha_cubrio_vacante: null,
      },
      options: {},
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Requisicion de Personal", to: { name: "rrhh.employees.list" } },
      { label: "Crear" },
    ]);
  },
  methods: {
    async save() {
      const _this = this;
      await axios
        .post("/admin/recruitment", _this.form)
        .then((response) => {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        });
    },
    loadResources() {},
  },
};
</script>
