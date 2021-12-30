<template>
  <v-row dense>
    <v-col cols="12">
      <v-stepper v-model="e6" vertical flat>
        <v-stepper-step :complete="e6 > 1" step="1" editable>
          Puesto
          <small>Solicitante</small>
        </v-stepper-step>

        <v-stepper-content step="1">
          <v-form v-model="valid" ref="formStepOne" lazy-validation>
            <v-card class="mb-6 overline" flat>
              <v-container fluid>
                <v-row dense align="center">
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.puesto"
                      label="Puesto"
                      filled
                      dense
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.sucursal_solcitante"
                      :items="options.agencies"
                      item-text="title"
                      item-value="title"
                      label="Sucursal Solicitante"
                      filled
                      :rules="[(v) => !!v || 'Es Requerido']"
                      dense
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-select
                      v-model="form.area_solicitante"
                      :items="options.departments"
                      item-text="title"
                      item-value="title"
                      label="Area Solicitante:"
                      filled
                      clearable
                      dense
                    ></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-btn color="primary" @click="validStepOne()" :disabled="!valid">
              Continuar
            </v-btn>
            <v-btn text @click="$refs.formStepOne.reset()">
              Cancelar
            </v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-step :complete="e6 > 2" step="2">
          Vacante a Cubrir
          <small>Tipo de Vacante</small>
        </v-stepper-step>

        <v-stepper-content step="2">
          <v-form v-model="valid" ref="formStepTwo" lazy-validation>
            <v-card class="mb-6 px-2" flat>
              <v-row align="center" justify="center">
                <v-col cols="12" md="3">
                  <v-radio-group
                    v-model="form.tipo_vacante"
                    :column="!$vuetify.breakpoint.mobile"
                    class="d-flex flex-wrap text-wrap"
                    :rules="[(v) => !!v || 'valor requerido']"
                    dense
                  >
                    <v-radio label="Remplazo" value="remplazo"></v-radio>
                    <v-radio
                      label="Nueva Creacion"
                      value="nueva creacion"
                    ></v-radio>
                    <v-radio label="Temporal" value="temporal"></v-radio>
                    <v-radio label="Permanente" value="permanente"></v-radio>
                  </v-radio-group>
                </v-col>
                <v-col cols="12" md="6">
                  <div class="d-flex flex-wrap flex-column">
                    <v-textarea
                      v-model="form.motivo_vacante"
                      label="Motivo"
                      :rules="[(v) => !!v || 'valor requerido']"
                      filled
                      outlined
                      height="130"
                      class="mt-2"
                    ></v-textarea>
                    <v-text-field
                      v-model="form.especificacion_vacante"
                      v-show="
                        form.tipo_vacante == 'remplazo' ||
                        form.tipo_vacante == 'temporal'
                      "
                      :label="
                        form.tipo_vacante == 'remplazo'
                          ? 'A quien:'
                          : 'Especifique'
                      "
                    ></v-text-field>
                  </div>
                </v-col>
              </v-row>
            </v-card>
            <v-btn color="primary" @click="validStepTwo()" :disabled="!valid">
              Continuar
            </v-btn>
            <v-btn text @click="$refs.formStepTwo.reset()">
              Cancelar
            </v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-step :complete="e6 > 3" step="3">
          Generales de Puesto
        </v-stepper-step>

        <v-stepper-content step="3">
          <v-form v-model="valid" ref="formStepThree" lazy-validation>
            <v-card class="mb-12 overline" flat>
              <v-container fluid>
                <v-row dense align="end" justify="center">
                  <v-col cols="12" md="3">
                    <div>Sexo</div>
                    <div
                      class="d-flex row--dense justify-space-around flex-wrap"
                    >
                      <v-checkbox
                        v-model="form.sexo_puesto"
                        label="Masculino"
                        value="M"
                        color="primary"
                      ></v-checkbox>
                      <v-checkbox
                        v-model="form.sexo_puesto"
                        label="Femenino"
                        value="F"
                        color="primary"
                      ></v-checkbox>
                    </div>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-select
                      v-model="form.escolaridad_puesto"
                      label="Escolaridad"
                      :items="[
                        'Primaria',
                        'Secundaria',
                        'Prepa',
                        'Licenciatura',
                      ]"
                      :rules="[(v) => !!v || 'valor requerido']"
                    >
                    </v-select>
                  </v-col>
                  <v-col cols="12" md="3">
                    <div class="pb-6">
                      Rango de Edad
                    </div>
                    <div class="d-flex align-center">
                      <v-range-slider
                        v-model="form.rango_edad_puesto"
                        :max="70"
                        :min="15"
                        :thumb-size="24"
                        class="align-center"
                        thumb-label="always"
                        thumb-color="green"
                        hide-details
                        :rules="[(v) => !!v || 'valor requerido']"
                      >
                      </v-range-slider>
                    </div>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-text-field
                      v-model="form.idiomas_puesto"
                      label="Idiomas"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3">
                    <v-switch
                      v-model="form.manejo_puesto"
                      inset
                      color="green"
                      class="ml-4"
                      :label="`Manejo de Equipo: ${
                        form.manejo_puesto ? 'SI' : 'NO'
                      }`"
                    ></v-switch>
                  </v-col>
                  <v-col cols="12" md="3" class="text-center">
                    <v-switch
                      v-model="form.comision_puesto"
                      inset
                      color="green"
                      class="ml-4"
                      :label="`Comision: ${form.comision_puesto ? 'SI' : 'NO'}`"
                    ></v-switch>
                  </v-col>

                  <v-col cols="12" md="3">
                    <v-text-field
                      v-model="form.sueldo_puesto"
                      label="Sueldo Mensual Inicial"
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="3">
                    <v-textarea
                      v-model="form.habilidades_puesto"
                      label="Habilidades"
                      height="100"
                      filled
                      hide-details
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-btn color="primary" @click="validStepThree()" :disabled="!valid">
              Continuar
            </v-btn>
            <!-- <v-btn text @click="$refs.formStepThree.reset()">
              Cancelar
            </v-btn> -->
          </v-form>
        </v-stepper-content>

        <v-stepper-step :complete="e6 > 4" step="4">
          Competencias
        </v-stepper-step>

        <v-stepper-content step="4">
          <v-form v-model="valid" ref="formStepFour" lazy-validation>
            <v-card class="mb-12" flat>
              <v-list dense flat>
                <v-list-item>
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
                  <v-list-item :key="item.title">
                    <template v-slot:default>
                      <v-list-item-content>
                        <v-list-item-title
                          v-text="item.name"
                          class="text-wrap"
                        ></v-list-item-title>
                      </v-list-item-content>

                      <v-list-item-action>
                        <v-radio-group v-model="item.value" row>
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
            </v-card>
            <v-btn color="primary" @click="validStepFour()" :disabled="!valid">
              Continuar
            </v-btn>
            <v-btn text @click="$refs.formStepFour.reset()">
              Cancelar
            </v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-step :complete="e6 > 5" step="5">
          Experiencia y/o conocimientos
          <small>Especificar Area y Tiempo</small>
        </v-stepper-step>

        <v-stepper-content step="5">
          <v-form v-model="valid" ref="formStepFive" lazy-validation>
            <v-card class="mb-12" flat>
              <v-container fluid>
                <v-row dense align="center">
                  <v-col cols="12" md="6">
                    <v-text-field
                      v-model="form.areas_experiencia"
                      label="Areas:"
                      outlined
                      filled
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-text-field>
                    <v-text-field
                      v-model="form.tiempo_experiencia"
                      label="Tiempo de Experiencia:"
                      placeholder="Años y/o Meses"
                      outlined
                      filled
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <v-textarea
                      v-model="form.actividades_experiencia"
                      label="Actividades Basicas a Desempeñar"
                      outlined
                      filled
                      :rules="[(v) => !!v || 'valor requerido']"
                    ></v-textarea>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-btn color="primary" @click="validStepFive()" :disabled="!valid">
              Continuar
            </v-btn>
            <v-btn text @click="$refs.formStepFive.reset()">
              Cancelar
            </v-btn>
          </v-form>
        </v-stepper-content>

        <v-stepper-step :complete="e6 > 6" step="6">
          Equipo a Proporcionar
        </v-stepper-step>

        <v-stepper-content step="6">
          <v-form v-model="valid" ref="formStepSix" lazy-validation>
            <v-card class="mb-12" flat>
              <v-container fluid>
                <v-row>
                  <v-col cols="12">
                    <v-chip-group
                      v-model="form.equipo_proporcionar"
                      column
                      multiple
                      active-class="green--text text--accent-4"
                    >
                      <v-chip
                        v-for="item in itemsEquipo"
                        :key="item"
                        filter
                        :value="item"
                        outlined
                      >
                        {{ item }}
                      </v-chip>
                    </v-chip-group>
                  </v-col>
                </v-row>
              </v-container>
            </v-card>
            <v-btn color="primary" @click="validStepSix()" :disabled="!valid">
              Continuar
            </v-btn>
            <v-btn text @click="$refs.formStepSix.reset()">
              Cancelar
            </v-btn>
          </v-form>
        </v-stepper-content>
      </v-stepper></v-col
    >
  </v-row>
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
  mounted() {
    this.loadOptions(() => {});
  },
  data() {
    return {
      e6: 1,
      selected: [],
      itemsEquipo: [
        "ZAPATOS",
        "PLAYERA",
        "PANTALON",
        "FAJA",
        "LENTES",
        "MANDIL",
        "GUANTES",
        "OVEROL",
        "VEHICULO",
        "CELULAR",
        "ALQUILER DE VIVIENDA",
        "COMPUTADORA",
        "IMPRESORA",
        "HERRAMIENTA",
        "PAPELERIA",
        "OTRO",
      ],
      options: {
        agencies: [],
        departments: [],
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
    competencies: {
      get: function () {
        return (this.form.competencias = [
          {
            name: "ADAPTACIÓN AL CAMBIO",
            value: 0,
          },
          {
            name: "AUTOCONFIANZA",
            value: 0,
          },
          {
            name: "AUTODOMINIO ",
            value: 0,
          },
          {
            name: "CAPACIDAD DE PLANEACIÓN ESTRATÉGICA Y ORGANIZACIONAL",
            value: 0,
          },
          {
            name: "COMPROMISO E INVOLUCRAMIENTO CON LA MARCA",
            value: 0,
          },
          {
            name: "CONOCIMIENTO ORG.",
            value: 0,
          },
          {
            name: "CONTROL Y SEGUIMIENTO ",
            value: 0,
          },
          {
            name: "DESARROLLO DE RELACIONES INTERPERSONALES",
            value: 0,
          },
          {
            name: "INICIATIVA",
            value: 0,
          },
          {
            name: "LIDERAZGO",
            value: 0,
          },
          {
            name: "ORIENTACIÓN A RESULTADOS",
            value: 0,
          },
          {
            name: "ORIENTACIÓN A LA CALIDAD",
            value: 0,
          },
          {
            name: "RESPONSABILIDAD",
            value: 0,
          },
          {
            name: "ORIENTACIÓN AL CLIENTE Y ACTITUD DE SERVICIO",
            value: 0,
          },
        ]);
      },
    },
  },
  methods: {
    validStepOne() {
      this.$refs.formStepOne.validate() ? (this.e6 = 2) : false;
    },
    validStepTwo() {
      this.$refs.formStepTwo.validate() ? (this.e6 = 3) : false;
    },
    validStepThree() {
      this.$refs.formStepThree.validate() ? (this.e6 = 4) : false;
    },
    validStepFour() {
      this.$refs.formStepFour.validate() ? (this.e6 = 5) : false;
    },
    validStepFive() {
      this.$refs.formStepFive.validate() ? (this.e6 = 6) : false;
    },
    validStepSix() {
      this.$refs.formStepSix.validate() ? (this.e6 = 7) : false;
    },
    async loadOptions(cb) {
      const _this = this;
      await axios
        .get("/admin/purchase-order/resources/options")
        .then(function (response) {
          let { agencies, departments } = response.data.data;
          _this.options.agencies = agencies;
          _this.options.departments = departments;
        });
    },
  },
};
</script>
<style lang=""></style>
