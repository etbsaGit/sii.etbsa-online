<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsGroupFormAdd" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12>
              <div class="title">Grupo GPS Detalle</div>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                label="Group Name"
                v-model="name"
                :rules="nameRules"
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-textarea
                label="Group Description"
                v-model="description"
                :rules="descriptionRules"
              ></v-textarea>
            </v-flex>
            <v-flex xs12>
              <v-btn block @click="save()" :disabled="!valid" color="primary"
                >Guardar</v-btn
              >
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      valid: false,
      isLoading: false,
      name: "",
      nameRules: [v => !!v || "Name is required"],
      agency: "CELAYA",
      agencyRules: [v => !!v || "Agencia is required"],
      department: "SERVICIO",
      // departmentRules: [v => !!v || "departmento is required"],
      description: "",
      descriptionRules: [v => !!v || "Description is required"]
    };
  },
  mounted() {
    const self = this;
    self.$refs.gpsGroupFormAdd.reset();
  },
  methods: {
    save() {
      const self = this;
      if (self.$refs.gpsGroupFormAdd.validate()) {
        let payload = {
          name: self.name,
          agency: self.agency,
          department: self.department,
          description: self.description
        };

        self.isLoading = true;

        axios
          .post("/admin/gps-groups", payload)
          .then(function(response) {
            self.$store.commit("showSnackbar", {
              message: response.data.message,
              color: "success",
              duration: 3000
            });

            self.isLoading = false;
            self.$eventBus.$emit("GPS_GROUP_ADDED");

            // reset
            self.$refs.gpsGroupFormAdd.reset();
          })
          .catch(function(error) {
            self.isLoading = false;
            if (error.response) {
              self.$store.commit("showSnackbar", {
                message: error.response.data.message,
                color: "error",
                duration: 3000
              });
            } else if (error.request) {
              console.log(error.request);
            } else {
              console.log("Error", error.message);
            }
          });
      }
    }
  }
};
</script>