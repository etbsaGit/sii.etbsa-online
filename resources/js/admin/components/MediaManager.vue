<template>
  <div>
    <v-card>
      <v-toolbar flat color="secondary">
        <v-toolbar-title class="white--text">Documentos</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn icon @click="modals.new_folder.show = true">
          <v-icon color="white">mdi-folder-plus</v-icon>
        </v-btn>

        <v-btn icon @click="modals.new_files.show = true">
          <v-icon color="white">mdi-file-upload</v-icon>
        </v-btn>
        <v-btn icon v-if="allowMove" @click="modals.move_files.show = true">
          <v-icon color="white">mdi-inbox-arrow-up</v-icon>
        </v-btn>
        <v-btn icon v-if="allowDelete" @click="modals.delete_files.show = true">
          <v-icon color="white">mdi-trash-can</v-icon>
        </v-btn>
        <!-- <v-btn icon v-if="allowDelete" @click="downloadFilesZip">
          <v-icon>mdi-folder-zip-outline</v-icon>
        </v-btn> -->
      </v-toolbar>
      <v-breadcrumbs :items="breadcrumbs">
        <template v-slot:item="{ item }">
          <v-breadcrumbs-item @click="setCurrentPath(item.index)" link>
            <v-chip small>{{ item.text }}</v-chip>
          </v-breadcrumbs-item>
        </template>
      </v-breadcrumbs>
      <v-list subheader two-line color="trasparent" dense>
        <v-list-item-group v-model="selected_files" multiple>
          <v-subheader inset>Folders</v-subheader>
          <v-list-item
            v-for="folder in files.filter((f) => f.type == 'folder')"
            :key="folder.name"
            :value="folder"
            v-on:dblclick="openFile(folder)"
          >
            <v-list-item-avatar>
              <v-icon class="grey lighten-1">
                mdi-folder
              </v-icon>
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title v-text="folder.name"></v-list-item-title>
              <v-list-item-subtitle
                v-text="folder.items"
              ></v-list-item-subtitle>
            </v-list-item-content>
            <!-- <v-list-item-action>
              <v-menu
                left
                offset-x
                transition="slide-x-transition"
                rounded="l-xl"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-information-outline</v-icon>
                  </v-btn>
                </template>
                <v-list shaped dense>
                  <v-list-item-group>
                    <v-list-item
                      @click="
                        modals.rename_file = {
                          show: true,
                          name: folder.name,
                          selected: folder,
                        }
                      "
                    >
                      <v-list-item-icon>
                        <v-icon class="blue--text">mdi-pencil</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>Renombrar</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
              </v-menu>
            </v-list-item-action> -->
          </v-list-item>
          <v-divider inset></v-divider>
          <v-subheader inset>Archivos</v-subheader>
          <v-list-item
            v-for="file in files.filter((f) => f.type != 'folder')"
            :key="file.name"
            :value="file"
            v-on:dblclick="openFile(file)"
          >
            <v-list-item-avatar>
              <v-icon color="blue" v-text="'mdi-file'"></v-icon>
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title v-text="file.name"></v-list-item-title>

              <!-- <v-list-item-subtitle
                v-text="$appFormatters.formatDate(file.last_modified, 'll')"
              ></v-list-item-subtitle> -->
            </v-list-item-content>

            <v-list-item-action>
              <v-menu
                left
                offset-x
                transition="slide-x-transition"
                rounded="l-xl"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon v-bind="attrs" v-on="on">
                    <v-icon>mdi-information</v-icon>
                  </v-btn>
                </template>
                <v-list shaped dense>
                  <v-list-item-group>
                    <v-list-item
                      @click="
                        modals.rename_file = {
                          show: true,
                          name: file.name,
                          selected: file,
                        }
                      "
                    >
                      <v-list-item-icon>
                        <v-icon class="blue--text">mdi-pencil</v-icon>
                      </v-list-item-icon>
                      <v-list-item-content>
                        <v-list-item-title>Renombrar</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
              </v-menu>
            </v-list-item-action>
          </v-list-item>
        </v-list-item-group>
      </v-list>
    </v-card>

    <!--modal new fiels -->
    <dialog-modal
      :show="modals.new_files.show"
      @close="modals.new_files.show = false"
    >
      <template #title>
        Subir Nuevo Archivo
      </template>
      <v-card flat>
        <v-file-input
          v-model="modals.new_files.files"
          color="deep-purple accent-4"
          counter
          label="Archivos"
          multiple
          placeholder="Seleccione sus archivos"
          prepend-icon="mdi-paperclip"
          outlined
          :show-size="1000"
        >
          <template v-slot:selection="{ index, text }">
            <v-chip v-if="index < 2" color="deep-purple accent-4" label small>
              {{ text }}
            </v-chip>

            <span
              v-else-if="index === 2"
              class="text-overline grey--text text--darken-3 mx-2"
            >
              +{{ files.length - 2 }} File(s)
            </span>
          </template>
        </v-file-input>
        <p class="caption grey--text pt-4">
          <v-icon>mdi-alert</v-icon>
          Los Nombres de los Archivos no deben llevar ESPACIOS EN BLANCO use
          (Guiones _--_ o CamelCase)
        </p>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="uploadFiles">upload</v-btn>
        </v-card-actions>
      </v-card>
    </dialog-modal>

    <!-- modal new folder -->
    <dialog-modal
      :show="modals.new_folder.show"
      @close="modals.new_folder.show = false"
    >
      <template #title>
        crear Nueva Carpeta
      </template>
      <v-card flat>
        <v-text-field
          v-model="modals.new_folder.name"
          label="Nueva Carpeta"
          outlined
          placeholder="Escribir nombre"
        ></v-text-field>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="createFolder">Crear</v-btn>
        </v-card-actions>
      </v-card>
    </dialog-modal>

    <!-- modal delete files -->
    <dialog-modal
      :show="modals.delete_files.show"
      @close="modals.delete_files.show = false"
    >
      <template #title>
        Confimar Eliminacion Archivos/Carpetas
      </template>
      <v-card flat>
        <div class="title">
          ¿Está seguro que quiere eliminar los siguientes archivos?
        </div>
        <ul>
          <li
            v-for="(file, i) in selected_files"
            :key="i"
            class="font-weight-bold"
          >
            {{ file.name }}
          </li>
        </ul>
        <p class="caption grey--text pt-4">
          <v-icon>mdi-alert</v-icon>
          Eliminar una carpeta eliminara todos los archivos y carpetas
          contenidas dentro
        </p>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="error" @click="deleteFiles">Eliminar</v-btn>
        </v-card-actions>
      </v-card>
    </dialog-modal>

    <!-- modal move files -->
    <dialog-modal
      :show="modals.move_files.show"
      @close="modals.move_files.show = false"
    >
      <template #title>
        Mover Archivo y/o Folder
      </template>
      <v-card flat>
        <v-select
          v-model="modals.move_files.destination"
          label="Carpeta Destino"
          placeholder="Seleccione Carpeta"
          outlined
          :items="selected_folder_move"
        >
        </v-select>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="moveFiles">Mover</v-btn>
        </v-card-actions>
      </v-card>
    </dialog-modal>

    <!-- modal rename File -->
    <dialog-component
      :show="modals.rename_file.show"
      @close="modals.rename_file = { name: '', show: false, selected: {} }"
      closeable
      :fullscreen="$vuetify.breakpoint.mobile"
      title="Renombrar Archivo/Carpeta"
      max-width="500"
    >
      <v-container fluid>
        <v-text-field
          v-model="modals.rename_file.name"
          label="Nuevo Nombre"
          outlined
          placeholder="Escribir nombre"
          :hint="`Nombre actual: ${modals.rename_file.selected.name}`"
          persistent-hint
        ></v-text-field>
      </v-container>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn @click="renameFile(modals.rename_file.selected)">
          Renombrar
        </v-btn>
      </v-card-actions>
    </dialog-component>
  </div>
</template>

<script>
import DialogComponent from "./DialogComponent.vue";
import DialogModal from "./DialogModal.vue";
const FileDownload = require("js-file-download");
export default {
  components: { DialogModal, DialogComponent },
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
  data() {
    return {
      file_download: FileDownload,
      current_folder: this.basePath,
      selected_files: [],
      files: [],
      is_loading: true,
      hidden_element: null,
      isExpanded: this.expanded,
      modals: {
        new_files: {
          show: false,
          files: [],
        },
        new_folder: {
          show: false,
          name: "",
        },
        move_files: {
          show: false,
          destination: "",
        },
        delete_files: {
          show: false,
        },
        rename_file: {
          show: false,
          name: "",
          selected: {},
        },
      },
    };
  },
  computed: {
    selected_file: function () {
      return this.selected_files[0];
    },
    selected_files_actions() {
      return this.selected_files.map((file) => {
        return file.name;
      });
    },
    selected_folder_move() {
      const _this = this;
      let folders = _this.files
        .filter(
          (file) =>
            file.type == "folder" && !_this.selected_files.includes(file)
        )
        .map((file) => {
          return {
            text: file.name,
            value: `${_this.current_folder}/${file.name}`,
          };
        });
      if (_this.current_folder != _this.basePath && _this.showFolders)
        folders.unshift({
          text: "../",
          value: "/../",
        });
      return folders;
    },
    breadcrumbs() {
      var path = this.current_folder
        .replace(this.basePath, "")
        .split("/")
        .filter(function (el) {
          return el != "";
        });
      path.unshift("Expediente");
      return path.map((menu, i) => {
        return { text: menu, index: i - 1 };
      });
    },
  },
  methods: {
    getFiles: function () {
      var vm = this;
      vm.is_loading = true;
      axios
        .post("/admin/media/files", {
          folder: vm.current_folder,
          //   _token: document.head.querySelector('meta[name="csrf-token"]'),
          details: vm.details,
        })
        .then(({ data }) => {
          let Data = data.data;
          vm.files = [];
          for (var i = 0, file; (file = Data[i]); i++) {
            if (vm.filter(file)) {
              vm.files.push(file);
            }
          }
          vm.selected_files = [];
          if (vm.preSelect && Data.length > 0) {
            vm.selected_files.push(Data[0]);
          }
          vm.is_loading = false;
        });
    },
    selectFile: function (file, e) {
      if ((!e.ctrlKey && !e.metaKey && !e.shiftKey) || !this.allowMultiSelect) {
        this.selected_files = [];
      }

      if (
        e.shiftKey &&
        this.allowMultiSelect &&
        this.selected_files.length == 1
      ) {
        var index = null;
        var start = 0;
        for (var i = 0, cfile; (cfile = this.files[i]); i++) {
          if (cfile === this.selected_file) {
            start = i;
            break;
          }
        }

        var end = 0;
        for (var i = 0, cfile; (cfile = this.files[i]); i++) {
          if (cfile === file) {
            end = i;
            break;
          }
        }

        for (var i = start; i < end; i++) {
          index = this.selected_files.indexOf(this.files[i]);
          if (index === -1) {
            this.selected_files.push(this.files[i]);
          }
        }
      }

      index = this.selected_files.indexOf(file);
      if (index === -1) {
        this.selected_files.push(file);
      }

      if (this.selected_files.length == 1) {
        var vm = this;
        Vue.nextTick(function () {
          if (vm.fileIs(vm.selected_file, "video")) {
            vm.$refs.videoplayer.load();
          } else if (vm.fileIs(vm.selected_file, "audio")) {
            vm.$refs.audioplayer.load();
          }
        });
      }
    },
    openFile: function (file) {
      const _this = this;
      if (file.type == "folder") {
        this.current_folder += file.name + "/";
        this.getFiles();
      } else if (this.hidden_element) {
        this.addFileToInput(file);
      } else {
        // if (this.fileIs(this.selected_file, "image")) {
        //   //   $("#imagemodal_" + this._uid).modal("show");
        //   console.log("files is images");
        // } else {
        // ...
        console.log("open file");
        // window.open(file.path, "_blank");
        axios({
          url: "/admin/media/get_file",
          method: "get",
          params: {
            path: file.relative_path,
          },
          responseType: "blob",
        }).then((res) => {
          // let filename = res.headers["content-disposition"].split(
          //   "filename="
          // )[1];
          // let name = filename.split(".");
          _this.file_download(
            res.data,
            res.headers["content-disposition"].split("filename=")[1]
          );
        });
        // }
      }
    },
    isFileSelected: function (file) {
      return this.selected_files.includes(file);
    },
    fileIs: function (file, type) {
      if (typeof file === "string") {
        if (type == "image") {
          return this.endsWithAny(
            ["jpg", "jpeg", "png", "bmp"],
            file.toLowerCase()
          );
        }
        //Todo: add other types
      } else {
        return file.type.includes(type);
      }

      return false;
    },
    getCurrentPath: function () {
      var path = this.current_folder
        .replace(this.basePath, "")
        .split("/")
        .filter(function (el) {
          return el != "";
        });

      console.log("currentPath", path);
      return path;
    },
    setCurrentPath: function (i) {
      if (i == -1) {
        this.current_folder = this.basePath;
      } else {
        var path = this.getCurrentPath();
        path.length = i + 1;
        this.current_folder = this.basePath + path.join("/") + "/";
      }

      this.getFiles();
    },
    filter: function (file) {
      if (this.allowedTypes.length > 0) {
        if (file.type != "folder") {
          for (var i = 0, type; (type = this.allowedTypes[i]); i++) {
            if (file.type.includes(type)) {
              return true;
            }
          }
        }
      }

      if (file.type == "folder" && this.showFolders) {
        return true;
      } else if (file.type == "folder" && !this.showFolders) {
        return false;
      }
      if (this.allowedTypes.length == 0) {
        return true;
      }

      return false;
    },
    addFileToInput: function (file) {
      if (file.type != "folder") {
        if (!this.allowMultiSelect) {
          this.hidden_element.value = file.relative_path;
        } else {
          var content = JSON.parse(this.hidden_element.value);
          if (content.indexOf(file.relative_path) !== -1) {
            return;
          }
          if (
            content.length >= this.maxSelectedFiles &&
            this.maxSelectedFiles > 0
          ) {
            var msg_sing =
              "{{ trans_choice('voyager::media.max_files_select', 1) }}";
            var msg_plur =
              "{{ trans_choice('voyager::media.max_files_select', 2) }}";
            if (this.maxSelectedFiles == 1) {
              //   toastr.error(msg_sing);
            } else {
              //   toastr.error(msg_plur.replace("2", this.maxSelectedFiles));
            }
          } else {
            content.push(file.relative_path);
            this.hidden_element.value = JSON.stringify(content);
          }
        }
        // this.$forceUpdate();
      }
    },
    removeFileFromInput: function (path) {
      if (this.allowMultiSelect) {
        var content = JSON.parse(this.hidden_element.value);
        if (content.indexOf(path) !== -1) {
          content.splice(content.indexOf(path), 1);
          this.hidden_element.value = JSON.stringify(content);
          //   this.$forceUpdate();
        }
      } else {
        this.hidden_element.value = "";
      }
    },
    getSelectedFiles: function () {
      if (!this.allowMultiSelect) {
        var content = [];
        if (this.hidden_element.value != "") {
          content.push(this.hidden_element.value);
        }

        return content;
      } else {
        return JSON.parse(this.hidden_element.value);
      }
    },
    renameFile: function (selected_file) {
      var vm = this;
      if (
        !this.allowRename ||
        vm.modals.rename_file.name == selected_file.name
      ) {
        return;
      }
      axios
        .post("/admin/media/rename_file", {
          folder_location: vm.current_folder,
          filename: vm.modals.rename_file.selected.name,
          new_filename: vm.modals.rename_file.name,
        })
        .then((res) => {
          console.log(res);
          vm.modals.rename_file = { show: false, name: "", selected: {} };
          vm.getFiles();
        });
    },
    createFolder: function (e) {
      if (!this.allowCreateFolder) {
        return;
      }
      var vm = this;
      var name = this.modals.new_folder.name;
      axios
        .post("/admin/media/new_folder", {
          new_folder: vm.current_folder + "/" + name,
        })
        .then((data) => {
          console.log(data);
          vm.modals.new_folder = { name: "", show: false };
          vm.getFiles();
          // if (data.success == true) {
          //   // toastr.success('{{ __('voyager::generic.successfully_created') }} ' + name, "{{ __('voyager::generic.sweet_success') }}");
          //   vm.getFiles();
          // } else {
          //   // toastr.error(data.error, "{{'voyager::generic.whoopsie' }}");
          // }
          // vm.modals.new_folder.name = "";
          // $("#create_dir_modal_" + vm._uid).modal("hide");
        });
    },
    deleteFiles: function () {
      if (!this.allowDelete) {
        return;
      }
      var vm = this;
      axios
        .post("/admin/media/delete_file_folder", {
          path: vm.current_folder,
          files: vm.selected_files,
        })
        .then((data) => {
          console.log(data);
          vm.modals.delete_files = { show: false };
          vm.getFiles();
        });
    },

    moveFiles: function (selected_file = []) {
      if (!this.allowMove) {
        return;
      }
      var vm = this;
      var destination = this.modals.move_files.destination;
      if (destination === "") {
        return;
      }
      axios
        .post("/admin/media/move_file", {
          path: vm.current_folder,
          files: selected_file != [] ? vm.selected_files : selected_file,
          destination: destination,
        })
        .then((res) => {
          vm.modals.move_files = { destination: "", show: false };
          vm.getFiles();
        });
    },

    //  downloadFilesZip() {
    //   const vm = this;
    //    axios
    //     .post("/admin/media/download-files-zip", {
    //       folder: vm.current_folder,
    //     })
    //     .then((res) => {
    //       console.log(res);
    //       _this.file_download(
    //         res.data,
    //         res.headers["content-disposition"].split("filename=")[1]
    //       );
    //       // vm.getFiles();
    //     });
    // },
    // crop: function (mode) {
    //   if (!this.allowCrop) {
    //     return;
    //   }
    //   if (!mode) {
    //     if (!window.confirm("Confirmar Crop")) {
    //       return;
    //     }
    //   }

    //   croppedData.originImageName = this.selected_file.name;
    //   croppedData.upload_path = this.current_folder;
    //   croppedData.createMode = mode;

    //   var vm = this;
    //   var postData = Object.assign(croppedData, {
    //     _token: "{{ csrf_token() }}",
    //   });
    //   axios.post("/admin/media/crop", postData, function (data) {
    //     if (data.success) {
    //       //   toastr.success(data.message);
    //       vm.getFiles();
    //       $("#crop_modal_" + vm._uid).modal("hide");
    //     } else {
    //       //   toastr.error(data.error, "{{ __('voyager::generic.whoopsie') }}");
    //     }
    //   });
    // },
    addSelectedFiles: function () {
      var vm = this;
      for (i = 0; i < vm.selected_files.length; i++) {
        vm.openFile(vm.selected_files[i]);
      }
    },
    bytesToSize: function (bytes) {
      var sizes = ["Bytes", "KB", "MB", "GB", "TB"];
      if (bytes == 0) return "0 Bytes";
      var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
      return Math.round(bytes / Math.pow(1024, i), 2) + " " + sizes[i];
    },
    getFileName: function (name) {
      var name = name.split("/");
      return name[name.length - 1];
    },
    imgIcon: function (path) {
      path = path.replace(/\\/g, "/");
      return (
        'background-size: cover; background-image: url("' +
        path +
        '"); background-repeat:no-repeat; background-position:center center;display:inline-block; width:100%; height:100%;'
      );
    },
    dateFilter: function (date) {
      if (!date) {
        return null;
      }
      var date = new Date(date * 1000);

      var month = "0" + (date.getMonth() + 1);
      var minutes = "0" + date.getMinutes();
      var seconds = "0" + date.getSeconds();

      var dateFormated =
        date.getFullYear() +
        "-" +
        month.substr(-2) +
        "-" +
        date.getDate() +
        " " +
        date.getHours() +
        ":" +
        minutes.substr(-2) +
        ":" +
        seconds.substr(-2);

      return dateFormated;
    },
    endsWithAny: function (suffixes, string) {
      return suffixes.some(function (suffix) {
        return string.endsWith(suffix);
      });
    },
    uploadFiles() {
      const vm = this;
      vm.modals.new_files.files.forEach(async (file) => {
        let formData = new FormData();
        formData.append("file", file);
        formData.append("upload_path", vm.current_folder);
        formData.append("filename", vm.filename);
        formData.append("details", JSON.stringify(vm.details));

        await axios.post("/admin/media/upload", formData).then((data) => {
          console.log("files upload", data);
          vm.getFiles();
        });
      });
      vm.modals.new_files = { files: [], show: false };
    },
  },
  mounted: function () {
    this.getFiles();
    var vm = this;

    // if (this.element != "") {
    //   this.hidden_element = document.querySelector(this.element);
    //   if (!this.hidden_element) {
    //     console.error('Element "' + this.element + '" could not be found.');
    //   } else {
    //     if (this.maxSelectedFiles > 1 && this.hidden_element.value == "") {
    //       this.hidden_element.value = "[]";
    //     }
    //   }
    // }

    //Key events
    // this.onkeydown = function (evt) {
    //   evt = evt || window.event;
    //   if (evt.keyCode == 39) {
    //     evt.preventDefault();
    //     for (var i = 0, file; (file = vm.files[i]); i++) {
    //       if (file === vm.selected_file) {
    //         i = i + 1; // increase i by one
    //         i = i % vm.files.length;
    //         vm.selectFile(vm.files[i], evt);
    //         break;
    //       }
    //     }
    //   } else if (evt.keyCode == 37) {
    //     evt.preventDefault();
    //     for (var i = 0, file; (file = vm.files[i]); i++) {
    //       if (file === vm.selected_file) {
    //         if (i === 0) {
    //           i = vm.files.length;
    //         }
    //         i = i - 1;
    //         vm.selectFile(vm.files[i], evt);
    //         break;
    //       }
    //     }
    //   } else if (evt.keyCode == 13) {
    //     evt.preventDefault();
    //     if (evt.target.tagName != "INPUT") {
    //       vm.openFile(vm.selected_file, null);
    //     }
    //   }
    // };
    //Dropzone
    // var dropzone = $(vm.$el).first().find("#upload").first();
    // var progress = $(vm.$el).first().find("#uploadProgress").first();
    // var progress_bar = $(vm.$el)
    //   .first()
    //   .find("#uploadProgress .progress-bar")
    //   .first();
    // if (this.allowUpload && !dropzone.hasClass("dz-clickable")) {
    //   dropzone.dropzone({
    //     timeout: 180000,
    //     url: "/admin/media/upload",
    //     previewsContainer: "#uploadPreview",
    //     totaluploadprogress: function (
    //       uploadProgress,
    //       totalBytes,
    //       totalBytesSent
    //     ) {
    //       progress_bar.css("width", uploadProgress + "%");
    //       if (uploadProgress == 100) {
    //         progress.delay(1500).slideUp(function () {
    //           progress_bar.css("width", "0%");
    //         });
    //       }
    //     },
    //     processing: function () {
    //       progress.fadeIn();
    //     },
    //     sending: function (file, xhr, formData) {
    //       formData.append("_token", "{{ csrf_token() }}");
    //       formData.append("upload_path", vm.current_folder);
    //       formData.append("filename", vm.filename);
    //       formData.append("details", JSON.stringify(vm.details));
    //     },
    //     success: function (e, res) {
    //       if (res.success) {
    //         // toastr.success(
    //         //   res.message,
    //         //   "{{ __('voyager::generic.sweet_success') }}"
    //         // );
    //       } else {
    //         // toastr.error(res.message, "{{ __('voyager::generic.whoopsie') }}");
    //       }
    //     },
    //     error: function (e, res, xhr) {
    //       //   toastr.error(res, "{{ __('voyager::generic.whoopsie') }}");
    //     },
    //     queuecomplete: function () {
    //       vm.getFiles();
    //     },
    //   });
    // }

    //Cropper
    // if (this.allowCrop) {
    //   var cropper = $(vm.$el)
    //     .first()
    //     .find("#crop_modal_" + vm._uid)
    //     .first();
    //   cropper.on("shown.bs.modal", function (e) {
    //     if (typeof cropper !== "undefined" && cropper instanceof Cropper) {
    //       cropper.destroy();
    //     }
    //     var croppingImage = document.getElementById(
    //       "cropping-image_" + vm._uid
    //     );
    //     cropper = new Cropper(croppingImage, {
    //       crop: function (e) {
    //         document.getElementById("new-image-width_" + vm._uid).innerText =
    //           Math.round(e.detail.width) + "px";
    //         document.getElementById("new-image-height_" + vm._uid).innerText =
    //           Math.round(e.detail.height) + "px";
    //         croppedData = {
    //           x: Math.round(e.detail.x),
    //           y: Math.round(e.detail.y),
    //           height: Math.round(e.detail.height),
    //           width: Math.round(e.detail.width),
    //         };
    //       },
    //     });
    //   });
    // }

    // $(document).ready(function () {
    //   $(".form-edit-add").submit(function (e) {
    //     if (vm.hidden_element) {
    //       if (vm.maxSelectedFiles > 1) {
    //         var content = JSON.parse(vm.hidden_element.value);
    //         if (content.length < vm.minSelectedFiles) {
    //           e.preventDefault();
    //           var msg_sing =
    //             "{{ trans_choice('voyager::media.min_files_select', 1) }}";
    //           var msg_plur =
    //             "{{ trans_choice('voyager::media.min_files_select', 2) }}";
    //           if (vm.minSelectedFiles == 1) {
    //             // toastr.error(msg_sing);
    //           } else {
    //             // toastr.error(msg_plur.replace("2", vm.minSelectedFiles));
    //           }
    //         }
    //       } else {
    //         if (vm.minSelectedFiles > 0 && vm.hidden_element.value == "") {
    //           e.preventDefault();
    //           //   toastr.error(
    //           // "{{ trans_choice('voyager::media.min_files_select', 1) }}"
    //           //   );
    //         }
    //       }
    //     }
    //   });

    //Nestable
    //   $("#dd_" + vm._uid).nestable({
    //     maxDepth: 1,
    //     handleClass: "file_link",
    //     collapseBtnHTML: "",
    //     expandBtnHTML: "",
    //     callback: function (l, e) {
    //       if (vm.allowMultiSelect) {
    //         var new_content = [];
    //         var object = $("#dd_" + vm._uid).nestable("serialize");
    //         for (var key in object) {
    //           new_content.push(object[key].url);
    //         }
    //         vm.hidden_element.value = JSON.stringify(new_content);
    //       }
    //     },
    //   });

    //   $("#create_dir_modal_" + vm._uid).on("hidden.bs.modal", function () {
    //     vm.modals.new_folder.name = "";
    //   });

    //   $("#move_files_modal_" + vm._uid).on("hidden.bs.modal", function () {
    //     vm.modals.move_files.destination = "";
    //   });
    // });
  },
  // watch: {
  //   permissionKey(v) {
  //     if (v) this.permissionKey = v.replace(" ", ".").toLowerCase();
  //   },
  // },
};
</script>
<style>
.dd-placeholder {
  flex: 1;
  width: 100%;
  min-width: 200px;
  max-width: 250px;
}
</style>
