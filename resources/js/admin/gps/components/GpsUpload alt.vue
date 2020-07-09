<template>
  <v-stepper v-model="curr" color="secondary" alt-labels>
    <v-stepper-header class="overflow-x-auto">
      <v-stepper-step
        v-for="(step, n) in steps"
        :key="n"
        :complete="stepComplete(n + 1)"
        :step="n + 1"
        :rules="[value => !!step.valid]"
        :color="stepStatus(n + 1)"
        editable
      >
        {{ step.name }}
      </v-stepper-step>
    </v-stepper-header>
    <v-stepper-content v-for="(step, n) in steps" :step="n + 1" :key="n">
      <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
        <v-file-input
          v-model="files[n]"
          label="File input"
          placeholder="Select your files"
          outlined
          class="pa-2"
          show-size
          chips
          counter
          :rules="step.rules"
          :accept="accept"
        ></v-file-input>
      </v-form>
      <v-btn
        v-if="n + 1 < lastStep"
        color="primary"
        @click="validate(n)"
        :disabled="!step.valid"
        >Continue</v-btn
      >
      <v-btn v-else color="success" @click="done()">Done</v-btn>
      <v-btn text @click="curr = n">Back</v-btn>
    </v-stepper-content>
  </v-stepper>
</template>

<script>
export default {
  data: () => ({
    curr: 1,
    lastStep: 3,
    steps: [
      { name: "File Wialon", rules: [v => !!v || "Required."], valid: true },
      { name: "File GPS", rules: [v => !!v || "Required."], valid: true },
      { name: "Complete" }
    ],
    valid: false,
    stepForm: [],
    files: [],
    accept:
      ".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
  }),
  methods: {
    stepComplete(step) {
      return this.curr > step;
    },
    stepStatus(step) {
      return this.curr > step ? "green" : "blue";
    },
    validate(n) {
      this.steps[n].valid = false;
      let v = this.$refs.stepForm[n].validate();
      if (v) {
        this.steps[n].valid = true;
        // continue to next
        this.curr = n + 2;
      }
    },
    done() {
      this.curr = 5;
    }
  }
};
</script>