<template>
  <v-card class="mx-auto" flat>
    <v-list subheader two-line dense>
      <v-subheader inset class="title">Cotizacion Adjunta</v-subheader>
      <v-list-item v-for="file in files" :key="file.name">
        <v-list-item-avatar>
          <v-icon class="blue" dark>mdi-file-upload</v-icon>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>{{ file.name }}</v-list-item-title>

          <v-list-item-subtitle>
            {{ file.lastModifiedDate }}
          </v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-action>
          <v-btn icon>
            <v-icon color="grey lighten-1">mdi-information</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list>
  </v-card>
</template>

<script>
export default {
  props: {
    files: {
      type: Array,
      require: true,
    },
  },
  data: () => ({
    file: [
      {
        color: "blue",
        icon: "mdi-clipboard-text",
        subtitle: "Jan 20, 2014",
        title: "Vacation itinerary",
      },
    ],
  }),
};
</script>
