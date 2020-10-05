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
    estate_id: function(v) {
      this.options.townships = [];
      // this.township_id = null;
      if (v) this.loadTownships(this.estate_id);
    },
  },
  methods: {
    loadEstates(cb) {
      const self = this;
      axios.get("/admin/estates").then(function(response) {
        let Resource = response.data.data;
        self.options.estates = Resource.estates;
        cb();
      });
    },
    loadTownships(id) {
      if (!id) {
        this.township_id = null;
        return;
      }
      const self = this;
      axios.get("/admin/townships/" + id).then(function(response) {
        let Resource = response.data.data;
        self.options.townships = Resource.townships;
        cb();
      });
    },
  },
};
