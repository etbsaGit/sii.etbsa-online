<template>
  <v-form ref="form" lazy-validation v-model="valid">
    <v-row>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Prospecto
        </p>
        <v-autocomplete
          v-model="Prospect"
          :items="options.prospects"
          placeholder="Buscar por Nombre o Telefono"
          persistent-placeholder
          :filter="customFilterProspect"
          persistent-hint
          :hint="`Telefono: ${prospect.phone || ''}`"
          item-text="full_name"
          item-value="id"
          class="overline"
          return-object
          clearable
          outlined
          filled
          chips
          dense
        >
          <template v-slot:prepend-item>
            <v-list dense>
              <v-list-item-group mandatory color="blue">
                <v-list-item dense @click="dialog = true">
                  <v-list-item-content>
                    <v-list-item-title>
                      Registrar Nuevo Prospecto
                    </v-list-item-title>
                  </v-list-item-content>
                  <v-list-item-action>
                    <v-btn icon>
                      <v-icon color="primary">mdi-plus</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </v-list-item-group>
            </v-list>
          </template>

          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title v-html="item.full_name" />
              <v-list-item-subtitle v-html="item.phone" />
            </v-list-item-content>
          </template>
        </v-autocomplete>
        <v-dialog v-model="dialog" max-width="600">
          <v-card>
            <v-card-title>
              Registrar nuevo Prospecto
            </v-card-title>
            <v-card-text>
              <v-form>
                <v-row>
                  <v-col cols="12" md="3">
                    <label for="firstnameHorizontalIcons">First Name</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-text-field
                      id="firstnameHorizontalIcons"
                      outlined
                      dense
                      placeholder="First Name"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="3">
                    <label for="emailHorizontalIcons">Email</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-text-field
                      id="emailHorizontalIcons"
                      outlined
                      dense
                      placeholder="Email"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="3">
                    <label for="mobileHorizontalIcons">Mobile</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-text-field
                      id="mobileHorizontalIcons"
                      type="number"
                      outlined
                      dense
                      placeholder="Number"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="3">
                    <label for="passwordHorizontalIcons">Password</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-text-field
                      id="passwordHorizontalIcons"
                      type="password"
                      outlined
                      dense
                      placeholder="Password"
                      hide-details
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" md="3">
                    <label for="countrydHorizontalIcons">Country</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-select
                      id="countryHorizontalIcons"
                      outlined
                      dense
                      placeholder="Country"
                      hide-details
                    ></v-select>
                  </v-col>
                  <v-col cols="12" md="3">
                    <label for="townHorizontalIcons">Town</label>
                  </v-col>

                  <v-col cols="12" md="9">
                    <v-select
                      id="townHorizontalIcons"
                      outlined
                      dense
                      placeholder="Toen"
                      hide-details
                    ></v-select>
                  </v-col>

                  <!-- <v-col offset-md="3" cols="12" md="9">
                    <v-checkbox
                      label="Remember me"
                      class="mt-0"
                      hide-details
                    ></v-checkbox>
                  </v-col> -->

                  <v-col offset-md="3" cols="12" md="9">
                    <v-btn color="primary">
                      Submit
                    </v-btn>
                    <v-btn type="reset" outlined class="mx-2">
                      Reset
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form>

              <!-- <v-form class="multi-col-validation">
                <v-row>
                  <v-col cols="12" md="6">
                    <v-text-field
                      label="First Name"
                      outlined
                      dense
                      placeholder="First Name"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Last Name"
                      outlined
                      dense
                      placeholder="Last Name"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Email"
                      outlined
                      dense
                      placeholder="Email"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      label="City"
                      outlined
                      dense
                      placeholder="City"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Country"
                      outlined
                      dense
                      placeholder="Country"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12" md="6">
                    <v-text-field
                      label="Company"
                      outlined
                      dense
                      placeholder="Company"
                      hide-details
                    ></v-text-field>
                  </v-col>

                  <v-col cols="12">
                    <v-checkbox
                      label="Remember me"
                      class="mt-0"
                      hide-details
                    ></v-checkbox>
                  </v-col>

                  <v-col cols="12">
                    <v-btn color="primary">
                      Submit
                    </v-btn>
                    <v-btn type="reset" outlined class="mx-2">
                      Reset
                    </v-btn>
                  </v-col>
                </v-row>
              </v-form> -->
            </v-card-text>
            <!-- <prospect
              :form.sync="form_prospect"
              @submit="SaveProspect()"
            ></prospect> -->
          </v-card>
        </v-dialog>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Categoria
        </p>
        <v-select
          v-model="form.category_id"
          :items="options.categories"
          placeholder="Categoria"
          item-text="title"
          item-value="id"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Referencia
        </p>
        <v-text-field
          v-model="form.reference"
          placeholder="Referencia"
          hide-details
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="6" sm="3">
        <p class="text-14 mb-1">
          Precio a tratar
        </p>
        <v-text-field
          v-model="form.price"
          placeholder="Precio a tratar"
          hide-details
          outlined
          dense
        ></v-text-field>
      </v-col>
      <v-col cols="6" sm="3">
        <p class="text-14 mb-1">
          Moneda
        </p>
        <v-select
          v-model="form.currency_id"
          :items="options.currency"
          item-value="id"
          item-text="name"
          placeholder="Moneda"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12">
        <p class="text-14 mb-1">
          Descripcion
        </p>
        <v-textarea
          placeholder="Describir el motivo del Seguimiento"
          hide-details
          outlined
          dense
        >
        </v-textarea>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Condicion de pago
        </p>
        <v-select
          v-model="form.payment_condition"
          :items="['Contado', 'Renta', 'Financiamiento']"
          placeholder="Condicion de pago"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Forma del Contacto
        </p>
        <v-select
          v-model="form.way_to_contact"
          :items="[
            'Visita de Campo',
            'Visita en Agencia',
            'Online (Llamada, Whatsapp, Pagina)',
          ]"
          placeholder="Forma del Contacto"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Agencia
        </p>
        <v-select
          v-model="form.agency_id"
          :items="options.agencies"
          item-value="id"
          item-text="title"
          placeholder="Sucursal"
          hide-details
          outlined
          dense
        >
        </v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Departamento
        </p>
        <v-select
          v-model="form.department_id"
          :items="options.departments"
          item-value="id"
          item-text="title"
          placeholder="Departamento"
          hide-details
          outlined
          dense
        ></v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <p class="text-14 mb-1">
          Promotor (Vendedor)
        </p>
        <v-autocomplete
          v-model="form.seller_id"
          :items="options.sellers"
          item-value="id"
          item-text="name"
          placeholder="Promotor"
          hide-details
          clearable
          outlined
          dense
        >
          <template v-slot:selection="{ item }">
            <v-chip label class="overline">
              {{ item.profiable.full_name || item.name }}
            </v-chip>
          </template>
          <template v-slot:item="{ item }">
            <v-list-item-content>
              <v-list-item-title
                v-text="item.profiable.full_name || item.name"
              />
              <v-list-item-subtitle v-html="item.email" />
            </v-list-item-content>
          </template>
        </v-autocomplete>
      </v-col>
    </v-row>
  </v-form>
</template>

<script>
import Prospect from "../../prospect/forms/Prospect.vue";
export default {
  components: { Prospect },
  name: "TrackingForm",
  props: {
    form: {
      type: Object,
      require: true,
    },
  },
  mounted() {
    this.loadOptions();
  },
  data() {
    return {
      valid: true,
      dialog: false,
      prospect: {},
      form_prospect: {
        rfc: null,
        town: null,
        email: null,
        phone: null,
        company: null,
        full_name: null,
        is_moral: 0,
        estate_id: null,
        township_id: null,
      },
      options: {
        prospects: [],
        categories: [],
        currency: [],
        agencies: [],
        departments: [],
        sellers: [],
      },
    };
  },
  watch: {},
  computed: {
    Prospect: {
      get() {
        return this.form.prospect;
      },
      set(val) {
        this.form.prospect_id = val.id;
        this.prospect = val;
      },
    },
  },
  methods: {
    async loadOptions() {
      const _this = this;
      const { data } = await axios.get("/admin/tracking/create");
      _this.options = data.data;
    },
    customFilterProspect(item, queryText, itemText) {
      const textName = item.full_name.toLowerCase();
      const textPhone = item.phone.toLowerCase();
      const searchText = queryText.toLowerCase();

      return (
        textName.indexOf(searchText) > -1 || textPhone.indexOf(searchText) > -1
      );
    },
    SaveProspect() {
      console.log(this.form_prospect);
    },
  },
};
</script>

<style></style>
