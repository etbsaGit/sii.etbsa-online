<template>
  <v-container fluid>
    <v-card
      class="d-flex flex-md-row flex-sm-column flex-wrap mt-2 pa-4"
      color="grey lighten-2"
      flat
      tile
      v-if="GPS"
    >
      <v-card class="mx-auto my-auto" max-width="300"> 
        <v-card-text>
          <div>Nombre GPS</div>
          <p class="text-h6 text--primary">
            {{ GPS.name }}
          </p>
          <div>Estatus:</div>
          <p class="text--primary">
            {{ GPS.estatus }}
          </p>
          <div>Fecha Instalacion:</div>
          <p class="text--primary">
            {{ $appFormatters.formatDate(GPS.installation_date, "LL") }}
          </p>
          <div>Fecha Renovacion:</div>
          <p class="text--primary">
            {{ $appFormatters.formatDate(GPS.renew_date, "LL") }}
          </p>
          <div>Fecha Cancelacion:</div>
          <p class="text--primary">
            {{ $appFormatters.formatDate(GPS.cancellation_date, "LL") }}
          </p>
        </v-card-text>
      </v-card>
      <v-card v-if="GPS.gps_group" class="mx-auto my-auto" max-width="300">
        <v-card-text>
          <div>Cliente Asignado</div>
          <p class="text-h6 text--primary">
            {{ GPS.gps_group.name }}
          </p>
          <div>Razon Social</div>
          <p class="text--primary">
            {{ GPS.gps_group.razon_social }}
          </p>
          <div>Agencia:</div>
          <p class="text--primary">
            {{ GPS.gps_group.agency }}
          </p>
          <div>Departamento:</div>
          <p class="text--primary">
            {{ GPS.gps_group.department || "S/A" }}
          </p>
          <div>Tipo de Pago:</div>
          <p class="text--primary">
            {{ GPS.payment_type }}
          </p>
        </v-card-text>
      </v-card>
      <v-card v-if="GPS.chip" class="mx-auto my-auto" min-width="300">
        <v-card-text>
          <div>CHIP Asignado</div>
          <p class="text-h6 text--primary">
            {{ GPS.chip.sim }}
          </p>
          <div>Costo:</div>
          <p class="text--primary">
            {{ GPS.chip.costo | money() }}
          </p>
          <div>Datos Facturacion:</div>
          <p class="text-h6 text--primary">
            {{ GPS.invoice || "Sin Factura" }}
          </p>
          <div>importe Factura:</div>
          <p class="text-h6 text--primary">
            {{ GPS.amount | money(GPS.currency) }}
          </p>
        </v-card-text>
      </v-card>
      <v-card class="mx-auto my-auto" min-width="300">
        <v-card-text>
          <div>Usuario que Registro:</div>
          <p class="text-h6 text--primary">
            {{ GPS.user.name }}
          </p>
          <div>Ultima actualizacion</div>
          <p class="text--primary">
            {{ $appFormatters.formatDate(GPS.updated_at, "LLL") }}
          </p>
        </v-card-text>
      </v-card>
    </v-card>
    <v-divider class="my-4"></v-divider>
    <v-data-table
      v-show="GPS.historical.length > 0"
      :items="GPS.historical"
      :headers="headers"
      dense
      disable-pagination
      hide-default-footer
      class="elevation-1 mx-auto caption"
    >
      <template v-slot:[`item.amount`]="{ item }">
        <span class="overline text-capitalize">
          {{ item.amount | money(item.currency) }}
        </span>
      </template>
      <template v-slot:[`item.renew_date`]="{ item }">
        <span class="overline text-capitalize">
          <!-- {{ $appFormatters.formatDate(item.installation_date) }} -->
          {{ $appFormatters.formatDate(item.renew_date, "MMM YYYY") }}
        </span>
      </template>
      <template v-slot:[`item.created_at`]="{ item }">
        <span class="overline text-capitalize">
          {{ $appFormatters.formatDate(item.created_at, "MMMDD YYYY HH:MM") }}
        </span>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
export default {
  props: {
    propGpsId: {
      required: true,
    },
  },
  data() {
    return {
      GPS: {},
      headers: [
        {
          text: "Nombre GPS",
          value: "name",
          align: "left",
        },
        {
          text: "SIM",
          value: "gps_chip_id",
          align: "right",
        },
        {
          text: "Cliente",
          value: "gps_group.name",
          align: "left",
        },
        {
          text: "Factura",
          value: "invoice",
          align: "center",
        },
        {
          text: "Importe",
          value: "amount",
          align: "center",
        },
        {
          text: "Forma de Pago",
          value: "payment_type",
          align: "center",
        },
        {
          text: "Fecha de Renovacion",
          value: "renew_date",
          align: "center",
        },
        {
          text: "Estatus",
          value: "estatus",
          align: "center",
          class: "pa-0",
        },
        {
          text: "Usuario",
          value: "user.name",
          align: "center",
          class: "pa-0",
        },
        {
          text: "Fecha Movimiento",
          value: "created_at",
          align: "center",
          class: "pa-0",
        },
      ],
    };
  },
  created() {
    const self = this;
    self.loadGps(() => {});
  },
  methods: {
    loadGps(cb) {
      const self = this;

      axios.get("/admin/gps/" + self.propGpsId).then(function(response) {
        self.GPS = response.data.data;
        cb();
      });
    },
  },
};
</script>
