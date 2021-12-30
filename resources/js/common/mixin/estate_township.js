export let mixinEstates = {
  data() {
    return {
      estate_id: null,
      township_id: null,
      options: {
        estates: [],
        townships: [],
      },
    };
  },
  watch: {
    estate_id: function (v) {
      this.options.townships = [];
      if (v) this.loadTownships(v);
    },
    "form.estate_id": function (v) {
      this.options.townships = [];
      if (v) this.loadTownships(v);
    },
  },
  methods: {
    async loadEstates() {
      const _this = this;
      await axios.get("/admin/estates").then(function (response) {
        let { estates } = response.data.data;
        _this.options.estates = estates;
      });
    },
    async loadTownships(estate_id) {
      const _this = this;
      if (!estate_id) {
        _this.township_id = [];
        return;
      }
      await axios
        .get(`/admin/townships/${estate_id}`)
        .then(function (response) {
          let { townships } = response.data.data;
          _this.options.townships = townships;
        });
    },
  },
};
