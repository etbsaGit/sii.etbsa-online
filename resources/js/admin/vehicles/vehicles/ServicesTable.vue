<template>
  <v-card>
    <v-card-text class="pa-0">
      <template>
        <v-data-table
          :headers="headers"
          :items="items"
          :sort-by="'created_at'"
          :sort-desc="true"
          class="elevation-0 table-striped"
        >
          <template v-slot:[`item.created_at`]="{ value }">
            {{ $appFormatters.formatDate(value, "DD MMM YYYY") }}
          </template>
          <template v-slot:[`item.estatus`]="{ item }">
            <v-chip
              label
              small
              :color="getColorByStatus(item.estatus.key)"
              text-color="white"
            >
              {{ item.estatus.title }}
            </v-chip>
          </template>
          <template v-slot:[`item.action`]="{ item }">
            <v-btn
              v-if="item.estatus.key == 'autorizado'"
              rounded
              small
              color="orange"
              icon
              @click="dialog = true"
            >
              <v-icon>mdi-gas-station-outline</v-icon>
            </v-btn>
            <dialog-confirm
              :show="dialog"
              :max-width="500"
              @close="dialog = false"
              @agree="update(item)"
            >
              <template #title>
                Confirmar Accion.
              </template>
              <template #body>
                <div class="text-center">
                  Inidica como quedo el Odometro despues de la Dispersion
                </div>
              </template>
            </dialog-confirm>
          </template>
        </v-data-table>
      </template>
      <v-divider />
    </v-card-text>
  </v-card>
</template>

<script>
import DialogConfirm from "@admin/components/DialogConfirm.vue";
export default {
  components: { DialogConfirm },
  props: {
    items: {
      type: Array,
      default: () => {
        [];
      },
    },
  },
  data: () => ({
    dialog: false,
    validOdometer: true,
    headers: [
      {
        text: "#",
        align: "left",
        value: "id",
        sortable: false,
      },
      { text: "Fecha", value: "created_at" },
      { text: "Motivo", value: "reason", sortable: false },
      { text: "Estatus", value: "estatus", sortable: false },
      { text: "Accion", value: "action", sortable: false },
    ],
    // items,
    colors: {
      pendiente: "blue",
      autorizado: "green",
      "flotilla.dispersado": "purple",
      denegar: "red",
    },
  }),
  methods: {
    getColorByStatus(status) {
      return this.colors[status];
    },
    async update(item) {
      const _this = this;
      if (!_this.$refs.formOdometer.validate()) return;
      let payload = {
        estatus_key: "flotilla.dispersado",
        ...item,
      };
      await axios
        .put(`/admin/vehicle-dispersal/${item.id}`, payload)
        .then(function (response) {
          _this.$store.commit("showSnackbar", {
            message: response.data.message,
            color: "success",
            duration: 3000,
          });
          _this.dialog = false;
          _this.$eventBus.$emit("REFRESH");
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
  },
};
</script>
