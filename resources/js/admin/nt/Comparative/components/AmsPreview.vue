<template>
  <div>
    <v-banner single-line class="d-print-inline">
      <v-img src="../img/etbsa-logo-agricola.png" width="200"></v-img>
      <template v-slot:actions>
        <v-btn color="primary" text> Comparativo AMS </v-btn>
      </template>
    </v-banner>
    <v-card-text>
      <v-row dense>
        <v-col cols="6">
          <div>Nombre: {{ customer.fullname }}</div>
          <div>Correo: {{ customer.email }}</div>
          <div>Telefono: {{ customer.phone }}</div>
          <div>Ciudad: {{ customer.city }}</div>
          <div>Estado: {{ customer.country }}</div>
          <div>Compañia: {{ customer.company }}</div></v-col
        >
        <v-col cols="6">
          <div v-if="folio" class="align-center text-h3 text-right">
            Folio: #{{ folio.toString().padStart(5, 0) }}
          </div>
        </v-col>
      </v-row>
    </v-card-text>

    <v-card-text class="overflow">
      <v-simple-table dense>
        <template v-slot:default>
          <caption>
            Rendimiento sin AMS
          </caption>
          <thead>
            <tr>
              <th class="text-left">Equipo</th>
              <th class="text-left">Nombre</th>
              <th class="text-left">Parametros</th>
              <th class="text-left">Ancho Corte</th>
              <th class="text-left">Vel. Kms</th>
              <th class="text-left">HA x Dia</th>
              <th class="text-left">Diesel x Dia</th>
              <th class="text-left">Precio Diesel</th>
              <th class="text-left">Total Diesel</th>
              <th class="text-left">Costo Diesel x HA</th>
              <th class="text-left">Operador x HA</th>
              <th class="text-left">Servicio x HA</th>
              <th class="text-left">Llantas x HA</th>
              <th class="text-left">Seguro x HA</th>
              <th class="text-left">Depreciacion x HA</th>
              <th class="text-left">Costo Total x HA</th>
            </tr>
          </thead>
          <tbody class="caption">
            <tr
              v-for="(
                {
                  ancho_corte,
                  costo_diesel_x_ha,
                  costo_x_ha,
                  depreciacion,
                  diesel_precio,
                  diesel_total,
                  diesel_x_dia,
                  equipment,
                  ha_x_dia,
                  llantas_x_ha,
                  operador_x_ha,
                  seguro_x_ha,
                  servicio_x_ha,
                  velocidad_km,
                },
                index
              ) in tableWithoutAms"
              :key="index"
            >
              <td>{{ equipment.category }}</td>
              <td>
                <div>{{ equipment.name }}</div>
                <div class="subtitle">{{ equipment.price | money }}</div>
              </td>
              <td>{{ equipment.value }} {{ equipment.unit }}</td>
              <td>{{ ancho_corte }}</td>
              <td>{{ velocidad_km }}</td>
              <td>{{ ha_x_dia }}</td>
              <td>{{ diesel_x_dia }}</td>
              <td>{{ diesel_precio | money }}</td>
              <td>{{ diesel_total | money }}</td>
              <td>{{ costo_diesel_x_ha | money }}</td>
              <td>{{ operador_x_ha | money }}</td>
              <td>{{ servicio_x_ha | money }}</td>
              <td>{{ llantas_x_ha | money }}</td>
              <td>{{ seguro_x_ha | money }}</td>
              <td>{{ depreciacion | money }}</td>
              <td>{{ costo_x_ha | money }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-divider class="my-4" />
      <v-simple-table dense>
        <template v-slot:default>
          <caption>
            Rendimiento con AMS
          </caption>
          <thead>
            <tr>
              <th class="text-left">Equipo</th>
              <th class="text-left">Nombre</th>
              <th class="text-left">Parametros</th>
              <th class="text-left">Ancho Corte</th>
              <th class="text-left">Vel. Kms</th>
              <th class="text-left">HA x Dia</th>
              <th class="text-left">Diesel x Dia</th>
              <th class="text-left">Precio Diesel</th>
              <th class="text-left">Total Diesel</th>
              <th class="text-left">Costo Diesel x HA</th>
              <th class="text-left">Operador x HA</th>
              <th class="text-left">Servicio x HA</th>
              <th class="text-left">Llantas x HA</th>
              <th class="text-left">Seguro x HA</th>
              <th class="text-left">Depreciacion x HA</th>
              <th class="text-left">Costo Total x HA</th>
            </tr>
          </thead>
          <tbody class="caption">
            <tr
              v-for="(
                {
                  ancho_corte,
                  costo_diesel_x_ha,
                  costo_x_ha,
                  depreciacion,
                  diesel_precio,
                  diesel_total,
                  diesel_x_dia,
                  equipment,
                  ha_x_dia,
                  llantas_x_ha,
                  operador_x_ha,
                  seguro_x_ha,
                  servicio_x_ha,
                  velocidad_km,
                },
                index
              ) in tableWithAms"
              :key="index"
            >
              <td>{{ equipment.category }}</td>
              <td>
                <div>{{ equipment.name }}</div>
                <div class="subtitle">{{ equipment.price | money }}</div>
              </td>
              <td>{{ equipment.value }} {{ equipment.unit }}</td>
              <td>{{ ancho_corte }}</td>
              <td>{{ velocidad_km }}</td>
              <td>{{ ha_x_dia }}</td>
              <td>{{ diesel_x_dia }}</td>
              <td>{{ diesel_precio | money }}</td>
              <td>{{ diesel_total | money }}</td>
              <td>{{ costo_diesel_x_ha | money }}</td>
              <td>{{ operador_x_ha | money }}</td>
              <td>{{ servicio_x_ha | money }}</td>
              <td>{{ llantas_x_ha | money }}</td>
              <td>{{ seguro_x_ha | money }}</td>
              <td>{{ depreciacion | money }}</td>
              <td>{{ costo_x_ha | money }}</td>
            </tr>
          </tbody>
        </template>
      </v-simple-table>
      <v-row class="mt-4">
        <v-col cols="12" md="8">
          <v-card>
            <v-card-title class="title">DIFERENCIAS</v-card-title>
            <v-card-text class="px-0">
              <v-simple-table>
                <template v-slot:default>
                  <thead>
                    <tr>
                      <th class="text-left">Equipo</th>
                      <th class="text-left">Nombre</th>
                      <th class="text-left">Costo X HA S/AMS</th>
                      <th class="text-left">Costo X HA C/AMS</th>
                      <th class="text-left">Diferencia</th>
                      <th class="text-left">Porcentaje %</th>
                      <th class="text-left">No. HAs</th>
                      <th class="text-left">Ahorro</th>
                      <th class="text-left">Pasadas</th>
                      <th class="text-left">Total Ahorro</th>
                    </tr>
                  </thead>
                  <tbody class="caption">
                    <tr
                      v-for="(
                        {
                          equipment,
                          percent_diff,
                          save,
                          total_cost_with_ams,
                          total_cost_without_ams,
                          total_diff,
                          total_save,
                        },
                        index
                      ) in tableDiffAms"
                      :key="index"
                    >
                      <td>{{ equipment.category }}</td>
                      <td>
                        <div>{{ equipment.name }}</div>
                        <div class="subtitle">
                          {{ equipment.price | money }}
                        </div>
                      </td>
                      <td>{{ total_cost_without_ams | money }}</td>
                      <td>{{ total_cost_with_ams | money }}</td>
                      <td class="red--text font-weight-bold">
                        {{ total_diff | money }}
                      </td>
                      <td class="green--text font-weight-bold">
                        {{ percent_diff }} %
                      </td>
                      <td>{{ hectares }}</td>
                      <td>{{ save | money }}</td>
                      <td>{{ equipment.steps }}</td>
                      <td class="red--text font-weight-bold">
                        {{ total_save | money }}
                      </td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-col>
        <v-col cols="12" md="4">
          <v-card>
            <v-card-title class="title">AHORRO</v-card-title>
            <v-card-text class="px-0">
              <v-simple-table dense>
                <template v-slot:default>
                  <thead></thead>
                  <tbody class="overline">
                    <tr>
                      <td>Total ahorro por ciclo</td>
                      <td>{{ tableSaveAms.total_save_x_cycle | money }}</td>
                    </tr>
                    <tr>
                      <td>Ciclos por año</td>
                      <td>{{ tableSaveAms.cycles_year }}</td>
                    </tr>
                    <tr>
                      <td>Valor del AMS</td>
                      <td>{{ tableSaveAms.ams_value | money }}</td>
                    </tr>
                    <tr>
                      <td>Retorno de la Inversion</td>
                      <td>{{ tableSaveAms.return_investment }} Dias</td>
                    </tr>
                    <tr>
                      <td>Ahorro por HA</td>
                      <td>{{ tableSaveAms.save_x_ha | money }}</td>
                    </tr>
                    <tr>
                      <td>40% Enganche con Seguro y Comision</td>
                      <td>{{ tableSaveAms.enganche | money }}</td>
                    </tr>
                    <tr>
                      <td>4 Pagos Semestrales Sin Intereses</td>
                      <td>{{ tableSaveAms.payments_semestral | money }}</td>
                    </tr>
                  </tbody>
                </template>
              </v-simple-table>
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </div>
</template>
<script>
export default {
  props: {
    hectares: {
      require: true,
    },
    customer: {
      require: true,
      type: Object,
      default: () => {
        return {};
      },
    },
    tableSaveAms: {
      require: true,
      type: Object | Array,
      default: () => {
        return {};
      },
    },
    tableDiffAms: {
      require: true,
      type: Array,
      default: () => {
        return [];
      },
    },
    tableWithAms: {
      require: true,
      type: Array,
      default: () => {
        return [];
      },
    },
    tableWithoutAms: {
      require: true,
      type: Array,
      default: () => {
        return [];
      },
    },
    folio: {
      require: false,
      type: Number | String,
    },
  },
};
</script>
<style></style>
