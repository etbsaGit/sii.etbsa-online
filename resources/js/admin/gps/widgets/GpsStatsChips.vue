<template>
  <v-chip-group active-class="accent--text">
    <v-chip v-for="tag in tags" :key="tag">
      {{ tag }}
    </v-chip>
  </v-chip-group>
</template>

<script>
export default {
  props: ['month','year'],
  data() {
    return {
      stats: [],
      tooltip: [
        "Total GPS",
        "Renovados",
        "Nuevos",
        "Cancelados",
        "% Renovacion",
      ],
    };
  },
  mounted() {
    axios.get(`/admin/gps/stats/${month}/${year}`).then(function(response) {
      console.log(response);
    });
  },
};
</script>
