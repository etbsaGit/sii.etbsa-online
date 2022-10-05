<template>
  <v-card>
    <v-card-title>Estadisticas CRM</v-card-title>
    <v-card-subtitle class="py-4">
      <v-select label="Vendedor" outlined></v-select>
      <v-select label="sucursal" outlined></v-select>
    </v-card-subtitle>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4" v-for="index in 3" :key="index">
          <v-card :class="'cyan darken-3'" dark elevation="4">
            <v-list-item>
              <v-list-item-avatar>
                <v-icon
                  :class="['amber white--text']"
                  v-text="'mdi-checkbox-marked'"
                ></v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-subtitle
                  v-text="'Seguimientos Activos'"
                ></v-list-item-subtitle>
                <br />
                <v-list-item-title v-text="'100'"></v-list-item-title>
              </v-list-item-content>

              <v-list-item-action>
                <v-btn icon>
                  <v-icon color="grey lighten-3">mdi-information</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>
<script>
export default {
  name: "StatTracking",
};
</script>
<style></style>
