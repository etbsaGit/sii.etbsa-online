<template>
  <v-container fluid>
    <v-list subheader two-line elevation="2" dense>
      <v-subheader inset>
        Folio Fiscal - UUID
        <v-spacer></v-spacer>
        <v-dialog v-model="dialog" persistent max-width="600px">
          <template v-slot:activator="{ on, attrs }">
            <v-btn small color="primary" dark v-bind="attrs" v-on="on">
              Registrar UUID
            </v-btn>
          </template>
          <v-card>
            <v-form ref="formDocument" v-model="valid" lazy-validation>
              <v-card-title>
                <span class="headline">Relacionar Documento:</span>
              </v-card-title>
              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <v-text-field
                        v-model="form.file_info.folio"
                        label="Folio Factura"
                        :rules="[(v) => !!v || 'Requerido']"
                        persistent-hint
                        required
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="form.uuid"
                        label="Folio Fiscal / UUID*"
                        :rules="uuidRules"
                        v-mask="'NNNNNNNN-NNNN-NNNN-NNNN-NNNNNNNNNNNN'"
                        hint="Formato: NNNNNNNN-NNNN-NNNN-NNNN-NNNNNNNNNNNN"
                        counter
                        persistent-hint
                        required
                      ></v-text-field>
                    </v-col>
                    <v-col v-show="false" cols="12">
                      <v-file-input
                        v-model="uploadFiles"
                        color="deep-purple accent-4"
                        counter
                        label="File input"
                        multiple
                        placeholder="Select your File"
                        prepend-icon="mdi-paperclip"
                        outlined
                        :show-size="1000"
                        disabled
                      >
                        <template v-slot:selection="{ index, text }">
                          <v-chip
                            v-if="index < 2"
                            color="deep-purple accent-4"
                            dark
                            label
                            small
                          >
                            {{ text }}
                          </v-chip>

                          <span
                            v-else-if="index === 2"
                            class="overline grey--text text--darken-3 mx-2"
                          >
                            +{{ uploadFiles.length - 2 }} File(s)
                          </span>
                        </template>
                      </v-file-input>
                    </v-col>
                  </v-row>
                </v-container>
                <small>*indicates required field</small>
              </v-card-text>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue darken-1"
                  dark
                  @click="(dialog = false), $refs.formDocument.reset()"
                >
                  Cancelar
                </v-btn>
                <v-btn
                  color="blue darken-1"
                  text
                  @click="save"
                  :disabled="!valid"
                >
                  Guardar
                </v-btn>
              </v-card-actions>
            </v-form>
          </v-card>
        </v-dialog>
      </v-subheader>
      <v-list-item v-for="file in Files" :key="file.id" dense>
        <v-list-item-avatar>
          <v-icon :class="file.color" dark v-text="file.icon"></v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title v-text="file.uuid"></v-list-item-title>
          <v-list-item-subtitle>
            {{ $appFormatters.formatDate(file.subtitle, "DD MMM YYYY") }}
          </v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-action>
          <v-menu
            v-model="menu"
            :close-on-content-click="false"
            :nudge-width="200"
            left
            offset-x
          >
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-bind="attrs" v-on="on">
                <v-icon color="grey lighten-1">mdi-information</v-icon>
              </v-btn>
            </template>

            <v-card>
              <!-- <v-list>
                <v-list-item>
                  <v-list-item-content>
                    <v-list-item-title>Informacion Factura</v-list-item-title>
                    <v-list-item-subtitle>
                      Programacion de Pago
                    </v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>

              <v-divider></v-divider>

              <v-list>
                <v-list-item>
                  <v-list-item-action>
                    <v-switch v-model="message" color="purple"></v-switch>
                  </v-list-item-action>
                  <v-list-item-title>Factura Pagada</v-list-item-title>
                </v-list-item>

                <v-list-item>
                  <v-list-item-content>
                    <v-text-field placeholder="dd/mm/aaaa"></v-text-field>
                  </v-list-item-content>
                </v-list-item>
              </v-list>

              <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn text @click="menu = false">
                  Cancel
                </v-btn>
                <v-btn color="primary" text @click="menu = false">
                  Save
                </v-btn>
              </v-card-actions> -->
            </v-card>
          </v-menu>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-container>
</template>
<script>
export default {
  props: {
    purchaseId: {
      require: true,
      type: Number,
    },
  },
  data() {
    return {
      valid: true,
      dialog: false,
      uuidRules: [
        (v) => !!v || "UUID es Requerido",
        (v) =>
          /^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{4}-[0-9a-fA-F]{12}$/.test(
            v
          ) || "UUID debe ser Valido.",
      ],
      uploadFiles: [],
      form: {
        uuid: null,
        file_info: {
          folio: null,
        },
      },
      items: [],
      fav: true,
      menu: false,
      message: false,
      hints: true,
    };
  },
  mounted() {
    this.loadPurchaseDocuments();
  },
  computed: {
    Files() {
      return this.items.map((file) => {
        return {
          id: file.id,
          color: "blue",
          icon: "mdi-clipboard-text",
          subtitle: file.created_at,
          info: file.file_info,
          uuid: file.uuid,
          uri: file.uri,
        };
      });
    },
  },
  methods: {
    async save() {
      if (!this.$refs.formDocument.validate()) return;
      const _this = this;
      await axios
        .post(`/admin/purchase-order/${_this.purchaseId}/document`, _this.form)
        .then((response) => {
          _this.loadPurchaseDocuments();
          _this.dialog = false;
          _this.$refs.formDocument.reset();
          _this.$eventBus.$emit("ORDERS_REFRESH");
        });
    },
    async loadPurchaseDocuments() {
      const _this = this;
      await axios
        .get(`/admin/purchase-order/${_this.purchaseId}/document`)
        .then((response) => {
          console.log(response);
          _this.items = response.data.data;
        });
    },
  },
};
</script>
<style lang=""></style>
