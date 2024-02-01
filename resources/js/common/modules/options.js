import axios from "axios";
const getOptions = async () => {
  const { data } = await axios.get("/admin/resource/options");
  console.log(data.data);
  return data;
};
const resources = {
  namespaced: true,
  state: {
    options: [],
  },
  getters: {
    options: (state) =>
      (state.options = axios.get("/admin/resource/options").then((res) => {
        return res.data;
      })),
  },
  mutations: {},
  actions: {},
};

export default resources;
