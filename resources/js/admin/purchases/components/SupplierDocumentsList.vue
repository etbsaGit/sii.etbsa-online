<template>
  <div>
    <v-list-item
      v-for="requirement in documentsList"
      :key="requirement.id"
      :value="requirement"
    >
      <v-tooltip top>
        <template #activator="{on}">
          <v-list-item-avatar v-on="on">
            <v-menu
              left
              offset-x
              :disabled="!$gate.allow('auditDocument', 'compras')"
            >
              <template v-slot:activator="{ on }">
                <v-icon
                  :class="iconStatus[requirement.status_key].color"
                  dark
                  v-on="on"
                >
                  {{ iconStatus[requirement.status_key].icon }}
                </v-icon>
              </template>
              <v-list>
                <v-list-item-group color="primary">
                  <v-list-item
                    v-for="(item, index) in actionsStatus"
                    :key="index"
                    @click="changeStatusDocument(requirement, item.status_key)"
                  >
                    <v-list-item-icon>
                      <v-icon> {{ item.icon }} </v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title>
                        {{ item.title }}
                      </v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                </v-list-item-group>
              </v-list>
            </v-menu>
          </v-list-item-avatar>
        </template>
        <span>{{ iconStatus[requirement.status_key].text }}</span>
      </v-tooltip>

      <v-list-item-content>
        <v-list-item-title>
          {{ requirement.name }}
        </v-list-item-title>

        <v-list-item-subtitle>
          {{ requirement.description }}
        </v-list-item-subtitle>
        <v-list-item-subtitle>
          <span v-if="requirement.file">
            {{
              $appFormatters.formatDate(requirement.file.lastModifiedDate, "ll")
            }}
          </span>
        </v-list-item-subtitle>
      </v-list-item-content>

      <v-list-item-action>
        <v-btn
          v-if="requirement.status_key != 'documento.valido'"
          text
          dark
          icon
          color="primary"
          @click="openDialogUpload(requirement)"
        >
          <v-icon>mdi-upload</v-icon>
        </v-btn>
        <v-btn
          v-if="requirement.file || requirement.file_path"
          text
          dark
          icon
          color="blue"
          @click="showFile(requirement)"
        >
          <v-icon> mdi-download</v-icon>
        </v-btn>
      </v-list-item-action>
    </v-list-item>

    <v-dialog v-model="dialogUpload" persistent max-width="600px">
      <v-card>
        <v-card-title>
          <span class="text-h5"> Subir {{ requirement.name }} </span>
        </v-card-title>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12">
                <v-file-input
                  v-model="requirement.file"
                  label="Seleccionar Documento"
                  outlined
                  required
                ></v-file-input>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="blue darken-1" text @click="closeDialog(requirement)">
            Cancelar
          </v-btn>
          <v-btn
            color="blue darken-1"
            text
            @click="uploadDocument(requirement)"
            :disabled="!requirement.file"
          >
            Cargar Archivo
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
export default {
  name: "SupplierDocumentsList",
  props: {
    documentsList: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      requirement: {
        file: null,
        file_path: null,
      },
      dialogUpload: false,
      actionsStatus: [
        {
          title: "Rechazar",
          icon: "mdi-file-alert-outline",
          status_key: "documento.invalido",
        },
        {
          title: "Aprobar",
          icon: "mdi-clipboard-check-outline",
          status_key: "documento.valido",
        },
        {
          title: "Eliminar",
          icon: "mdi-clipboard-alert-outline",
          status_key: "documento.none",
        },
      ],
      iconStatus: {
        "documento.revicion": {
          color: "blue",
          icon: "mdi-clock-alert-outline",
          text: "Para Revision",
        },
        "documento.none": {
          color: "grey",
          icon: "mdi-clipboard-alert-outline",
          text: "Sin Documento",
        },
        "documento.invalido": {
          color: "red",
          icon: "mdi-file-alert-outline",
          text: "Documento Invalido",
        },
        "documento.valido": {
          color: "green",
          icon: "mdi-clipboard-check-outline",
          text: "Aprobado",
        },
      },
    };
  },
  methods: {
    openDialogUpload(requirement) {
      this.dialogUpload = true;
      this.requirement = requirement;
    },
    closeDialog(requirement) {
      this.dialogUpload = false;
      if (!requirement.file_path) {
        requirement.file = null;
        requirement.status_key = "documento.none";
      }
    },
    showFile(requirement) {
      if (requirement.file) {
        const url = URL.createObjectURL(requirement.file);
        window.open(url, "_blank");
      } else if (requirement.file_path) {
        console.log("Abrir Path ", requirement.file_path);
        window.open(requirement.file_path, "_blank");
      }
    },
    uploadDocument(document) {
      const currentDate = new Date();
      const limitDate = new Date();
      limitDate.setDate(currentDate.getMonth() - 3);
      if (document.file.lastModifiedDate <= limitDate) {
        this.$store.commit("showSnackbar", {
          message: "Tiene 3 mes o mas de Antiguedad el Archivo",
          color: "error",
          duration: 3000,
        });
        document.file = null;
        document.status = "documento.none";
        return;
      }
      document.status_key = "documento.revicion";
      this.$nextTick(() => {
        this.dialogUpload = false;
      });
    },

    changeStatusDocument(document, status_key) {
      document.status_key = status_key;
      if (status_key == "documento.none") document.file = null;
    },
  },
};
</script>
<style></style>
