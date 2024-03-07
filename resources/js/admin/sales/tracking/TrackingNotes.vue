<template >
  <v-card flat>
    <v-toolbar>
      <v-toolbar-title> Notas del Seguimiento </v-toolbar-title>
      <v-spacer></v-spacer>
      <v-dialog v-model="dialog" persistent max-width="600px">
        <template v-slot:activator="{ on, attrs }">
          <v-btn color="primary" dark v-bind="attrs" v-on="on">
            <v-icon left>mdi-plus</v-icon>
            Agregar Nota
          </v-btn>
        </template>
        <v-card>
          <v-card-title>
            <span class="text-h5">Crear Nota</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-textarea
                    v-model="body"
                    label="Nota*"
                    required
                  ></v-textarea>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="blue darken-1"
              text
              @click="
                dialog = false;
                body = '';
              "
            >
              Cerrar
            </v-btn>
            <v-btn
              color="blue darken-1"
              text
              @click="
                save();
                dialog = false;
                body = '';
              "
            >
              Guardar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
      <v-btn @click="init" icon>
        <v-icon>mdi-refresh</v-icon>
      </v-btn>
    </v-toolbar>

    <v-card-text style="max-height: 500px" class="overflow-auto">
      <div v-for="n in notes" :key="n.id" class="mb-4">
        <div class="text-right subtitle mt-4 font-weight-bold">
          <v-btn
            v-if="n?.created_by?.id == user_id"
            text
            color="red"
            x-small
            @click="deleteConfirm(n)"
          >
            Eliminar nota
            <v-icon color="red" right>mdi-trash-can</v-icon>
          </v-btn>
        </div>
        <blockquote>
          {{ n.body }}
        </blockquote>
        <div class="text-right subtitle mt-4 font-weight-bold">
          {{ n?.created_by?.name }} -
          {{ $appFormatters.formatDate(n.created_at, "L") }}
        </div>
        <v-divider />
      </div>
    </v-card-text>
  </v-card>
</template>
<script>
import { mapGetters } from "vuex";
export default {
  name: "TrackingNotes",
  props: {
    propTrackingId: {
      type: [Number, String],
      required: true,
    },
  },
  data() {
    return {
      dialog: false,
      body: "",
      notes: [],
    };
  },
  mounted() {
    this.init();
  },
  computed: {
    ...mapGetters("user", ["user_id"]),
  },
  methods: {
    async init() {
      const _this = this;

      const { data: data } = await axios.get(
        `/admin/tracking/${_this.propTrackingId}/notes`
      );
      _this.notes = data.data;
    },
    async save() {
      const _this = this;

      let params = {
        body: _this.body,
      };

      await axios.post(`/admin/tracking/${_this.propTrackingId}/notes`, params);
      _this.init();
    },
    deleteConfirm(note) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmacion de eliminacion",
        message: "¿Seguro en eliminar este registro?",
        okCb: async () => {
          try {
            await axios.delete(`/admin/notes/${note.id}`);
            _this.init();
          } catch (error) {
            console.log(error);
          }
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
  },
};
</script>
<style >
</style>