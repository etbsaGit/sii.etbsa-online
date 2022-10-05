<template>
  <v-card flat color="transparent">
    <v-slide-x-transition>
      <router-view></router-view>
    </v-slide-x-transition>
  </v-card>
</template>

<script>
export default {
  name: "Products",
  data() {
    return {};
  },
  computed: {},
};
</script>
