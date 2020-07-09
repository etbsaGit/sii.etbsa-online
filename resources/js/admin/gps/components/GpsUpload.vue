<template>
  <div>
    <v-stepper v-model="e1" alt-labels>
      <v-stepper-header>
        <v-stepper-step :step="1" editable :complete="!!files['file_wialon']">
          Archivo Wialon
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :step="2" editable :complete="!!files['file_supplier']">
          Archivo Proveedor
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :step="3">Cargar a BD</v-stepper-step>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content :step="1">
          <v-file-input
            v-model="files['file_wialon']"
            label="Archivo Wialon"
            placeholder="Selecciona tu Archivo"
            outlined
            class="pa-2"
            show-size
            chips
            counter
            :accept="accept"
          ></v-file-input>
          <div class="overline">
            Muestra de Encabezados en el Excel iniciando en (A1)
          </div>
          <v-simple-table class="mb-2" dense fixed-header dark>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-center">Nombre</th>
                  <th class="text-center">Grupo</th>
                  <th class="text-center">Sucursal</th>
                  <th class="text-center">Departamento</th>
                  <th class="text-center">Creado</th>
                </tr>
              </thead>
            </template>
          </v-simple-table>

          <v-btn color="primary" @click="nextStep(1)">
            Continuar <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-stepper-content>

        <v-stepper-content :step="2">
          <v-file-input
            v-model="files['file_supplier']"
            label="Archivo Proveedor"
            placeholder="Selecciona tu Archivo"
            outlined
            class="pa-2"
            show-size
            chips
            counter
            :accept="accept"
          ></v-file-input>
          <div class="overline">
            Muestra de Encabezados en el Excel iniciando en (A1)
          </div>
          <v-simple-table class="mb-2" dense fixed-header dark>
            <template v-slot:default>
              <thead>
                <tr>
                  <th class="text-center">SIM</th>
                  <th class="text-center">IMEI</th>
                  <th class="text-center">Fecha</th>
                </tr>
              </thead>
            </template>
          </v-simple-table>

          <v-btn color="primary" @click="nextStep(2)">
            Continuar
            <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-stepper-content>

        <v-stepper-content :step="3">
          <div class="text-h5">Confirmacion para carga de archivos</div>
          <v-list two-line subheader>
            <v-subheader inset>Archivos</v-subheader>
            <v-list-item v-for="(item, i) in files" :key="i">
              <template v-if="item.name">
                <v-list-item-avatar>
                  <v-icon>mdi-file-excel</v-icon>
                </v-list-item-avatar>
                <v-list-item-content>
                  <v-list-item-title v-text="item.name"></v-list-item-title>
                  <v-list-item-subtitle>
                    {{ $appFormatters.formatByteToMB(item.size) }}MB
                  </v-list-item-subtitle>
                </v-list-item-content>
              </template>
            </v-list-item>
            <v-divider inset></v-divider>
          </v-list>
          <v-btn color="primary" @click="nextStep(3)">
            Continue
          </v-btn>
          <v-btn text>Cancel</v-btn>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper>
  </div>
</template>

<script>
export default {
  data() {
    return {
      e1: 1,
      steps: 3,
      files: {},
      accept:
        ".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    };
  },
  methods: {
    nextStep(n) {
      const self = this;
      if (n === this.steps) {
        // last Step
        let formData = new FormData();
        formData.append("file_wialon", self.files["file_wialon"]);
        formData.append("file_supplier", self.files["file_supplier"]);
        self.$store.commit("showLoader");
        axios
          .post("admin/gps/import", formData)
          .then(response => {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000
            });
            self.$eventBus.$emit("UPLOAD_COMPLETE");
            self.$eventBus.$emit("GPS_UPDATED");
            self.$eventBus.$emit("GPS_GROUP_UPDATED");
          })
          .finally(function() {
            self.$store.commit("hideLoader");
          });
      } else {
        self.e1 = n + 1;
      }
    }
  }
};
</script>