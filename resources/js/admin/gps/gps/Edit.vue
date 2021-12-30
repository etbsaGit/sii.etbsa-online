<template>
  <v-card flat>
    <v-card-text class="overflow-y-auto">
      <v-form ref="form" v-model="valid" lazy-validation>
        <gps-form :form.sync="form" :options="options" editing></gps-form>
      </v-form>
      <v-simple-table
        v-show="historical.length > 0"
        max-height="300"
        fixed-header
        dense
        class="caption text-uppercase"
      >
        <template v-slot:default>
          <thead>
            <tr>
              <th class="text-left">
                #
              </th>
              <th class="text-left">
                Factura
              </th>
              <th class="text-left">
                Importe Moneda
              </th>
              <th class="text-left">
                Fecha Facturacion
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in historical" :key="index">
              <td>{{ index + 1 }}</td>
              <td>{{ item.invoice }}</td>
              <td>{{ item.amount | currency }} {{ item.currency }}</td>
              <td>{{ item.invoice_date }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
    </v-card-text>
    <v-card-actions>
      <v-btn :disabled="!valid" color="success" @click="save" block>
        Editar
        <v-icon right>mdi-pencil</v-icon>
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import GpsForm from "../forms/GpsForm.vue";
export default {
  components: { GpsForm },
  props: {
    propGpsId: {
      required: true,
      type: [String, Number],
    },
  },

  data() {
    return {
      valid: true,
      form: {},
      historical: [],
      options: {
        groups: [],
        chips: [],
        types: ["CONTADO", "CREDITO", "CARGO"],
      },
    };
  },
  mounted() {
    console.log("mounted");
    this.loadGps(() => {});
    this.loadOptions(() => {});
    this.$store.commit("setBreadcrumbs", [
      { label: "GPS", to: { name: "gps.list" } },
      { label: "Detalle" },
    ]);
  },
  destroyed() {
    console.log("destroy");
    this.$destroy();
  },
  methods: {
    save() {
      if (!this.$refs.form.validate()) return;
      const _this = this;

      axios
        .put(`/admin/gps/${_this.propGpsId}`, _this.form)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });

          _this.$eventBus.$emit("GPS_REFRESH");
        })
        .catch(function (error) {
          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
    loadGps(cb) {
      const _this = this;
      axios
        .get("/admin/gps/" + _this.propGpsId + "/edit")
        .then(function (response) {
          let Gps = response.data.data;
          _this.form = {
            ...Gps,
            installation_date: _this.$appFormatters.formatDate(
              Gps.installation_date
            ),
            renew_date: _this.$appFormatters.formatDate(Gps.renew_date),
            invoice_date: Gps.invoice_date
              ? _this.$appFormatters.formatDate(Gps.invoice_date || "")
              : null,
            cancellation_date: Gps.cancellation_date
              ? _this.$appFormatters.formatDate(Gps.cancellation_date || "")
              : null,
          };
          _this.historical = Gps.historical;
        })
        .catch(function (error) {
          _this.$store.commit("hideLoader");

          if (error.response) {
            _this.$store.commit("showSnackbar", {
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
      cb();
    },
    loadOptions(cb) {
      const _this = this;
      axios.get("/admin/gps/search/resources").then(function (response) {
        _this.options.chips = response.data.data.chips;
        _this.options.groups = response.data.data.groups;
        (cb || Function)();
      });
    },
  },
};
</script>
