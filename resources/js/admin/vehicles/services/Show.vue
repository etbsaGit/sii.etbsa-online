<template>
  <v-card class="mx-auto" flat dark>
    <v-sheet
      id="scrolling-techniques-3"
      class="overflow-y-auto"
      :height="minHeight"
    >
      <v-list class="transparent" dense>
        <v-list-item-group>
          <v-list-item class="overline">
            <v-list-item-title>Con Cargo a:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ service.agency.title }} - {{ service.department.title }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Vehiculo:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{
                `${service.vehicle.model}
              ${service.vehicle.year}`
              }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Matricula:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ service.vehicle.matricula }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title class="blue--text">
              Solicitante:
            </v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ service.user.name }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>
              Fecha Creacion:
            </v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ $appFormatters.formatDate(service.created_at, "LLL") }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-list-item class="overline">
            <v-list-item-title>Costo del Servicio:</v-list-item-title>
            <v-list-item-subtitle class="text-right">
              {{ service.cost | money }}
            </v-list-item-subtitle>
          </v-list-item>
          <v-expansion-panels flat>
            <v-expansion-panel>
              <v-expansion-panel-header class="px-4 py-0 overline">
                <template v-slot:default="{ open }">
                  <v-row no-gutters dense>
                    <v-col cols="4">
                      Motivo del Servicio:
                    </v-col>
                    <v-col cols="8">
                      <v-fade-transition leave-absolute>
                        <span v-if="open" key="0">
                          {{ service.reason }}
                        </span>
                      </v-fade-transition>
                    </v-col>
                  </v-row>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content class="text-uppercase caption">
                {{ service.reason_description }}
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>
        </v-list-item-group>
      </v-list>
    </v-sheet>
    <v-divider></v-divider>

    <v-card-actions class="justify-end">
      <v-btn color="primary" @click="changeEstatus('autorizado')">
        Autorizar
      </v-btn>
      <v-btn text color="error" @click="changeEstatus('denegar')">
        Denegar
      </v-btn>
      <v-btn text color="orange" @click="changeEstatus('flotilla.dispersado')">
        Dispersado
      </v-btn>
    </v-card-actions>

    <dialog-confirm
      :show="dialog"
      :max-width="500"
      @close="dialog = false"
      @agree="dialog = false"
    >
      <template #title>
        Confirmar Accion.
      </template>
      <template #body>
        <pre> {{ service.estatus_key }} </pre>
      </template>
    </dialog-confirm>
  </v-card>
</template>

<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { DialogConfirm },
  props: {
    serviceId: {
      required: true,
      type: [Number, String],
    },
  },
  data() {
    return {
      dialog: false,
      validOdometer: true,
      service: {
        agency: { title: "" },
        cost: null,
        created_at: "",
        created_by: null,
        department: { title: "" },
        estatus: Object,
        estatus_id: null,
        id: null,
        invoice: null,
        mileage: null,
        reason: "",
        reason_description: "",
        updated_at: "2",
        user: Object,
        vehicle: { model: "", year: "", matricula: "" },
      },
      colors: {
        pendiente: "blue",
        autorizado: "green",
        "flotilla.dispersado": "orange",
        denegar: "red",
      },
    };
  },
  computed: {
    minHeight() {
      const height = this.$vuetify.breakpoint.mobile ? "85vh" : "65vh";
      return `calc(${height} - ${this.$vuetify.application.top}px)`;
    },
  },
  created() {
    this.loadService(() => {});
  },
  methods: {
    getColorByStatus(status) {
      return this.colors[status];
    },
    async update() {
      const _this = this;
      if (_this.service.estatus_key == "flotilla.dispersado") {
        if (!_this.$refs.formOdometer.validate()) return;
      }
      const payload = {
        ..._this.service,
      };
      await axios
        .put(`/admin/vehicle-services/${_this.serviceId}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.$eventBus.$emit("VEHICLE_REFRESH");
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
    async loadService(cb) {
      const _this = this;
      await axios
        .get(`/admin/vehicle-services/${_this.serviceId}`)
        .then(function (response) {
          let Data = response.data.data;
          _this.service = { ...Data };
          (cb || Function)();
        });
    },
    save() {
      this.$store.commit("showSnackbar", {
        message: "Guardar",
        color: "success",
        duration: 2000,
      });
    },
    cancel() {
      this.$store.commit("showSnackbar", {
        message: "Cancelado",
        color: "error",
        duration: 2000,
      });
    },
    open() {
      this.$store.commit("showSnackbar", {
        message: "Modificar Cantidad de Litros",
        color: "info",
        duration: 2000,
      });
    },

    changeEstatus(key = "pendiente") {
      const _this = this;
      _this.service.estatus_key = key;
      _this.dialog = true;
    },
  },
};
</script>
