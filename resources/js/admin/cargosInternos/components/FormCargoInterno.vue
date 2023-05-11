<template>
  <v-row>
    <v-col cols="12" md="6">
      <v-subheader> Datos solicitud</v-subheader>
      <VRow dense>
        <!-- 👉 First Name -->
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

        <!-- 👉 Last Name -->
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

        <!-- 👉 Email -->
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

        <!-- 👉 City -->
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
        <!-- 👉 Country -->
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

        <!-- 👉 Company -->
        <VCol cols="12" md="6">
          <VTextField
            v-model.number="form.mano_obra"
            label="Mano de Obra"
            outlined
            :error-messages="errors.mano_obra"
            dense
            placeholder="$ 0.00"
          />
        </VCol>
        <VCol cols="12" md="6">
          <VTextField
            v-model.number="form.refacciones"
            label="Refacciones"
            outlined
            :error-messages="errors.refacciones"
            dense
            placeholder="$ 0.00"
          />
        </VCol>
        <VCol cols="12" md="6">
          <VTextField
            v-model.number="form.kilometraje"
            label="Kilometraje"
            outlined
            :error-messages="errors.kilometraje"
            dense
            placeholder="0"
          />
        </VCol>
        <VCol cols="12" md="6">
          <VTextField
            v-model.number="form.foraneo"
            label="Foraneo"
            outlined
            :error-messages="errors.foraneo"
            dense
            placeholder="$ 0.00"
          />
        </VCol>
        <VCol cols="12" md="6">
          <v-card flat class="text-center text-h6">
            Monto del Cargo:
            {{ TotalAmount | currency }}
          </v-card>
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
    <v-col cols="12" md="6">
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
            class="d-flex flex-row flex-wrapF align-center"
            v-for="(item, index) in form.cargos_sucursal"
            :key="`sucursal-${index}`"
          >
            <v-select
              v-model="form.cargos_sucursal[index].sucursal_id"
              label="Sucursal"
              style="max-width: 40%"
              class="ma-2 flex-shrink-1"
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
              style="max-width: 40%"
              class="ma-2 flex-shrink-1"
              :items="options.departments"
              :rules="[(v) => !!v || 'Es Requerido']"
              item-value="id"
              item-text="title"
              outlined
              dense
            ></v-select>
            <v-text-field
              v-model.number="form.cargos_sucursal[index].percent"
              label="Porcentaje"
              :rules="[
                (v) => !!v || 'Es Requerido',
                (v) => TotalPercent == 100 || 'el total no es igual a 100',
              ]"
              style="max-width: 100px"
              class="ma-2 flex-grow-0 flex-shrink-0"
              suffix="%"
              outlined
              dense
            ></v-text-field>

            <v-btn
              icon
              @click="deleteCargoSucusal(index)"
              style="max-width: 75px"
            >
              <v-icon color="red">mdi-trash-can</v-icon>
            </v-btn>
          </div>
        </v-row>
        <v-card-actions class="text-h5">
          <v-spacer />
          Total: {{ TotalPercent }}%
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: "FormCargoInterno",
  props: {
    form: {
      type: Object,
      require: true,
    },
    errors: {
      type: Object,
      require: true,
    },
  },
  data() {
    return {
      options: {
        agencies: [],
        departments: [],
      },
    };
  },
  async mounted() {
    const { data } = await axios.get("/admin/cargos-internos/create");
    this.options = { ...data.data.options };
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
    TotalPercent() {
      return this.form.cargos_sucursal.reduce(
        (acc, curr) => acc + curr.percent,
        0
      );
    },
  },
  methods: {
    addCargoSucursal() {
      this.form.cargos_sucursal.push({
        sucursal_id: null,
        department_id: null,
      });
    },
    deleteCargoSucusal(index) {
      this.form.cargos_sucursal.splice(index, 1);
    },
    async save() {
      const _this = this;
      if (!this.$refs.form.validate()) return;
      try {
        const { data } = await axios.post("/admin/cargos-internos", this.form);
        console.log("Success", data);
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