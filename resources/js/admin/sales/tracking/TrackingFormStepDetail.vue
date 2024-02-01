<template>
  <v-form v-model="valid" ref="form" lazy-validation>
    <v-row>
      <v-col cols="12">
        <v-text-field
          outlined
          solo
          filled
          dense
          label="Label"
          :rules="rules.required"
        ></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field outlined solo filled dense label="Label"></v-text-field>
      </v-col>
      <v-col cols="6">
        <v-text-field outlined solo filled dense label="Label"></v-text-field>
      </v-col>
      <v-col cols="12">
        <v-textarea outlined solo filled dense label="Label"></v-textarea>
      </v-col>
      <v-col cols="12">
        <v-select outlined solo filled dense label="Label"></v-select>
      </v-col>
      <v-col cols="12">
        <v-select outlined solo filled dense label="Label"></v-select>
      </v-col>
    </v-row>
  </v-form>
</template>
<script>
export default {
  data() {
    return {
      valid: true,
      rules: {
        required: [(v) => !!v || "Requerido"],
      },
    };
  },
};
</script>
<style></style>
