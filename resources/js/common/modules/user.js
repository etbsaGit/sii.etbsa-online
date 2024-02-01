const { profiable_id, profiable_type, profiable } = window.LSK_APP.AUTH_USER;
const user = {
  namespaced: true,
  state: {
    ...window.LSK_APP.AUTH_USER,
  },

  getters: {
    // name: (state) => (state.profiable ? state.profiable.full_name : state.name),
    name: (state) => (state.profile ? state.profile.full_name : state.name),
    permissions: (state) => state.all_permissions,
    user: (state) => state.email,
    user_id: (state) => state.id,
    groups: (state) => state.groups,
    avatar: (state) => state.avatar,
    status: (state) => !!state.active,
    photo: (state) => (state.profile ? state.profile.profile_photo_url : false),
  },

  mutations: {},

  actions: {},

  modules: {
    // inherits the namespace from parent module
    account: {
      namespaced: true,
      state: () => ({
        profiable_id,
        profiable_type,
        profiable,
      }),
      getters: {
        id: (state) => state.profiable_id,
        profile: (state) => state.profiable || {}, // -> getters['user/profile']
        route() {},
      },
    },
  },
};

export default user;
