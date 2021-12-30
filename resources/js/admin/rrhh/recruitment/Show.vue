<template>
  <v-card flat>
    <v-row dense no-gutters>
      <v-col cols="12" md="6">
        <v-row class="text-left" tag="v-card-text">
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Puesto:
          </v-col>
          <v-col>{{ form.puesto }}</v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Sucursal Solicitante:
          </v-col>
          <v-col>{{ form.sucursal_solcitante }} </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Area Solicitante:
          </v-col>
          <v-col>{{ form.area_solicitante }}</v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Tipo Vacante:
          </v-col>
          <v-col>{{ form.tipo_vacante }}</v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Motivo Vacante:
          </v-col>
          <v-col>
            {{ form.motivo_vacante }}
          </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Escolaridad:
          </v-col>
          <v-col>
            {{ form.escolaridad_puesto }}
          </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Sexo:
          </v-col>
          <v-col class="py-0">
            <v-chip-group active-class="primary--text" column>
              <v-chip
                v-for="sexo in form.sexo_puesto"
                :key="sexo"
                small
                class="caption text-uppercase"
              >
                {{ sexo == "M" ? "Masculino" : "Femenino" }}
              </v-chip>
            </v-chip-group>
          </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Rango de Edad:
          </v-col>
          <v-col>
            <span v-if="form.rango_edad_puesto">
              {{ form.rango_edad_puesto.join(" - ") }} años
            </span>
          </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Equipo a Proporcionar:
          </v-col>
          <v-col class="py-0">
            <v-chip-group active-class="primary--text" column>
              <v-chip
                v-for="equipo in form.equipo_proporcionar"
                :key="equipo"
                small
                class="caption text-uppercase"
              >
                {{ equipo }}
              </v-chip>
            </v-chip-group>
          </v-col>
          <v-col class="text-right mr-4" tag="strong" cols="6">
            Experiencia y Conocimientos:
          </v-col>
          <v-col>
            Tiempo Experiencia: {{ form.tiempo_experiencia }}<br />
            Habilidades: {{ form.habilidades_puesto }}<br />
            Idiomas: {{ form.idiomas_puesto }}<br />
            Actividades: {{ form.actividades_experiencia }}
          </v-col>
        </v-row>
        <!-- </v-card-text> -->
      </v-col>
      <v-col cols="12" md="6">
        <v-container fluid>
          <app-widget
            icon-title="mdi-information-outline"
            title="Competencias"
            padding-hide
          >
            <v-list dense flat slot="widget-content">
              <v-list-item dense>
                <v-list-item-content>
                  <v-list-item-title v-text="''"></v-list-item-title>
                </v-list-item-content>

                <v-list-item-action>
                  <div class="d-flex flex-row">
                    <div class="d-flex mr-4">
                      <v-icon color="red">mdi-information-outline</v-icon>
                    </div>
                    <div class="d-flex mr-4">
                      <v-icon color="blue">mdi-information-outline</v-icon>
                    </div>
                    <div class="d-flex mr-4">
                      <v-icon color="green">mdi-information-outline</v-icon>
                    </div>
                  </div>
                </v-list-item-action>
              </v-list-item>
              <v-divider></v-divider>
              <template v-for="(item, index) in competencies">
                <v-list-item :key="item.title" dense>
                  <template v-slot:default>
                    <v-list-item-content>
                      <v-list-item-title
                        v-text="item.name"
                        class="text-wrap"
                      ></v-list-item-title>
                    </v-list-item-content>

                    <v-list-item-action>
                      <v-radio-group v-model="item.value" row readonly>
                        <v-radio :value="-1"></v-radio>
                        <v-radio :value="0"></v-radio>
                        <v-radio :value="1"></v-radio>
                      </v-radio-group>
                    </v-list-item-action>
                  </template>
                </v-list-item>

                <v-divider
                  v-if="index < competencies.length - 1"
                  :key="index"
                ></v-divider>
              </template>
            </v-list>
          </app-widget>
        </v-container>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
import AppWidget from "@admin/components/AppWidget.vue";
export default {
  components: { AppWidget },
  props: {
    recruitmentId: {
      require: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      valid: true,
      isLoading: false,
      form: {},
      options: { users: [], agencies: [] },
    };
  },
  mounted() {
    const _this = this;
    _this.loadRecruitment(() => {});
    _this.$eventBus.$on(["Recruitment_REFRESH"], () => {
      _this.loadRecruitment(() => {});
    });
  },
  computed: {
    minHeight() {
      const height = "100vh";
      return `calc(${height} - ${this.$vuetify.application.bottom}px)`;
    },
    competencies: {
      get: function () {
        return this.form.competencias;
      },
    },
  },
  methods: {
    // async update() {
    //   if (!this.$refs.RecruitmentForm.$refs.form.validate()) return;
    //   const _this = this;
    //   await axios
    //     .put(`/admin/recruitment/${_this.recruitmentId}`, _this.form)
    //     .then(function (response) {
    //       _this.$store.commit("showSnackbar", {
    //         message: response.data.message,
    //         color: "success",
    //         duration: 3000,
    //       });
    //       _this.$eventBus.$emit("Recruitment_REFRESH");
    //       _this.$router.push({ name: "Recruitment.list" });
    //     })
    //     .catch(function (error) {
    //       if (error.response) {
    //         _this.$store.commit("showSnackbar", {
    //           message: error.response.data.message,
    //           color: "error",
    //           duration: 3000,
    //         });
    //       } else if (error.request) {
    //         console.log(error.request);
    //       } else {
    //         console.log("Error", error.message);
    //       }
    //     });
    // },
    async loadRecruitment(cb) {
      const _this = this;

      await axios
        .get(`/admin/recruitment/${_this.recruitmentId}`)
        .then(function (response) {
          let Recruitment = response.data.data;
          _this.form = { ...Recruitment };
          (cb || Function)();
        });
    },
  },
};
</script>
