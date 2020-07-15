<template>
  <div>
    <v-stepper v-model="e1" alt-labels>
      <v-stepper-header>
        <v-stepper-step
          :step="1"
          editable
          :complete="!!files['file_clientes_gps']"
        >
          Archivo Clientes
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step
          :step="2"
          editable
          :complete="!!files['file_gps_chips']"
        >
          Archivo Proveedor
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :step="3" editable :complete="!!files['file_gps']">
          Archivo Wialon
        </v-stepper-step>
        <v-divider></v-divider>
        <v-stepper-step :step="4">Cargar a BD</v-stepper-step>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content :step="1">
          <v-file-input
            v-model="files['file_clientes_gps']"
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
                  <th class="text-center">Cliente</th>
                  <th class="text-center">Razon Social</th>
                  <th class="text-center">RFC</th>
                  <th class="text-center">Nombre Gps</th>
                  <th class="text-center">Sucursal</th>
                  <th class="text-center">Departamento</th>
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
            v-model="files['file_gps_chips']"
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
                  <th class="text-center">Cuenta</th>
                  <th class="text-center">IMEI</th>
                  <th class="text-center">Fecha Activacion</th>
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
          <v-file-input
            v-model="files['file_gps']"
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
                  <th class="text-center">Nombre GPS</th>
                  <th class="text-center">Telefono</th>
                  <th class="text-center">Fecha Instalacion</th>
                </tr>
              </thead>
            </template>
          </v-simple-table>

          <v-btn color="primary" @click="nextStep(3)">
            Continuar <v-icon>mdi-chevron-right</v-icon>
          </v-btn>
        </v-stepper-content>

        <v-stepper-content :step="4">
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
          <v-btn color="primary" @click="nextStep(4)">
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
      steps: 4,
      files: {},
      accept:
        ".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    };
  },
  methods: {
    async nextStep(n) {
      const self = this;
      self.$store.commit("showLoader");
      if (n === self.steps) {
        console.log("LastStep excute upload");
        const result = await self.matchingChipsInGps();
        console.log("LastStep finish", result);
      } else if (n === 1) {
        console.log("Step1 excute upload");
        const result = await self.uploadFileClientes();
        console.log("Step1 finish", result);
        if (result.data == true) self.e1 = n + 1;
      } else if (n === 2) {
        console.log("Step2 excute upload");
        const result = await self.uploadFileChips();
        console.log("Step2 finish", result);
        if (result.data == true) self.e1 = n + 1;
      } else if (n === 3) {
        // success = await self.uploadFileChips();
        console.log("Step3 execute upload");
        const result = await self.uploadFileGps();
        console.log("Step3 finish", result);
        if (result.data == true) self.e1 = n + 1;
      }
      self.$store.commit("hideLoader");
    },
    uploadFileClientes() {
      const self = this;
      let formData = new FormData();
      formData.append("file_clientes_gps", self.files["file_clientes_gps"]);
      return axios
        .post("admin/clientes-gps/import", formData)
        .then(response => {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          return response.data;
        })
        .catch(error => {
          return error;
        })
        .finally(function() {
          self.$store.commit("hideLoader");
        });
    },
    uploadFileGps() {
      const self = this;
      let formData = new FormData();
      formData.append("file_gps", self.files["file_gps"]);
      return axios
        .post("admin/gps/import", formData)
        .then(response => {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          return response.data;
        })
        .catch(error => {
          return error;
        })
        .finally(function() {
          self.$store.commit("hideLoader");
        });
    },
    uploadFileChips() {
      const self = this;
      let formData = new FormData();
      formData.append("file_gps_chips", self.files["file_gps_chips"]);
      return axios
        .post("admin/gps-chips/import", formData)
        .then(response => {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          return response.data;
        })
        .finally(function() {
          self.$store.commit("hideLoader");
        });
    },
    matchingChipsInGps() {
      const self = this;

      return axios
        .post("admin/matching-chips-gps/import")
        .then(response => {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          return response.data;
        })
        .finally(function() {
          self.$store.commit("hideLoader");
        });
    }
  }
};
</script>