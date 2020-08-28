<template>
  <div class="component-wrap">
    <v-row>
      <v-col sm="12" lg="3" class="mb-4 controls">
        <!-- <v-navigation-drawer v-model="drawer"> -->
        <v-form ref="filterForm">
          <v-text-field
            prepend-icon="mdi-magnify"
            label="Filtrar por Nombre"
            v-model="filters.name"
            clearable
            class="mt-2"
          ></v-text-field>
          <!-- <v-select
            hide-details
            outlined
            dense
            prepend-icon="mdi-calendar-month-outline"
            v-model="filters.month"
            :items="options.months"
            label="Mes Vencimiento"
            :menu-props="{ offsetY: true }"
            item-text="name"
            item-value="value"
            clearable
            class="mt-2"
          ></v-select>
          <v-select
            hide-details
            outlined
            dense
            prepend-icon="mdi-calendar-month-outline"
            v-model="filters.year"
            :items="options.years"
            item-text="name"
            item-value="name"
            label="Año Vencimiento"
            clearable
            class="mt-2"
          ></v-select>
          <v-select
            hide-details
            outlined
            dense
            prepend-icon="mdi-calendar-month-outline"
            v-model="filters.month_installation"
            :items="options.months"
            label="Mes Instalacion"
            :menu-props="{ offsetY: true }"
            item-text="name"
            item-value="value"
            clearable
            class="mt-2"
          ></v-select>
          <v-select
            hide-details
            outlined
            dense
            prepend-icon="mdi-calendar-month-outline"
            v-model="filters.year_installation"
            :items="options.years"
            item-text="name"
            item-value="name"
            label="Año Instalacion"
            clearable
            class="mt-2"
          ></v-select> -->
          <v-select
            hide-details
            outlined
            dense
            v-model="filters.agency"
            :items="options.agencies"
            label="Sucursal"
            :menu-props="{ offsetY: true }"
            item-text="name"
            item-value="name"
            prepend-icon="mdi-filter-variant"
            clearable
            filled
            class="mt-2"
          ></v-select>
          <v-select
            hide-details
            outlined
            dense
            v-model="filters.department"
            :items="options.departments"
            label="Departamento"
            :menu-props="{ offsetY: true }"
            item-text="name"
            item-value="name"
            prepend-icon="mdi-filter-variant"
            clearable
            filled
            class="mt-2"
          ></v-select>
          <v-select
            hide-details
            outlined
            dense
            v-model="filters.payment_type"
            :items="options.payment_type"
            label="Tipo de Pago"
            :menu-props="{ offsetY: true }"
            prepend-icon="mdi-filter-variant"
            clearable
            filled
            class="mt-2"
          ></v-select>
          <v-autocomplete
            hide-details
            outlined
            dense
            v-model="filters.groupId"
            filled
            multiple
            small-chips
            deletable-chips
            clearable
            prepend-icon="mdi-filter-variant"
            label="Filtrar por Clientes"
            :items="options.gpsGroup"
            item-text="name"
            item-value="id"
            class="mt-2"
          ></v-autocomplete>
          <v-autocomplete
            hide-details
            outlined
            dense
            v-model="filters.chipsId"
            filled
            multiple
            small-chips
            deletable-chips
            clearable
            prepend-icon="mdi-filter-variant"
            label="Buscar CHIP"
            :items="options.gpsChips"
            item-text="sim"
            item-value="sim"
            class="mt-2"
          ></v-autocomplete>
          <v-select
            hide-details
            outlined
            dense
            v-model="filters.estatus"
            :items="options.estatus"
            label="Estatus GPS"
            item-text="name"
            item-value="name"
            :menu-props="{ offsetY: true }"
            prepend-icon="mdi-filter-variant"
            clearable
            filled
            class="mt-2"
          ></v-select>
          <v-row class="justify-space-between mx-auto">
            <v-checkbox
              v-model="filters.assigned"
              label="Asignados"
              hide-details
            ></v-checkbox>
            <v-checkbox
              v-model="filters.deallocated"
              label="Sin Asignar"
              hide-details
            ></v-checkbox>
            <v-checkbox
              v-model="filters.renewed"
              label="Renovados"
              hide-details
            ></v-checkbox>
            <v-checkbox
              v-model="filters.expired"
              label="Vencidos"
              hide-details
            ></v-checkbox>
            <v-checkbox
              v-model="filters.canceled"
              label="Cancelados"
              hide-details
            ></v-checkbox>
          </v-row>
        </v-form>
        <!-- </v-navigation-drawer> -->
      </v-col>
      <v-col sm="12" lg="9" class="pl-2">
        <!-- <v-sheet height="600"> -->
        <gps-widget-stats></gps-widget-stats>
        <v-data-table
          v-bind:headers="headers"
          :options.sync="pagination"
          :items="items"
          :server-items-length="totalItems"
          dense
          fixed-header
          height="500"
          class="elevation-4 text-uppercase caption"
        >
          <!-- Top -->
          <template v-slot:top>
            <v-toolbar elevation="0">
              <!-- <v-row>
                <div
                  class="col-4 flex-grow-1 caption text-uppercase text-truncate"
                >
                  Mes Anterior({{ lastMonth.mes }}):<br />
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="grey" dark small v-bind="attrs" v-on="on">
                        {{ lastMonth.total }}
                      </v-chip>
                    </template>
                    <span>GPS Totales</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        color="primary"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ lastMonth.renovados }}
                      </v-chip>
                    </template>
                    <span>GPS Renovados</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="blue" dark small v-bind="attrs" v-on="on">
                        {{ lastMonth.nuevos }}
                      </v-chip>
                    </template>
                    <span>GPS Nuevos</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        :color="getColor(lastMonth.porcentaje * 100)"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ lastMonth.porcentaje | percent() }}
                      </v-chip>
                    </template>
                    <span>Porcentaje Renovacion</span>
                  </v-tooltip>
                </div>
                <div
                  class="col-4 flex-grow-1 caption text-uppercase text-truncate"
                >
                  Mes Actual({{ currentMonth.mes }}):<br />
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="grey" dark small v-bind="attrs" v-on="on">
                        {{ currentMonth.total }}
                      </v-chip>
                    </template>
                    <span>GPS Totales</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        color="primary"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ currentMonth.renovados }}
                      </v-chip>
                    </template>
                    <span>GPS Renovados</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="blue" dark small v-bind="attrs" v-on="on">
                        {{ currentMonth.nuevos }}
                      </v-chip>
                    </template>
                    <span>GPS Nuevos</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        :color="getColor(currentMonth.porcentaje * 100)"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ currentMonth.porcentaje | percent() }}
                      </v-chip>
                    </template>
                    <span>Porcentaje Renovacion</span>
                  </v-tooltip>
                </div>
                <div
                  class="col-4 flex-grow-1 caption text-uppercase text-truncate"
                >
                  Mes Siguiente({{ nextMonth.mes }}):<br />
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="grey" dark small v-bind="attrs" v-on="on">
                        {{ nextMonth.total }}
                      </v-chip>
                    </template>
                    <span>GPS Totales</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        color="primary"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ nextMonth.renovados }}
                      </v-chip>
                    </template>
                    <span>GPS Renovados</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip color="blue" dark small v-bind="attrs" v-on="on">
                        {{ nextMonth.nuevos }}
                      </v-chip>
                    </template>
                    <span>GPS Nuevos</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-chip
                        :color="getColor(nextMonth.porcentaje * 100)"
                        dark
                        small
                        v-bind="attrs"
                        v-on="on"
                      >
                        {{ nextMonth.porcentaje | percent() }}
                      </v-chip>
                    </template>
                    <span>Porcentaje Renovacion</span>
                  </v-tooltip>
                </div>
              </v-row> -->
              <v-row>
                <div class="cols-3 d-flex flex-grow-1 justify-end">
                  <!-- <v-btn icon color="accent" @click="drawer = !drawer">
                    <v-icon>mdi-magnify</v-icon>
                  </v-btn> -->
                  <v-btn
                    icon
                    color="secondary"
                    @click="
                      $refs.filterForm.reset(), (pagination.itemsPerPage = 10)
                    "
                  >
                    <v-icon>mdi-filter-remove-outline</v-icon>
                  </v-btn>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        icon
                        color="green"
                        @click="exportGps()"
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-file-excel</v-icon>
                      </v-btn>
                    </template>
                    <span>Exportar</span>
                  </v-tooltip>
                  <v-tooltip top>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        icon
                        color="blue"
                        @click="dialogs.show = true"
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-plus-thick</v-icon>
                      </v-btn>
                    </template>
                    <span>Agregar Nuevo</span>
                  </v-tooltip>
                  <v-btn icon @click="refresh()">
                    <v-icon>mdi-refresh</v-icon>
                  </v-btn>
                </div>
              </v-row>
            </v-toolbar>
          </template>
          <!-- Body  -->
          <template v-slot:[`item.action`]="{ item }">
            <v-menu offset-x>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon small v-bind="attrs" v-on="on">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list shaped>
                <v-list-item-group>
                  <v-list-item @click="editItem(item)">
                    <v-list-item-icon>
                      <v-icon class="blue--text"
                        >mdi-information-outline</v-icon
                      >
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-tile-title>Detalle GPS</v-list-tile-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item @click="openDialogReasign(item)">
                    <v-list-item-icon>
                      <v-icon class="blue--text">mdi-swap-horizontal</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-tile-title>Reasignar GPS</v-list-tile-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item @click="openDialogCancel(item)">
                    <v-list-item-icon>
                      <v-icon class="red--text" dark>mdi-cancel</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-tile-title>Cancelar GPS</v-list-tile-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-list-item v-if="false" @click="trash(item)">
                    <v-list-item-icon>
                      <v-icon class="red--text">mdi-trash-can</v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-tile-title>Eliminar</v-list-tile-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list-item-group>
              </v-list>
            </v-menu>
          </template>

          <template v-slot:[`item.name`]="{ item }">
            <span class="caption">{{ item.name }}</span>
          </template>
          <template v-slot:[`item.sim`]="{ item }">
            <template v-if="item.chip">
              {{ item.chip.sim }}
            </template>
            <!-- <template v-else>
              <v-edit-dialog
                :return-value.sync="item.gps_chip_id"
                large
                persistent
                @save="saveInLine(item, undefined)"
                @cancel="cancel"
              >
                <v-btn outlined x-small color="primary" pa-0
                  >Asignar CHIP</v-btn
                >
                <template v-slot:input>
                  <v-form
                    v-model="validInLine"
                    ref="formInLine"
                    lazy-validation
                  >
                    <div class="mt-4 title">Asignar CHIP</div>
                    <v-autocomplete
                      v-model="item.gps_chip_id"
                      filled
                      clearable
                      prepend-icon="mdi-filter-variant"
                      label="Buscar CHIP"
                      :items="options.gpsChips"
                      item-text="sim"
                      item-value="sim"
                    ></v-autocomplete>
                  </v-form>
                </template>
              </v-edit-dialog>
            </template> -->
          </template>

          <template v-slot:[`item.gps_group`]="{ item }">
            <template v-if="item.gps_group">
              <span class="caption">{{ item.gps_group.name }}</span>
            </template>
            <!-- <template v-else>
              <v-edit-dialog
                :return-value.sync="item.gps_group_id"
                large
                persistent
                @save="saveInLine(item, undefined)"
                @cancel="cancel"
              >
                <v-btn outlined x-small color="primary" pa-0
                  >Asignar a Grupo</v-btn
                >
                <template v-slot:input>
                  <v-form
                    v-model="validInLine"
                    ref="formInLine"
                    lazy-validation
                  >
                    <div class="mt-4 title">Asignar a Grupo</div>
                    <v-autocomplete
                      v-model="item.gps_group_id"
                      filled
                      clearable
                      prepend-icon="mdi-filter-variant"
                      label="Buscar Grupo"
                      :items="options.gpsGroup"
                      item-text="name"
                      item-value="id"
                    ></v-autocomplete>
                  </v-form>
                </template>
              </v-edit-dialog>
            </template> -->
          </template>

          <template v-slot:[`item.cost`]="{ item }">
            <template v-if="item.chip">
              <span class="button">
                {{ item.chip.costo | money() }}
              </span>
            </template>
            <template v-else>
              <span class="button">
                n/a
              </span>
            </template>
          </template>

          <template v-slot:[`item.amount`]="{ item }">
            <template v-if="canEditAmount(item.renew_date)">
              <v-tooltip top>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn text pa-0 v-bind="attrs" v-on="on" small>{{
                    item.amount | money(item.currency)
                  }}</v-btn>
                </template>
                <span>{{ item.invoice }}</span>
              </v-tooltip>
            </template>
            <v-edit-dialog
              v-else
              :return-value.sync="item.amount"
              large
              persistent
              save-text="RENOVAR"
              @save="renewInvoice(item)"
              @cancel="cancel"
            >
              <v-btn outlined small pa-0>{{
                item.amount | money(item.currency)
              }}</v-btn>
              <template v-slot:input>
                <v-container>
                  <div class="title">Renovacion</div>
                  <div class="overline">{{ item.name }}</div>
                  <div class="mt-2 caption text-uppercase">
                    Ingresar Factura:
                  </div>
                  <v-form
                    v-model="validInLine"
                    ref="formInLine"
                    lazy-validation
                  >
                    <v-text-field
                      v-model.lazy="item.invoice"
                      label="Folio Factura"
                      :rules="[(v) => !!v || 'Campo requerido']"
                      counter
                      autofocus
                    ></v-text-field>
                    <v-text-field
                      v-model.lazy="item.amount"
                      label="Importe Factura"
                      type="tel"
                      prefix="$"
                      :rules="[(v) => !!v || 'Campo requerido']"
                      autofocus
                    ></v-text-field>
                    <v-row dense>
                      <v-col cols="6">
                        <v-select
                          v-model="item.currency"
                          :items="['MXN', 'USD']"
                          label="Moneda"
                          :rules="[(v) => !!v || 'Campo requerido']"
                        ></v-select>
                      </v-col>
                      <v-col cols="6">
                        <v-text-field
                          v-model.lazy="item.exchange_rate"
                          label="Tipo Cambio"
                          type="Numeric"
                          prefix="$"
                          :rules="[(v) => !!v || 'Campo requerido']"
                          autofocus
                        ></v-text-field>
                      </v-col>
                    </v-row>
                  </v-form>
                </v-container>
              </template>
            </v-edit-dialog>
          </template>

          <template v-slot:[`item.renew_date`]="{ item }">
            <span class="overline text-capitalize">
              <!-- {{ $appFormatters.formatDate(item.installation_date) }} -->
              {{ $appFormatters.formatDate(item.renew_date, "MMM YYYY") }}
            </span>
          </template>

          <template v-slot:[`item.renew_days`]="{ item }">
            <span class="overline text-capitalize">
              <v-chip
                :color="
                  getColor($appFormatters.formatTimeDiffNow(item.renew_date))
                "
                dark
                small
              >
                {{ $appFormatters.formatTimeDiffNow(item.renew_date, "days") }}
                Dias
              </v-chip>
            </span>
          </template>
          <template v-slot:[`item.estatus`]="{ item }">
            <span class="overline text-capitalize">
              {{ item.estatus || "NA" }}
            </span>
          </template>
        </v-data-table>
        <!-- </v-sheet> -->
      </v-col>
    </v-row>
    <!-- cards importes totales -->
    <v-row
      v-show="countAmountCost >= 0 && countAmountInvoice >= 0"
      class="justify-space-between"
    >
      <v-col cols="12" md="4">
        <gps-card
          propTitle="Total Costo MXN:"
          :propAmount="countAmountCost"
        ></gps-card
      ></v-col>
      <v-col cols="12" md="4"
        ><gps-card
          propTitle="Total Facturado MXN:"
          :propAmount="countAmountInvoice"
        ></gps-card
      ></v-col>
      <v-col cols="12" md="4">
        <gps-card
          propTitle="Total Facturado USD:"
          :propAmount="countAmountInvoiceUSD"
          propCurrency="USD"
        ></gps-card
      ></v-col>
    </v-row>
    <!-- dialog -->
    <v-dialog
      v-model="dialogs.show"
      fullscreen
      transition="dialog-bottom-transition"
      :overlay="false"
    >
      <v-card>
        <v-toolbar class="primary">
          <v-btn
            icon
            @click.native="(dialogs.show = false), (editedIndex = -1)"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title class="white--text">{{ formTitle }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn
              text
              @click.native="(dialogs.show = false), (editedIndex = -1)"
              >Done</v-btn
            >
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text>
          <gps-edit
            v-if="formEdit"
            :propGpsId="dialogs.gps.id"
            :propOptionGroups="options.gpsGroup"
          ></gps-edit>
          <gps-add
            v-else-if="dialogs.show"
            :propOptionGroups="options.gpsGroup"
          ></gps-add>
          <!-- :propOptionChips="options.gpsChips" -->
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogCancelled" max-width="500px">
      <v-card>
        <v-card-title>
          Cancelacion GPS
        </v-card-title>
        <v-card-text>
          <v-text-field
            label="Fecha Cancelacion"
            v-model="cancelled_date"
            type="date"
            filled
          ></v-text-field>
          <v-textarea
            label="Descripcion:"
            v-model="description"
            outlined
          ></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" text @click="dialogCancelled = false">
            Close
          </v-btn>
          <v-btn color="primary" text @click="cancelled(item)">
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-dialog v-model="dialogReasign" max-width="500px">
      <v-card>
        <v-card-title>
          Reasignar GPS
        </v-card-title>
        <v-card-text>
          <v-text-field
            label="Nombre GPS"
            v-model="reasign_name"
            filled
          ></v-text-field>
          <v-autocomplete
            v-model="reasign_sim"
            filled
            clearable
            prepend-icon="mdi-filter-variant"
            label="Asignar CHIP"
            :items="options.gpsChips"
            item-text="sim"
            item-value="sim"
          ></v-autocomplete>
          <v-autocomplete
            v-model="reasign_group"
            filled
            clearable
            prepend-icon="mdi-filter-variant"
            label="Asignar Grupo/Cliente"
            :items="options.gpsGroup"
            item-text="name"
            item-value="id"
          ></v-autocomplete>
          <v-text-field
            v-model="reasign_date"
            label="Fecha Activacion"
            type="date"
          ></v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-btn color="error" @click="(dialogReasign = false), (item = {})">
            Cancelar
          </v-btn>
          <v-btn color="primary" text @click="reasign(item)">
            OK
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import GpsAdd from "@admin/gps/components/GpsAdd.vue";
import GpsEdit from "@admin/gps/components/GpsEdit.vue";
import GpsCard from "@admin/gps/components/GpsAmountsCard.vue";
import GpsWidgetStats from "@admin/gps/components/widgets/GpsStats.vue";

import optionMonths from "~/api/months.json";
import optionYears from "~/api/years.json";
import optionAgencies from "~/api/agencies.json";
import optionDepartments from "~/api/departments.json";
import optionEstatus from "~/api/estatus_gps.json";
export default {
  components: {
    GpsAdd,
    GpsEdit,
    GpsCard,
    GpsWidgetStats,
  },
  data() {
    return {
      validInLine: false,
      drawer: false,
      headers: [
        {
          value: "action",
          align: "center",
          divider: true,
          width: 10,
          class: "pa-auto",
          sortable: false,
        },
        {
          text: "Nombre GPS",
          value: "name",
          align: "left",
          sortable: true,
        },
        {
          text: "SIM",
          value: "sim",
          align: "right",
          sortable: true,
        },
        {
          text: "Grupo",
          value: "gps_group",
          align: "left",
          sortable: false,
        },
        {
          text: "Costo",
          value: "cost",
          align: "right",
          sortable: false,
        },
        {
          text: "Facturado",
          value: "amount",
          align: "center",
          sortable: false,
        },
        {
          text: "Fecha de Renovacion",
          value: "renew_date",
          align: "center",
          width: 135,
          sortable: false,
        },
        {
          text: "Dias vence:",
          value: "renew_days",
          align: "center",
          width: 125,
          class: "pa-0",
          sortable: true,
        },
        {
          text: "Estatus",
          value: "estatus",
          align: "center",
          class: "pa-0",
          sortable: true,
        },
      ],
      item: {},
      dialogCancelled: false,
      dialogReasign: false,
      cancelled_date: "",
      description: "",
      reasign_date: "",
      reasign_sim: "",
      reasign_name: "",
      reasign_group: "",
      items: [],
      items_np: [],
      itemEditInLine: {},
      totalItems: 0,
      pagination: {
        itemsPerPage: 10,
        page: 1,
      },
      editedIndex: -1,
      dialogs: {
        show: false,
        gps: null,
      },
      options: {
        months: optionMonths,
        years: optionYears,
        gpsGroup: [],
        gpsChips: [],
        agencies: optionAgencies,
        departments: optionDepartments,
        estatus: optionEstatus,
        payment_type: ["CARGO", "CONTADO", "CREDITO"],
      },
      filters: {
        name: null,
        month: null,
        year: null,
        month_installation: null,
        year_installation: null,
        groupId: [],
        chipsId: [],
        agency: null,
        department: null,
        assigned: null,
        deallocated: null,
        expired: null,
        canceled: null,
        renewed: null,
        payment_type: null,
        estatus: null,
      },
    };
  },
  mounted() {
    const self = this;

    self.$eventBus.$on(["GPS_ADDED", "GPS_UPDATED", "GPS_DELETED"], () => {
      self.loadGps(() => {});
      self.loadGpsGroup(() => {});
      self.loadGpsChips(() => {});
    });
    self.loadGpsGroup(() => {});
    self.loadGpsChips(() => {});
  },
  watch: {
    pagination: {
      handler: _.debounce(function() {
        this.loadGps(() => {});
      }, 700),
      deep: true,
    },
    filters: {
      handler: _.debounce(function(v) {
        this.loadGps(() => {});
      }, 700),
      deep: true,
    },
    item: {
      handler: _.debounce(function(v) {
        const self = this;
        self.reasign_date = self.$appFormatters.formatDate(
          self.item.installation_date,
          "yyyy-MM-DD"
        );
        self.reasign_sim = parseInt(self.item.gps_chip_id);
        self.reasign_name = self.item.name;
        self.reasign_group = self.item.gps_group_id;
      }, 700),
      deep: true,
    },
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Registrar Nuevo GPS" : "Detalle GPS";
    },
    formEdit() {
      return this.editedIndex !== -1;
    },
    countAmountCost() {
      const self = this;
      if (self.items_np.length > 0) {
        return self.items_np
          .map((item) => {
            return !!item.chip ? item.chip.costo : 0;
          })
          .reduce((acc, curr) => {
            return parseFloat(acc) + parseFloat(curr);
          });
      } else {
        return 0;
      }
    },
    countAmountInvoice() {
      const self = this;
      if (self.items_np.length > 0) {
        return self.items_np
          .map((item) => {
            return !!item.amount
              ? parseFloat(item.amount) * parseFloat(item.exchange_rate)
              : 0;
          })
          .reduce((acc, curr) => {
            return parseFloat(acc) + parseFloat(curr);
          });
      } else {
        return 0;
      }
    },
    countAmountInvoiceUSD() {
      const self = this;
      if (self.items_np.length > 0) {
        return self.items_np
          .map((item) => {
            return item.currency == "USD" ? parseFloat(item.amount) : 0;
          })
          .reduce((acc, curr) => {
            return parseFloat(acc) + parseFloat(curr);
          });
      } else {
        return 0;
      }
    },
    currentMonth() {
      const self = this;
      let month = moment().month();
      if (self.items_np.length > 0) {
        let gps = self.items_np.filter(
          (item) => moment(item.renew_date).month() == month
        );
        return self.renewGpsStats(gps, month);
      } else {
        return {};
      }
    },
    lastMonth() {
      const self = this;
      let month = moment()
        .subtract(1, "M")
        .month();
      if (self.items_np.length > 0) {
        let gps = self.items_np.filter(
          (item) => moment(item.renew_date).month() == month
        );
        return self.renewGpsStats(gps, month);
      } else {
        return {};
      }
    },
    nextMonth() {
      const self = this;
      let month = moment()
        .add(1, "M")
        .month();
      if (self.items_np.length > 0) {
        let gps = self.items_np.filter(
          (item) => moment(item.renew_date).month() == month
        );
        return self.renewGpsStats(gps, month);
      } else {
        return {};
      }
    },
  },
  methods: {
    refresh() {
      const self = this;
      self.pagination.page = 1;
      self.loadGpsGroup(() => {});
      self.loadGpsChips(() => {});
      self.loadGps(() => {});
    },
    openDialogCancel(item) {
      this.item = {};
      this.cancelled_date = "";
      this.description = "";
      this.item = item;
      this.dialogCancelled = true;
    },
    openDialogReasign(item) {
      this.item = {};
      this.reasign_date = "";
      this.reasign_sim = "";
      this.item = item;
      this.dialogReasign = true;
    },
    editItem(item) {
      const self = this;
      self.editedIndex = self.items.indexOf(item);
      self.dialogs.gps = item;
      self.dialogs.show = true;
    },
    loadGps(cb) {
      const self = this;

      let params = {
        name: self.filters.name,
        month: self.filters.month,
        year: self.filters.year,
        month_installation: self.filters.month_installation,
        year_installation: self.filters.year_installation,
        agency: self.filters.agency,
        department: self.filters.department,
        payment_type: self.filters.payment_type,
        estatus: self.filters.estatus,
        group_id: self.filters.groupId.join(","),
        chips_id: self.filters.chipsId.join(","),
        assigned: self.filters.assigned ? self.filters.assigned : null,
        deallocated: self.filters.deallocated ? self.filters.deallocated : null,
        expired: self.filters.expired ? self.filters.expired : null,
        renewed: self.filters.renewed ? self.filters.renewed : null,
        canceled: self.filters.canceled ? self.filters.canceled : null,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        page: self.pagination.page,
        per_page: self.pagination.itemsPerPage,
      };

      // no paginate
      const no_paginate = { ...params, paginate: "no" };
      // const no_paginate = { paginate: "no" };
      axios.get("/admin/gps", { params: no_paginate }).then(function(response) {
        self.items_np = response.data.data;
      });

      axios.get("/admin/gps", { params: params }).then(function(response) {
        self.items = response.data.data.data;
        self.totalItems = response.data.data.total;
        self.pagination.totalItems = response.data.data.total;
        (cb || Function)();
      });
    },
    loadGpsGroup(cb) {
      const self = this;
      let params = {
        paginate: "no",
      };

      axios
        .get("/admin/gpsCustomers", { params: params })
        .then(function(response) {
          self.options.gpsGroup = response.data.data;
          cb();
        });
    },
    loadGpsChips(cb) {
      const self = this;
      let params = {
        paginate: "no",
        // deallocated: false,
      };

      axios.get("/admin/chips", { params: params }).then(function(response) {
        self.options.gpsChips = response.data.data;
        cb();
      });
    },
    trash(gps) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirm Deletion",
        message: "Are you sure you want to delete this GPS?",
        okCb: () => {
          axios
            .delete("/admin/gps/" + gps.id)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.$eventBus.$emit("GPS_DELETED");
              self.$eventBus.$emit("GPS_GROUP_DELETED");
              self.$eventBus.$emit("GPS_CHIP_UPDATED");
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit("showSnackbar", {
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    cancelled(gps) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmar Cancelacion",
        message: "¿Seguro en Cancelar el GPS?",
        okCb: () => {
          let payload = {
            // cancellation_date: this.$appFormatters.formatDate(
            //   new Date(),
            //   "yyyy-MM-DD"
            // ),
            cancellation_date: this.cancelled_date,
            description: this.description,
          };
          axios
            .post("/admin/gps/cancelled/" + gps.id, payload)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.$eventBus.$emit("GPS_DELETED");
              self.$eventBus.$emit("GPS_GROUP_DELETED");
              self.$eventBus.$emit("GPS_CHIP_UPDATED");
              self.dialogCancelled = false;
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit("showSnackbar", {
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    reasign(gps) {
      const self = this;

      self.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmar Reasignacion",
        message: "¿Seguro en Reasignar el GPS?",
        okCb: () => {
          let payload = {
            name: self.reasign_name,
            gps_chip_id: self.reasign_sim,
            gps_group_id: self.reasign_group,
            installation_date: self.reasign_date,
          };
          axios
            .post("/admin/gps/reasign/" + gps.id, payload)
            .then(function(response) {
              self.$store.commit("showSnackbar", {
                message: response.data.message,
                color: "success",
                duration: 3000,
              });

              self.$eventBus.$emit("GPS_DELETED");
              self.$eventBus.$emit("GPS_GROUP_DELETED");
              self.$eventBus.$emit("GPS_CHIP_UPDATED");
              self.dialogReasign = false;
            })
            .catch(function(error) {
              if (error.response) {
                self.$store.commit("showSnackbar", {
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
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    saveInLine(item, renew = null) {
      const self = this;
      if (self.$refs.formInLine.validate()) {
        if (renew) item.renew = renew;
        self.$store.commit("showDialog", {
          type: "confirm",
          title: "Confirmar Cambio",
          message: "¿Seguro Realizar Cambios al GPS?",
          okCb: () => {
            axios
              .put("/admin/gps/" + item.id, item)
              .then(function(response) {
                self.$store.commit("showSnackbar", {
                  message: response.data.message,
                  color: "success",
                  duration: 3000,
                });

                self.$eventBus.$emit("GPS_UPDATED");
                self.$eventBus.$emit("GPS_GROUP_UPDATED");
              })
              .catch(function(error) {
                item.amount = 0;
                item.invoice = "";
                item.currency = "MXN";
                item.exchange_rate = 1;
                if (error.response) {
                  self.$store.commit("showSnackbar", {
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
          cancelCb: () => {
            self.cancel();
            item.amount = 0;
            item.invoice = "";
            item.currency = "MXN";
            item.exchange_rate = 1;
          },
        });
      } else {
        self.cancel();
        item.amount = 0;
        item.invoice = "";
        item.currency = "MXN";
        item.exchange_rate = 1;
      }
    },
    renewInvoice(item) {
      const self = this;
      if (self.$refs.formInLine.validate()) {
        self.$store.commit("showDialog", {
          type: "confirm",
          title: "Confirmar Cambio",
          message: "¿Seguro en Renovacion de  GPS?",
          okCb: () => {
            axios
              .post("/admin/gps/renewInvoice/" + item.id, item)
              .then(function(response) {
                self.$store.commit("showSnackbar", {
                  message: response.data.message,
                  color: "success",
                  duration: 3000,
                });

                self.$eventBus.$emit("GPS_UPDATED");
                self.$eventBus.$emit("GPS_GROUP_UPDATED");
              })
              .catch(function(error) {
                item.amount = 0;
                item.invoice = "";
                item.currency = "MXN";
                item.exchange_rate = 1;
                if (error.response) {
                  self.$store.commit("showSnackbar", {
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
          cancelCb: () => {
            self.cancel();
            item.amount = 0;
            item.invoice = "";
            item.currency = "MXN";
            item.exchange_rate = 1;
          },
        });
      } else {
        self.cancel();
        item.amount = 0;
        item.invoice = "";
        item.currency = "MXN";
        item.exchange_rate = 1;
      }
    },
    cancel() {
      const self = this;
      self.$store.commit("showSnackbar", {
        message: "Cancel",
        color: "error lighten-1",
        duration: 3000,
      });
    },
    getColor(value) {
      if (value < 31) return "red";
      else if (value < 62) return "orange";
      else return "green";
    },
    exportGps() {
      const self = this;

      let params = {
        name: self.filters.name,
        month: self.filters.month,
        year: self.filters.year,
        month_installation: self.filters.month_installation,
        year_installation: self.filters.year_installation,
        agency: self.filters.agency,
        department: self.filters.department,
        payment_type: self.filters.payment_type,
        group_id: self.filters.groupId.join(","),
        chips_id: self.filters.chipsId.join(","),
        assigned: self.filters.assigned ? self.filters.assigned : null,
        deallocated: self.filters.deallocated ? self.filters.deallocated : null,
        expired: self.filters.expired ? self.filters.expired : null,
        renewed: self.filters.renewed ? self.filters.renewed : null,
        canceled: self.filters.canceled ? self.filters.canceled : null,
        order_sort: self.pagination.sortDesc[0] ? "desc" : "asc",
        order_by: self.pagination.sortBy[0] || "name",
        paginate: "no",
      };
      self.$store.commit("showLoader");
      axios
        .get("/admin/gps-export", {
          params: params,
          responseType: "blob",
        })
        .then((res) => {
          const url = window.URL.createObjectURL(new Blob([res.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "gps.xlsx"); //or any other extension
          document.body.appendChild(link);
          link.click();
        })
        .catch(function(error) {
          if (error.response) {
            self.$store.commit("showSnackbar", {
              message: error.response.data.message,
              color: "error",
              duration: 3000,
            });
          } else if (error.request) {
            console.log(error.request);
          } else {
            console.log("Error", error.message);
          }
        })
        .finally(function() {
          self.$store.commit("hideLoader");
        });
    },
    renewGpsStats(GpsMonth = [], month) {
      let toRenewGps = GpsMonth.filter(
        (item) => moment(item.installation_date).year() < moment().year()
      );
      let newGps = GpsMonth.filter(
        (item) => moment(item.installation_date).year() == moment().year()
      );
      let renewedGps = toRenewGps.filter(
        (item) =>
          moment(item.renew_date).year() ==
          moment()
            .add(1, "y")
            .year()
        // && moment(item.installation_date).year() < moment().year()
      );
      let totalGps = GpsMonth.length;
      let totalNewGps = newGps.length;
      let totalRenewedGps = renewedGps.length;
      let percent = totalRenewedGps / totalGps;
      // let percent = (totalRenewedGps * 100) / totalGps;
      return {
        total: totalGps,
        nuevos: totalNewGps,
        renovados: totalRenewedGps,
        porcentaje: percent,
        mes: moment.months(month),
      };
    },
    canEditAmount(date) {
      return (
        moment(date).year() ==
        moment()
          .add(1, "y")
          .year()
      );
    },
  },
};
</script>

<style scoped>
.controls {
  position: relative;
}
</style>
