<template>
  <v-tabs color="green" left class="caption">
    <v-tab>Campo</v-tab>
    <v-tab>Distribucion y Composicion</v-tab>
    <v-tab>Riego</v-tab>
    <v-tab>Cultivado</v-tab>
    <v-tab>Maquinaria</v-tab>
    <v-tab-item>
      <v-card class="grey lighten-3" flat>
        <v-container fluid>
          <v-row dense>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Reporte Campo"
                icon-title="mdi-silo"
              >
                <template #widget-header-action>
                  <v-btn text @click="$eventBus.$emit('SAVE_REPORTE_CAMPO')">
                    <v-icon left> mdi-content-save</v-icon> Guardar
                  </v-btn>
                </template>
                <metatable-reporte-campo
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="{
                    cultivos: metatable.cultivos || [],
                    reporte_campo: metatable.reporte_campo,
                  }"
                >
                </metatable-reporte-campo>
              </app-widget>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card class="grey lighten-3" flat>
        <v-container fluid>
          <v-row dense>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Distribucion"
                icon-title="mdi-elevation-rise"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogDistribucion = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-distribucion
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.distribucion || []"
                  :dialogForm="dialogDistribucion"
                  @close="dialogDistribucion = false"
                  @edit="dialogDistribucion = true"
                />
              </app-widget>
            </v-col>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Composicion"
                icon-title="mdi-view-compact-outline"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogComposicion = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-composicion
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.composicion || []"
                  :dialogForm="dialogComposicion"
                  @close="dialogComposicion = false"
                  @edit="dialogComposicion = true"
                />
              </app-widget>
            </v-col>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Tenencia de Tierra"
                icon-title="mdi-weather-hazy"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogTenencia = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-tenencia-tierra
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.tenencia || []"
                  :dialogForm="dialogTenencia"
                  @close="dialogTenencia = false"
                  @edit="dialogTenencia = true"
                />
              </app-widget>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card class="grey lighten-3" flat>
        <v-container fluid>
          <v-row dense>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Abastecimiento"
                icon-title="mdi-water"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogAbastecimiento = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-abastecimiento
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.abastecimiento || []"
                  :dialogForm="dialogAbastecimiento"
                  @close="dialogAbastecimiento = false"
                  @edit="dialogAbastecimiento = true"
                />
              </app-widget>
            </v-col>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Tipo de Riego"
                icon-title="mdi-waves"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogRiego = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-riego
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.tipo_riego || []"
                  :dialogForm="dialogRiego"
                  @close="dialogRiego = false"
                  @edit="dialogRiego = true"
                />
              </app-widget>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card class="grey lighten-3" flat>
        <v-container fluid>
          <v-row dense>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Cultivos"
                icon-title="mdi-seed-outline"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogCultivado = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-cultivado
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.cultivado || []"
                  :dialogForm="dialogCultivado"
                  @close="dialogCultivado = false"
                  @edit="dialogCultivado = true"
                />
              </app-widget>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-tab-item>
    <v-tab-item>
      <v-card class="grey lighten-3" flat>
        <v-container fluid>
          <v-row dense>
            <v-col cols="12">
              <app-widget
                padding-hide
                title="Maquinaria"
                icon-title="mdi-tractor"
              >
                <template #widget-header-action>
                  <v-btn icon @click="dialogEquipos = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-equipos
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.equipos || []"
                  :dialogForm="dialogEquipos"
                  @close="dialogEquipos = false"
                  @edit="dialogEquipos = true"
                />
              </app-widget>
            </v-col>
            <v-col cols="12">
              <app-widget padding-hide title="Vehiculos" icon-title="mdi-car">
                <template #widget-header-action>
                  <v-btn icon @click="dialogVehiculos = true">
                    <v-icon color="green">
                      mdi-plus-thick
                    </v-icon>
                  </v-btn>
                </template>
                <metatable-vehicle
                  slot="widget-content"
                  :customer-id="customerId"
                  :items="metatable.vehiculos || []"
                  :dialogForm="dialogVehiculos"
                  @close="dialogVehiculos = false"
                  @edit="dialogVehiculos = true"
                />
              </app-widget>
            </v-col>
          </v-row>
        </v-container>
      </v-card>
    </v-tab-item>
  </v-tabs>
</template>
<script>
import AppWidget from "@admin/components/AppWidget.vue";
import MetatableVehicle from "./MetatableVehicle.vue";
import MetatableEquipos from "./MetatableEquipos.vue";
import MetatableCultivado from "./MetatableCultivado.vue";
import MetatableAbastecimiento from "./MetatableAbastecimiento.vue";
import MetatableRiego from "./MetatableRiego.vue";
import MetatableComposicion from "./MetatableComposicion.vue";
import MetatableTenenciaTierra from "./MetatableTenenciaTierra.vue";
import MetatableDistribucion from "./MetatableDistribucion.vue";
import MetatableReporteCampo from "./MetatableReporteCampo.vue";
export default {
  name: "MetatableCustomerInfo",
  components: {
    AppWidget,
    MetatableVehicle,
    MetatableEquipos,
    MetatableCultivado,
    MetatableAbastecimiento,
    MetatableRiego,
    MetatableComposicion,
    MetatableTenenciaTierra,
    MetatableDistribucion,
    MetatableReporteCampo,
  },
  props: {
    customerId: {
      type: Number | String,
      require: true,
    },
  },
  mounted() {
    this.loadItem();
  },
  data() {
    return {
      dialogEdit: false,
      dialogVehiculos: false,
      dialogEquipos: false,
      dialogCultivado: false,
      dialogAbastecimiento: false,
      dialogRiego: false,
      dialogDistribucion: false,
      dialogComposicion: false,
      dialogTenencia: false,
      metatable: {},
    };
  },
  methods: {
    async loadItem() {
      const _this = this;
      await axios.get(`/admin/customers/${_this.customerId}`).then((res) => {
        _this.metatable = { ...res.data.data.metatable };
      });
    },
  },
};
</script>
<style></style>
