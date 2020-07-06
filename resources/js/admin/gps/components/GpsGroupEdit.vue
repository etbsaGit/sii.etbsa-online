<template>
  <div class="component-wrap">
    <v-card>
      <v-form v-model="valid" ref="gpsGroupFormEdit" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12>
              <div class="body-2 white--text">Gps Group Details</div>
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
      nameRules: [v => !!v || "Name is required"],
      description: "",
      descriptionRules: [v => !!v || "Description is required"]
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
        description: self.description
      };

      self.isLoading = true;

      axios
        .put("/admin/gps-groups/" + self.propGpsGroupId, payload)
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
        .get("/admin/gps-groups/" + self.propGpsGroupId)
        .then(function(response) {
          let Group = response.data.data;
          self.name = Group.name;
          self.description = Group.description;
          cb();
        });
    }
  }
};
</script>