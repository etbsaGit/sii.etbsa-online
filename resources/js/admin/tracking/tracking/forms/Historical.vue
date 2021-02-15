<template>
  <v-form ref="form" v-model="valid" lazy-validation v-if="Tracking">
    <v-radio-group v-model="form.estatus" row hide-details class="my-2">
      <v-radio label="Proximo Seguimiento" :value="'activo'"></v-radio>
      <v-radio label="Finalizar Seguimiento" :value="'finalizado'"></v-radio>
      <v-radio label="Formalizar Seguimiento" :value="'formalizado'"></v-radio>
    </v-radio-group>
    <v-card color="grey lighten-4" class="pa-2">
      <v-row align="center">
        <v-col cols="12" md="6" v-if="form.estatus == 'activo'">
          <v-dialog
            ref="dialog"
            v-model="dialogCalendar"
            :return-value.sync="form.date_next_tracking"
            persistent
            transition="scale-transition"
            width="324px"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                v-model="form.date_next_tracking"
                class="my-3"
                label="Fecha Proximo Seguimiento"
                prepend-icon="mdi-calendar"
                dense
                readonly
                :rules="[(v) => !!v || 'Requerido']"
                outlined
                hide-details
                v-bind="attrs"
                v-on="on"
                filled
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="form.date_next_tracking"
              scrollable
              :min="new Date().toISOString().substr(0, 10)"
            >
              <v-spacer></v-spacer>
              <v-btn text color="primary" @click="dialogCalendar = false">
                Cancel
              </v-btn>
              <v-btn
                text
                color="primary"
                @click="$refs.dialog.save(form.date_next_tracking)"
              >
                OK
              </v-btn>
            </v-date-picker>
          </v-dialog></v-col
        >
        <v-col cols="12" md="6">
          <v-select
            v-model="form.assertiveness"
            :items="options.assertiveness"
            label="Certeza:"
            :rules="[(v) => !!v || 'Requerido']"
            outlined
            dense
            filled
            hide-details
          >
            <template v-slot:item="data">
              <v-list-item-content>
                <v-list-item-title
                  class="overline"
                  v-text="data.item.text"
                ></v-list-item-title>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn :color="data.item.color" dark>
                  {{ data.item.value | percent }}
                </v-btn>
              </v-list-item-action>
            </template>
          </v-select>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
            v-model="form.tracking_condition"
            :items="options.condition"
            label="Condicion de operacion:"
            :rules="[(v) => !!v || 'Requerido']"
            outlined
            dense
            filled
            hide-details
          >
          </v-select>
        </v-col>
        <v-col cols="12" md="6" v-if="form.estatus == 'formalizado'">
          <v-text-field
            v-model="form.invoice"
            label="#Pedido o Factura:"
            outlined
            :rules="[(v) => !!v || 'Requerido']"
            dense
            hide-details
            filled
          ></v-text-field>
        </v-col>
      </v-row>

      <v-row align="center">
        <v-col cols="12" md="6">
          <v-text-field
            v-model="form.price"
            hide-details
            label="Ultimo Precio a Tratar:"
            outlined
            dense
            :rules="[(v) => !!v || 'Requerido']"
            reverse
            type="number"
            append-icon="mdi-currency-usd"
            filled
          >
            <template v-slot:prepend-inner>
              <v-btn small text @click="changeCurrency()">
                {{ form.currency }}
              </v-btn>
            </template>
          </v-text-field>
        </v-col>
        <v-col cols="12" md="6">
          <v-select
            v-model="form.type_contacted"
            :items="options.type_contacted"
            label="Medio Contacto:"
            :rules="[(v) => !!v || 'Requerido']"
            hide-details
            dense
            outlined
            filled
          ></v-select>
        </v-col>
      </v-row>
      <v-textarea
        v-model="form.message"
        label="Escribir Comentario..."
        :rules="[(v) => !!v || 'Requerido']"
        outlined
        class="title"
        filled
      >
      </v-textarea>
      <v-btn depressed block :disabled="!valid" color="success" @click="submit">
        Enviar
        <v-icon right dark> mdi-send </v-icon>
      </v-btn>
    </v-card>
  </v-form>
</template>

<script>
import Assertiveness from '@admin/tracking/tracking/resources/assertiveness.json';
export default {
  props: {
    Tracking: {
      required: true,
      type: Object,
    },
  },
  data: () => ({
    valid: true,
    loading: false,
    dialogCalendar: false,
    form: {
      message: null,
      price: 0,
      invoice: null,
      currency: null,
      assertiveness: 0.1,
      tracking_condition: null,
      date_next_tracking: null,
      type_contacted: 'Llamada',
      estatus: 'activo',
    },
    options: {
      type_contacted: ['Llamada', 'Visita Campo', 'En Agencia'],
      condition: ['Por definir', 'Contado', 'Financiamiento', 'Renta'],
      assertiveness: Assertiveness,
    },
  }),
  mounted() {
    this.form.price = this.Tracking.price;
    this.form.currency = this.Tracking.currency;
    this.form.tracking_condition = this.Tracking.tracking_condition;
    this.form.assertiveness = this.Tracking.assertiveness;
  },
  methods: {
    submit() {
      if (!this.$refs.form.validate()) return;
      const self = this;

      let payload = {
        ...self.form,
        last_assertiveness: self.form.assertiveness,
        last_price: self.form.price,
        last_currency: self.form.currency,
      };

      axios
        .put('/admin/tracking/historical/' + self.Tracking.id, payload)
        .then(() => {
          self.$refs.form.resetValidation();
          self.$eventBus.$emit('MESSAGE_ADDED');
          self.$store.commit('showSnackbar', {
            message: response.data.message,
            color: 'success',
            duration: 3000,
          });
        })
        .catch(function(error) {
          self.$store.commit('hideLoader');

          if (error.response) {
            self.$store.commit('showSnackbar', {
              message: error.response.data.message,
              color: 'error',
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log('Error', error.message);
          }
        });

      this.form.message = null;
    },
    changeCurrency() {
      const self = this;
      self.form.currency = self.form.currency === 'MXN' ? 'USD' : 'MXN';
    },
  },
};
</script>

<style></style>
