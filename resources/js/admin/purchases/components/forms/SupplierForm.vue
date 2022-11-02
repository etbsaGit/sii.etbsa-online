<template>
  <v-form ref="formSupplier" v-model="valid" lazy-validation>
    <v-row dense>
      <v-col cols="12" md="8">
        <v-row class="caption text-uppercase" dense>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="form.alias"
              label="Clave en EQUIP:"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="8">
            <v-text-field
              v-model="form.business_name"
              label="Razon Social:"
              outlined
              :rules="[(v) => !!v || 'Es Requerido']"
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-text-field
              v-model="form.rfc"
              label="RFC:"
              :rules="[(v) => !!v || 'Es Requerido']"
              counter="15"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
            <v-autocomplete
              v-model="Estate"
              :items="options.estates"
              item-text="name"
              item-value="id"
              label="Estado:"
              filled
              outlined
              dense
            />
          </v-col>
          <v-col cols="12" md="4">
            <v-autocomplete
              v-model="form.township_id"
              :items="options.townships"
              label="Municipio"
              item-text="name"
              item-value="id"
              :rules="[(v) => !!v || 'El municipio es requerido']"
              filled
              outlined
              outline
              dense
            />
          </v-col>
          <v-col cols="12" md="8">
            <v-text-field
              v-model="form.address"
              label="Domicilio:"
              outlined
              dense
            ></v-text-field>
          </v-col>
          <v-col cols="12" md="4">
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
          <v-col cols="12" md="6">
            <v-text-field
              v-model="form.email"
              label="Email:"
              type="email"
              outlined
              dense
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="3">
            <v-select
              v-model="form.credit_days"
              label="Condicion de Pago:"
              outlined
              :rules="[(v) => !!v || 'Es Requerido']"
              :items="[
                { text: '8 Dias', value: 8 },
                { text: '15 Dias', value: 15 },
                { text: '30 Dias', value: 30 },
                { text: '60 Dias', value: 60 },
                { text: '90 Dias', value: 90 },
              ]"
              dense
            ></v-select>
            <!-- <v-text-field
              v-model="form.credit_days"
              label="Dias de credito"
              hide-details=""
              outlined
              dense
            ></v-text-field> -->
          </v-col>
          <v-col cols="12" md="3">
            <v-currency-field
              v-model="form.credit_limit"
              :default-value="form.credit_limit"
              label="Limite Credito:"
              prefix="$"
              suffix="MXN"
              type="number"
              :rules="[(v) => !!v || 'Es Requerido']"
              outlined
              dense
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-combobox
              v-model="form.giro"
              :items="items"
              label="Giro(s)"
              class="caption"
              deletable-chips
              small-chips
              multiple
              outlined
              filled
              dense
            ></v-combobox>
          </v-col>
        </v-row>
        <v-expansion-panels v-model="panel" multiple>
          <v-expansion-panel>
            <v-expansion-panel-header
              class="title"
              color="grey lighten-3"
              ripple
            >
              Contactos
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-simple-table
                v-show="form.contacts.length > 0"
                class="text-uppercase mt-3"
                dense
              >
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Nombre
                      </th>
                      <th class="text-left">
                        Telefono
                      </th>
                      <th class="text-left">
                        Email
                      </th>
                      <th class="text-left"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in form.contacts" :key="item.name">
                      <td class="caption">{{ item.name }}</td>
                      <td class="caption">{{ item.phone }}</td>
                      <td class="caption">{{ item.email }}</td>
                      <td class="text-right" style="width: 50px;">
                        <v-icon color="red" small @click="deleteContact(index)">
                          mdi-delete
                        </v-icon>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
              <v-form ref="formContact" lazy-validation>
                <v-row class="overline mt-3" dense>
                  <v-col cols="12" md="4">
                    <v-text-field
                      v-model="contact.name"
                      label="Nombre contacto"
                      :rules="[rules.required]"
                      outlined
                      dense
                    ></v-text-field
                  ></v-col>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="contact.phone"
                      label="Telefono"
                      v-mask="'(###)###-##-## Ext: ###'"
                      placeholder="(###)###-##-##"
                      hint="Numero 10 digitos"
                      :rules="[rules.requred, rules.counter]"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="6" md="4">
                    <v-text-field
                      v-model="contact.email"
                      label="Email"
                      append-outer-icon="mdi-plus-thick"
                      @click:append-outer="addContact"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-form>
            </v-expansion-panel-content>
          </v-expansion-panel>
          <!-- <v-expansion-panel>
            <v-expansion-panel-header
              class="title"
              color="grey lighten-3"
              ripple
            >
              Domicilios
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-simple-table
                v-show="form.addresses.length > 0"
                dense
                class="text-uppercase mt-3"
              >
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">
                        Domicilio
                      </th>
                      <th class="text-left">
                        C.P.
                      </th>
                      <th class="text-left">
                        Estado
                      </th>
                      <th class="text-left">
                        Ciudad
                      </th>
                      <th class="text-left"></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="(item, index) in form.addresses"
                      :key="item.name"
                    >
                      <td class="caption">{{ item.address }}</td>
                      <td class="caption">{{ item.postal_code }}</td>
                      <td class="caption">{{ item.estate }}</td>
                      <td class="caption">{{ item.town }}</td>
                      <td class="text-right" style="width: 50px;">
                        <v-icon color="red" small @click="deleteAddress(index)">
                          mdi-delete
                        </v-icon>
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
              <v-row class="overline mt-3" dense>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="address.address"
                    label="Direccion"
                    hide-details
                    outlined
                    dense
                  ></v-text-field
                ></v-col>
                <v-col cols="6" md="2">
                  <v-text-field
                    v-model="address.postal_code"
                    label="C.P."
                    v-mask="'#####'"
                    placeholder="00000"
                    hide-details
                    outlined
                    dense
                  ></v-text-field>
                </v-col>
                <v-col cols="6" md="3">
                  <v-autocomplete
                    v-model="address.estate"
                    :items="options.estates"
                    item-text="name"
                    item-value="name"
                    label="Estado:"
                    filled
                    outlined
                    dense
                  />
                </v-col>
                <v-col cols="6" md="3">
                  <v-text-field
                    v-model="address.town"
                    label="Municipio"
                    hide-details
                    outlined
                    dense
                    append-outer-icon="mdi-plus-thick"
                    @click:append-outer="addAddress"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-expansion-panel-content>
          </v-expansion-panel> -->
        </v-expansion-panels>
      </v-col>

      <v-col cols="12" md="4">
        <v-expansion-panels multiple>
          <v-expansion-panel>
            <v-expansion-panel-header class="title" color="grey lighten-3">
              Datos Bancarios
            </v-expansion-panel-header>
            <v-expansion-panel-content>
              <v-card-text class="px-0 pb-0">
                <v-row class="caption text-uppercase" dense>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.billing_data.bank"
                      label="Nombre del Banco:"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.billing_data.account"
                      label="Cuenta:"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.billing_data.clabe"
                      label="CLABE:"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      v-model="form.billing_data.agency"
                      label="sucursal:"
                      outlined
                      dense
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-expansion-panel-content>
          </v-expansion-panel>
        </v-expansion-panels>
        <v-textarea
          v-model="form.observation"
          label="Observaciones"
          filled
          outlined
          class="pt-4"
        ></v-textarea>
      </v-col>
    </v-row>
  </v-form>
</template>

<script>
import { mixinEstates } from "~/common/mixin/estate_township.js";
export default {
  mixins: [mixinEstates],
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
    const defaulFormContact = Object.freeze({
      name: "",
      phone: "",
      email: "",
    });
    const defaulFormAddresss = Object.freeze({
      address: "",
      postal_code: "",
      estate: "",
      town: "",
    });
    return {
      panel: [0],
      contact: Object.assign({}, defaulFormContact),
      address: Object.assign({}, defaulFormAddresss),
      rules: {
        required: (value) => !!value || "Requerido.",
        counter: (value) => value.length >= 10 || "Min 10 caracteres",
        email: (value) => {
          const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
          return pattern.test(value) || "Email Invalido.";
        },
      },
      select: [],
      items: [
        "Llantas",
        "Papeleria",
        "Motores",
        "Refacciones",
        "Productos Limpieza",
        "Gorras",
        "Botas",
      ],
    };
  },

  mounted() {
    const _this = this;
    _this.loadEstates(() => {
      _this.estate_id = _this.Estate;
    });
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
    Estate: {
      get() {
        return this.form.township
          ? (this.estate_id = this.form.township.estate_id)
          : 0;
      },
      set(_estate_id) {
        this.estate_id = _estate_id;
      },
    },
  },
  methods: {
    save() {
      this.$emit("submit");
    },
    addContact() {
      if (!this.$refs.formContact.validate()) return;
      this.form.contacts.push(this.contact);
      this.contact = Object.assign({}, this.defaulFormContact);
      this.$refs.formContact.resetValidation();
    },
    deleteContact(index) {
      this.form.contacts.splice(index, 1);
    },
    addAddress() {
      this.form.addresses.push(this.address);
      this.address = Object.assign({}, this.defaulFormAddresss);
    },
    deleteAddress(index) {
      this.form.addresses.splice(index, 1);
    },
  },
};
</script>
