<template>
  <v-card>
    <v-card-title>
      Editar Solicitud de Cargo
      <v-spacer />
      <v-btn class="mr-2">Autorizar</v-btn>
      <v-btn>Rechazar</v-btn>
    </v-card-title>
    <v-card-subtitle> Estatus Actual: {{ form.estatus }} </v-card-subtitle>
    <v-card-text>
      <VForm ref="form" lazy-validation @submit.prevent="save">
        <form-cargo-interno
          :form.sync="form"
          :errors="errors"
        ></form-cargo-interno>
        <VRow dense>
          <VCol cols="12" class="d-flex gap-4">
            <v-spacer />
            <VBtn type="submit"> Actualizar </VBtn>
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
  components: { FormCargoInterno },
  name: "CreateCargoInterno",
  props: {
    CargoInternoId: {
      // type: [Number | String],
      require: true,
    },
  },
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
        estatus: "",
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
    this.getEdit();
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
    async getEdit() {
      const { data } = await axios.get(
        `/admin/cargos-internos/${this.CargoInternoId}/edit`
      );
      this.form = { ...data.data.item };
    },
    async save() {
      const _this = this;
      if (!this.$refs.form.validate()) return;
      let payload = {
        ...this.form,
        amount: this.TotalAmount,
      };
      try {
        const { data } = await axios.put(
          `/admin/cargos-internos/${this.CargoInternoId}`,
          payload
        );
        // console.log("Success", data.message);
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