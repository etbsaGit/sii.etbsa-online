<template>
  <v-form ref="formSupplier" v-model="valid" lazy-validation>
    <v-expansion-panels v-model="panel" multiple dark>
      <v-expansion-panel>
        <v-expansion-panel-header class="overline" color="blue darken-3" ripple>
          Datos del Proveedor
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row class="overline mt-3">
            <v-col cols="12" md="4" class="pb-0">
              <v-text-field
                v-model="form.alias"
                label="Alias del Proveedor:"
                :rules="[(v) => !!v || 'Es Requerido']"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="8" class="pb-0">
              <v-text-field
                v-model="form.business_name"
                label="Razon Social:"
                outlined
                :rules="[(v) => !!v || 'Es Requerido']"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4" class="pb-0">
              <v-text-field
                v-model="form.rfc"
                label="RFC:"
                :rules="[(v) => !!v || 'Es Requerido']"
                counter="12"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="8" class="pb-0">
              <v-text-field
                v-model="form.address"
                label="Domicilio:"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="pb-0">
              <v-text-field
                v-model="form.phone"
                v-mask="'(###)###-##-##'"
                placeholder="(###)###-##-##"
                hint="Numero 10 digitos"
                label="Telefono:"
                outlined
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="6" class="pb-0">
              <v-text-field
                v-model="form.email"
                label="Email:"
                type="email"
                outlined
                dense
              ></v-text-field>
            </v-col>
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel>
        <v-expansion-panel-header class="overline">
          Datos Bancarios
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <v-row class="overline">
            <v-col cols="12">
              <v-text-field
                v-model="form.billing_data.bank"
                label="Nombre del Banco:"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.billing_data.account"
                label="Cuenta:"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.billing_data.clabe"
                label="CLABE:"
                dense
              ></v-text-field>
            </v-col>
            <v-col cols="12" md="4">
              <v-text-field
                v-model="form.billing_data.agency"
                label="sucursal:"
                dense
              ></v-text-field>
            </v-col>
          </v-row>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
    <!-- <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn @click="$eventBus.$emit('CLOSE_DIALOG')">Cancelar</v-btn>
      <v-btn
        v-if="$gate.allow('editSupplier', 'compras')"
        color="green"
        dark
        class="ml-4"
        @click="save"
        :disabled="!valid"
      >
        Guardar
      </v-btn>
    </v-card-actions> -->
  </v-form>
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
  data() {
    return {
      panel: [0, 1],
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
  },
  methods: {
    save() {
      this.$emit("submit");
    },
  },
};
</script>
