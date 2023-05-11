<template>
  <v-card>
    <v-card-title> Form solicitud de Cargo Interno </v-card-title>
    <v-card-text>
      <VForm ref="form" lazy-validation @submit.prevent="save">
        <!-- <v-row>
          <v-col cols="12" md="7">
            <v-subheader> Datos solicitud</v-subheader>
            <VRow dense>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.num_economico"
                  label="No. Economico"
                  :error-messages="errors.num_economico"
                  outlined
                  dense
                  placeholder="Numero Economico"
                />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.num_cliente"
                  label="No. cliente"
                  :error-messages="errors.num_cliente"
                  outlined
                  dense
                  placeholder="Numero Cliente"
                />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.nip"
                  label="NIP"
                  :error-messages="errors.nip"
                  outlined
                  dense
                  placeholder="(Ultimos 5 Digitos)"
                />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.concepto_cargo"
                  label="Concepto de Cargo"
                  :error-messages="errors.concepto_cargo"
                  outlined
                  dense
                  placeholder="nombre"
                />
              </VCol>
            </VRow>
            <v-divider />
            <v-subheader> Datos de la Orden de Trabajo (OT)</v-subheader>
            <VRow dense>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.ot"
                  label="Orden de Trabajo"
                  outlined
                  :error-messages="errors.ot"
                  dense
                  placeholder="Numero de OT"
                />
              </VCol>

              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.mano_obra"
                  label="Mano de Obra"
                  outlined
                  :error-messages="errors.mano_obra"
                  dense
                  placeholder="$ 0.00"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.refacciones"
                  label="Refacciones"
                  outlined
                  :error-messages="errors.refacciones"
                  dense
                  placeholder="$ 0.00"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.kilometraje"
                  label="Kilometraje"
                  outlined
                  :error-messages="errors.kilometraje"
                  dense
                  placeholder="0"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.foraneo"
                  label="Foraneo"
                  outlined
                  :error-messages="errors.foraneo"
                  dense
                  placeholder="$ 0.00"
                />
              </VCol>
              <VCol cols="12" md="6">
                <VTextField
                  v-model="form.amount"
                  label="Valor total OT"
                  outlined
                  :error-messages="errors.amount"
                  dense
                  placeholder="$ 0.00"
                />
              </VCol>
            </VRow>
            <VRow>
              <v-col cols="12">
                <v-textarea
                  v-model="form.description"
                  label="Decripcion"
                  outlined
                  :error-messages="errors.description"
                  dense
                ></v-textarea>
              </v-col>
            </VRow>
          </v-col>
          <v-col cols="12" md="5">
            <v-card>
              <v-card-title>
                Cargo a Sucursal(es)
                <v-spacer />
                <v-btn icon @click="addCargoSucursal">
                  <v-icon color="primary">mdi-plus-box-multiple</v-icon>
                </v-btn>
              </v-card-title>
              <v-row tag="v-card-text">
                <div
                  class="d-flex flex-row align-center"
                  v-for="(item, index) in form.cargos_sucursal"
                  :key="`sucursal-${index}`"
                >
                  <v-select
                    v-model="form.cargos_sucursal[index].sucursal_id"
                    label="Sucursal"
                    class="ma-2"
                    :items="options.agencies"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    item-value="id"
                    item-text="title"
                    outlined
                    dense
                  ></v-select>
                  <v-select
                    v-model="form.cargos_sucursal[index].department_id"
                    label="Departamento"
                    class="ma-2"
                    :items="options.departments"
                    :rules="[(v) => !!v || 'Es Requerido']"
                    item-value="id"
                    item-text="title"
                    outlined
                    dense
                  ></v-select>
                  <v-text-field
                    v-model="form.cargos_sucursal[index].percent"
                    label="Porcentaje"
                    class="ma-2"
                    suffix="%"
                    outlined
                    dense
                  ></v-text-field>

                  <v-btn icon @click="deleteCargoSucusal(index)">
                    <v-icon color="red">mdi-trash-can</v-icon>
                  </v-btn>
                </div>
              </v-row>
            </v-card>
          </v-col>
        </v-row> -->

        <form-cargo-interno
          :form.sync="form"
          :errors="errors"
        ></form-cargo-interno>
        <VRow dense>
          <VCol cols="12" class="d-flex gap-4">
            <v-spacer />
            <VBtn type="submit"> Enviar Solicitud </VBtn>
            <!-- <VBtn type="reset" color="secondary" variant="tonal"> Reset </VBtn> -->
          </VCol>
        </VRow>
      </VForm>
    </v-card-text>
  </v-card>
</template>

<script>
import FormCargoInterno from "./components/FormCargoInterno.vue";
export default {
  middleware({ $gates, redirect }) {
    // If the user is not an admin
    if (!$gates.hasRole("Super User")) {
      return redirect("/login");
    }
  },
  components: { FormCargoInterno },
  name: "CreateCargoInterno",
  data() {
    return {
      valid: true,
      errors: {},
      form: {
        title: null,
        description: null,
        num_economico: null,
        num_cliente: null,
        nip: null,
        concepto_cargo: null,
        ot: null,
        mano_obra: null,
        refacciones: null,
        kilometraje: null,
        foraneo: null,
        amount: null,
        cargos_sucursal: [
          {
            sucursal_id: null,
            department_id: null,
          },
        ],
      },
    };
  },
  mounted() {
    this.$store.commit("setBreadcrumbs", [
      { label: "Cargos Internos", to: { name: "cargos_internos.index" } },
      { label: "Nueva solicitud" },
    ]);
  },

  computed: {
    TotalAmount: {
      get() {
        return (
          this.form.mano_obra +
          this.form.refacciones +
          this.form.kilometraje +
          this.form.foraneo
        );
      },
      set(val) {},
    },
  },
  methods: {
    async save() {
      const _this = this;
      if (!this.$refs.form.validate()) return;
      let payload = {
        ...this.form,
        amount: this.TotalAmount,
      };
      try {
        const { data } = await axios.post("/admin/cargos-internos", payload);
        // console.log("Success", data);
        _this.$store.commit("showSnackbar", {
          message: data.message,
          color: "success",
          duration: 3000,
        });
      } catch (err) {
        if (err.response) {
          _this.errors = err.response.data.errors;
        } else {
          console.log(err);
        }
      }
    },
  },
};
</script>

<style>
</style>