<template>
  <transition name="fade">
    <router-view></router-view>
  </transition>
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
