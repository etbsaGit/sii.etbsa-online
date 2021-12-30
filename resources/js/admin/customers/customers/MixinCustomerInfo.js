export let mixinCustomerInfo = {
  props: {
    customerId: {
      type: Number | String,
      require: true,
    },
    items: {
      type: Array,
      default: () => {
        [];
      },
    },
    dialogForm: {
      type: Boolean,
      default: false,
    },
  },
  computed: {
    dialog: {
      get() {
        return this.dialogForm;
      },
      set(v) {
        if (!v) {
          this.form = { ...this.formDefault };
          this.editedIndex = -1;
          this.$emit("close");
        } else {
          return this.$emit("edit");
        }
      },
    },
  },
  data: () => ({
    valid: true,
    editedIndex: -1,
    form: {},
    formDefault: {},
    headers: [],
  }),
  methods: {
    editItem(item) {
      this.editedIndex = this.items.indexOf(item);
      this.form = Object.assign({}, item);
      this.dialog = true;
    },
    deleteItem(item) {
      const _this = this;
      _this.$store.commit("showDialog", {
        type: "confirm",
        icon: "warning",
        title: "Confirmar Eliminacion",
        message: "¿Esta seguro en eliminar el Registro?",
        okCb: () => {
          const index = _this.items.indexOf(item);
          _this.items.splice(index, 1);
          _this.updateMeta();
        },
        cancelCb: () => {
          console.log("CANCEL");
        },
      });
    },
    submit() {
      const _this = this;
      if (!_this.$refs.form.validate()) return;
      if (_this.editedIndex > -1) {
        Object.assign(_this.items[_this.editedIndex], _this.form);
      } else {
        _this.items.push(_this.form);
      }
      _this.updateMeta();
    },
    async updateMeta() {
      const _this = this;
      await axios
        .post(`/admin/customer/${_this.customerId}/updateMeta`, _this.MetaInfo)
        .then((response) => {
          console.log(response);
          _this.dialog = false;
        });
    },
  },
};
