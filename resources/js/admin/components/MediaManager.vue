<template>
  <v-card>
    <!-- <v-toolbar flat>
      <v-toolbar-title class="grey--text"> Files </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-btn icon v-show="active[0]">
        <v-icon>mdi-pencil-box-outline</v-icon>
      </v-btn>
      <v-btn icon @click="getFiles()">
        <v-icon>mdi-reload</v-icon>
      </v-btn>
    </v-toolbar> -->

    <v-toolbar
      :color="tree.length ? 'grey darken-4' : 'deep-purple accent-4'"
      dark
    >
      <!-- <v-app-bar-nav-icon v-if="!tree.length"></v-app-bar-nav-icon> -->
      <v-btn v-if="tree.length" icon @click="tree = []">
        <v-icon>mdi-close</v-icon>
      </v-btn>

      <v-toolbar-title v-if="!tree.length"> Archivos </v-toolbar-title>
      <v-toolbar-title v-else>
        {{ tree.length ? `${tree.length} selected` : "Photos" }}
      </v-toolbar-title>

      <v-spacer></v-spacer>

      <v-scale-transition>
        <v-btn v-if="tree.length" key="export" icon>
          <v-icon>mdi-export-variant</v-icon>
        </v-btn>
      </v-scale-transition>
      <v-scale-transition>
        <v-btn v-if="tree.length" key="delete" icon>
          <v-icon>mdi-delete</v-icon>
        </v-btn>
      </v-scale-transition>

      <v-btn color="primary" dark>
        Cargar Archivo(s)
        <v-icon right>mdi-upload</v-icon>
      </v-btn>
      <v-btn icon>
        <v-icon>mdi-folder-plus</v-icon>
      </v-btn>
      <v-btn icon @click="getFiles()">
        <v-icon>mdi-reload</v-icon>
      </v-btn>
    </v-toolbar>

    <v-divider></v-divider>
    <v-card-text>
      <v-treeview
        v-model="tree"
        :items="items"
        item-key="relative_path"
        open-on-clic
        selectable
        hoverable
        activatable
        :active.sync="active"
        selection-type="independent"
        dense
      >
        <template v-slot:prepend="{ item, open }">
          <v-icon v-if="item.type == 'dir'">
            {{ open ? "mdi-folder-open" : "mdi-folder" }}
          </v-icon>
          <v-icon v-else>
            {{ filesIcons[item.extension] }}
          </v-icon>
        </template>
        <template v-slot:append="{ item }">
          <v-btn v-if="item.type == 'file'" icon>
            <v-icon> mdi-cloud-download-outline </v-icon>
          </v-btn>
          <v-btn icon>
            <v-icon> mdi-trash-can-outline </v-icon>
          </v-btn>
        </template>
      </v-treeview>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "MediaManagerComponent",
  props: {
    basePath: {
      type: String,
      default: "/",
    },
    filename: {
      type: String,
      default: null,
    },
    allowMultiSelect: {
      type: Boolean,
      default: true,
    },
    maxSelectedFiles: {
      type: Number,
      default: 0,
    },
    minSelectedFiles: {
      type: Number,
      default: 0,
    },
    showFolders: {
      type: Boolean,
      default: true,
    },
    showToolbar: {
      type: Boolean,
      default: true,
    },
    allowUpload: {
      type: Boolean,
      default: true,
    },
    allowMove: {
      type: Boolean,
      default: true,
    },
    allowDelete: {
      type: Boolean,
      default: true,
    },
    allowCreateFolder: {
      type: Boolean,
      default: true,
    },
    allowRename: {
      type: Boolean,
      default: true,
    },
    allowCrop: {
      type: Boolean,
      default: true,
    },
    allowedTypes: {
      type: Array,
      default: function () {
        return [];
      },
    },
    preSelect: {
      type: Boolean,
      default: true,
    },
    element: {
      type: String,
      default: "",
    },
    details: {
      type: Object,
      default: function () {
        return {};
      },
    },
    expanded: {
      type: Boolean,
      default: true,
    },
  },
  mounted: function () {
    this.getFiles();
  },
  data: () => ({
    filesIcons: {
      html: "mdi-language-html5",
      js: "mdi-nodejs",
      json: "mdi-code-json",
      md: "mdi-language-markdown",
      pdf: "mdi-file-pdf",
      png: "mdi-file-image",
      txt: "mdi-file-document-outline",
      xls: "mdi-file-excel",
    },
    tree: [],
    active: [],
    items: [],
  }),
  methods: {
    async getFiles() {
      var vm = this;
      vm.is_loading = true;
      const { data } = await axios.post("/admin/media/files", {
        folder: vm.basePath,
        details: vm.details,
      });

      vm.items = data.data;

      vm.is_loading = false;
    },
    // findByRelativePath(relativePath) {
    //   const findRecursively = (array, path) => {
    //     for (const item of array) {
    //       if (item.relative_path === path) {
    //         return item;
    //       } else if (item.children && item.children.length > 0) {
    //         const result = findRecursively(item.children, path);
    //         if (result) {
    //           return result;
    //         }
    //       }
    //     }
    //     return null;
    //   };

    //   return findRecursively(this.items, relativePath);
    // },
    findLastDirRelativePath(relativePath) {
      let lastDirRelativePath = null;

      const findRecursively = (array, path) =>
        array.forEach((item) => {
          if (item.relative_path === path && item.type === "dir") {
            lastDirRelativePath = item.relative_path;
          } else if (item.children && item.children.length > 0) {
            findRecursively(item.children, path);
          }
        });

      const findDirByFilePath = (array, path) =>
        array.forEach((item) => {
          const childItem = item.children?.find(
            (child) => child.relative_path === path && child.type === "file"
          );

          if (childItem) {
            lastDirRelativePath = item.relative_path;
          } else if (item.children && item.children.length > 0) {
            findDirByFilePath(item.children, path);
          }
        });

      findRecursively(this.items, relativePath);
      findDirByFilePath(this.items, relativePath);

      return lastDirRelativePath;
    },
    flattenFolders(array) {
      const result = [];

      const flattenRecursively = (items, basePath = "") => {
        for (const item of items) {
          if (item.type === "dir") {
            result.push({
              name: item.name,
              relative_path: item.relative_path,
            });

            if (item.children && item.children.length > 0) {
              flattenRecursively(item.children, item.relative_path);
            }
          }
        }
      };

      if (this.showFolders && this.current_folder !== this.basePath) {
        result.push({
          name: "../",
          relative_path: "/../",
        });
      }

      flattenRecursively(array, this.current_folder);
      return result;
    },
  },
  computed: {
    DirPathActive() {
      const _this = this;
      return _this.active[0]
        ? _this.findLastDirRelativePath(_this.active[0])
        : _this.basePath;
    },
    SelectedFolderToMove() {
      const folders = this.flattenFolders(this.items);

      return folders.map((folder) => ({
        text: folder.name,
        value: folder.relative_path,
      }));
    },
  },
};
</script>