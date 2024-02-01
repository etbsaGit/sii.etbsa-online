<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsGroupFormEdit" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12>
              <div class="body-2 white--text">Grupo GPS Detalle</div>
            </v-flex>
            <v-flex xs9>
              <v-text-field
                v-model="name"
                label="Nombre Grupo"
                :rules="rules"
              ></v-text-field>
            </v-flex>
             <v-flex xs3>
              <v-text-field
                v-model="phone"
                label="Telefono Contacto"
                counter="10"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md6>
              <v-select
                v-model="agency"
                :items="options.agencies"
                label="Sucursal"
                :menu-props="{ offsetY: true }"
                item-text="name"
                item-value="name"
                clearable
              ></v-select>
            </v-flex>
            <v-flex xs12 md6>
              <v-select
                v-model="department"
                :items="options.departments"
                label="Departamento"
                :menu-props="{ offsetY: true }"
                item-text="name"
                item-value="name"
                clearable
              ></v-select>
            </v-flex>
            <v-flex xs12>
              <v-textarea
                v-model="description"
                label="Descripcion"
                outlined
              ></v-textarea>
            </v-flex>
            <v-flex xs12>
              <v-btn block @click="save()" :disabled="!valid" color="primary"
                >Modificar</v-btn
              >
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
  </div>
</template>

<script>
import optionAgencies from "~/api/agencies.json";
import optionDepartments from "~/api/departments.json";
export default {
  props: {
    propGpsGroupId: {
      required: true
    }
  },
  data() {
    return {
      valid: false,
      isLoading: false,
      name: "",
      phone: "",
      agency: "",
      department: "",
      description: "",
      rules: [v => !!v || "Name is required"],
      options: {
        agencies: optionAgencies,
        departments: optionDepartments
      }
    };
  },
  mounted() {
    const self = this;
    self.loadGpsGroup(() => {});
  },
  methods: {
    save() {
      const self = this;

      let payload = {
        name: self.name,
        phone: self.phone,
        agency: self.agency ? self.agency : " ",
        department: self.department ? self.department : " ",
        description: self.description
      };

      self.isLoading = true;

      axios
        .put("/admin/gpsCustomers/" + self.propGpsGroupId, payload)
        .then(function(response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000
          });

          self.$eventBus.$emit("GPS_GROUP_UPDATED");
        })
        .catch(function(error) {
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
        })
        .finally(function() {
          self.isLoading = false;
        });
    },
    loadGpsGroup(cb) {
      const self = this;

      axios
        .get("/admin/gpsCustomers/" + self.propGpsGroupId)
        .then(function(response) {
          let Group = response.data.data;
          self.name = Group.name;
          self.phone = Group.phone;
          self.agency = Group.agency;
          self.department = Group.department;
          self.description = Group.description;
          cb();
        });
    }
  }
};
</script>