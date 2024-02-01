<template>
  <v-card class="mx-auto" flat>
    <v-list subheader two-line dense>
      <v-subheader inset class="title">Lista de Archivos Adjuntos</v-subheader>
      <v-list-item v-for="file in files" :key="file.name">
        <v-list-item-avatar>
          <v-icon class="blue" dark>mdi-file-upload</v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ file.name }}</v-list-item-title>

          <v-list-item-subtitle>
            {{ file.created_at ? file.created_at : file.lastModifiedDate }}
          </v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-action class="d-flex">
          <v-btn icon @click="showFile(file)">
            <v-icon
              :color="file.file_path ? 'blue lighten-1' : 'grey lighten-1'"
            >
              mdi-information
            </v-icon>
          </v-btn>
          <v-btn icon @click="deleteFile(file)">
            <v-icon
              :color="file.file_path ? 'red lighten-1' : 'grey lighten-1'"
            >
              mdi-delete
            </v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  props: {
    files: {
      type: Array,
      require: true,
    },
  },
  data: () => ({}),
  methods: {
    showFile(file) {
      if (file.file_path) {
        window.open(file.file_path, "_blank");
      } else if (file) {
        const url = URL.createObjectURL(file);
        window.open(url, "_blank");
      }
    },
    deleteFile(file) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        title: "Confirmacion de eliminacion",
        message: "¿Estás seguro de que quieres eliminar este registro?",
        okCb: async () => {
          if (file.file_path) {
            await axios.delete(`/admin/purchase-file/${file.id}`);
            _this.$eventBus.$emit("ORDERS_REFRESH");
          } else if (file) {
            let index = _this.files.indexOf(file);
            _this.files.splice(index, 1);
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
