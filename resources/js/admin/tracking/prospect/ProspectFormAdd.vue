<template>
  <div class="component-wrap">
    <!-- form -->
    <v-card>
      <v-card-title>
        <v-icon>mdi-account-box-outline</v-icon> Registrar Nuevo Prospecto
      </v-card-title>
      <v-divider class="mb-2"></v-divider>
      <v-form v-model="valid" ref="prospectFormAdd" lazy-validation>
        <v-container grid-list-md>
          <v-layout row wrap>
            <v-flex xs12>
              <div class="body-2">Ingresar la informacion del Propecto</div>
            </v-flex>
            <v-flex xs12 md3>
              <v-switch
                v-model="is_moral"
                flat
                :label="`Persona ${is_moral ? 'Fisica' : 'Moral'}`"
                class="mx-auto"
              ></v-switch>
            </v-flex>
            <v-flex xs12 md9>
              <v-text-field
                label="Nombre a quien va dirigido:"
                v-model="full_name"
                :rules="[(v) => !!v || 'Nombre Requerido']"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md8 v-if="is_moral">
              <v-text-field
                label="Razon Social:"
                v-model="company"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4 lg4>
              <v-text-field v-model="rfc" label="RFC:" filled></v-text-field>
            </v-flex>
            <v-flex xs12 md4>
              <v-autocomplete
                v-model="estate_id"
                :items="options.estates"
                item-text="name"
                item-value="id"
                label="Estado:"
                filled
                :rules="[(v) => !!v || 'Estado Requerido']"
              ></v-autocomplete>
            </v-flex>
            <v-flex xs12 md4>
              <v-autocomplete
                :items="options.townships"
                v-model="township_id"
                label="Municipio"
                item-text="name"
                item-value="id"
                filled
                :rules="[(v) => !!v || 'Municipio Requerido']"
                outline
              ></v-autocomplete>
            </v-flex>

            <v-flex xs12 md4 lg4>
              <v-text-field
                label="Telefono:"
                v-model="phone"
                :rules="[(v) => !!v || 'Telefono Requerido']"
                filled
                counter="10"
              ></v-text-field>
            </v-flex>
            <v-flex xs12 md4 lg4>
              <v-text-field
                v-model="email"
                label="email:"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-text-field
                v-model="town"
                label="Nombre Racho/comunidad (optional):"
                placeholder="¿De donde nos visita?"
                filled
              ></v-text-field>
            </v-flex>
            <v-flex xs12>
              <v-btn
                block
                @click="save()"
                :loading="isLoading"
                :disabled="!valid || isLoading"
                color="primary"
                dark
                >Guardar</v-btn
              >
            </v-flex>
          </v-layout>
        </v-container>
      </v-form>
    </v-card>
    <!-- /form -->
  </div>
</template>

<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  mixins: [mixinEstates],
  data() {
    return {
      valid: false,
      isLoading: false,
      full_name: "",
      phone: "",
      email: "",
      rfc: "",
      town: "",
      company: null,
      is_moral: false,
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Prospectos", to: { name: "prospect.list" } },
      { label: "Registrar", name: "" },
    ]);
    this.loadEstates(() => {});
  },
  methods: {
    save() {
      const self = this;

      let payload = {
        full_name: self.full_name,
        phone: self.phone,
        email: self.email,
        rfc: self.rfc,
        township_id: self.township_id,
        is_moral: self.is_moral,
        company: self.company,
      };

      self.isLoading = true;

      axios
        .post("/admin/prospects", payload)
        .then(function (response) {
          self.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          // self.$eventBus.$emit("PERMISSION_ADDED");

          // reset
          self.isLoading = false;
          self.$router.push({ name: "prospect.list" });
        })
        .catch(function (error) {
          self.isLoading = false;
          self.$store.commit("hideLoader");

          if (error.response) {
            self.$store.commit("showSnackbar", {
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
  },
};
</script>
