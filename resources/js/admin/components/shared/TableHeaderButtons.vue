<template>
  <div class="py-2">
    <base-tooltip v-if="updateSearchPanel" :text="`Busqueda avanzada`">
      <template #activator="{ on }">
        <v-btn icon v-on="on" @click.native.stop="updateSearchPanel">
          <v-icon>mdi-magnify</v-icon>
        </v-btn>
      </template>
    </base-tooltip>

    <v-btn icon @click="print">
      <v-icon>mdi-printer</v-icon>
    </v-btn>

    <v-btn v-if="exportTable" icon @click.native.stop="exportTable">
      <v-icon color="green">mdi-file-excel</v-icon>
    </v-btn>

    <v-btn icon @click.native.stop="reloadTable">
      <v-icon>mdi-reload</v-icon>
    </v-btn>
  </div>
</template>
<script>
import BaseTooltip from "../Base/BaseTooltip.vue";
export default {
  components: { BaseTooltip },
  props: {
    updateSearchPanel: {
      type: Function,
      require: true,
    },
    reloadTable: {
      type: Function,
      require: true,
    },
    exportTable: {
      type: Function,
      require: false,
    },
  },
  methods: {
    print() {
      window.print();
    },
  },
};
</script>
