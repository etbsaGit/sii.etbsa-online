<template>
  <v-container fluid>
    <transition name="fade">
      <router-view></router-view>
    </transition>
  </v-container>
</template>

<script>
export default {
  data() {
    return {
      active: "",
    };
  },
};
</script>
