<template>
  <v-card>
    <v-card-title color="grey lighten-3">
      Crear Seguimiento
      <v-spacer />
      <v-btn text color="error"> Cancelar </v-btn>
      <v-btn text color="primary"> Crear </v-btn>
    </v-card-title>

    <!-- <v-stepper v-model="e1" non-linear outlined>
      <v-stepper-header>
        <v-stepper-step
          step="1"
          :complete="e1 > 1"
          editable
          :rules="Rules.stepDetail"
        >
          Detalle
        </v-stepper-step>

        <v-divider></v-divider>

        <v-stepper-step step="2" editable>
          Prospecto
          <small> A quien va dirigido </small>
        </v-stepper-step>

        <v-divider></v-divider>

        <v-stepper-step step="3" editable>
          Referencia
        </v-stepper-step>
      </v-stepper-header>
      <v-stepper-items>
        <v-stepper-content step="1" class="pa-0">
          <v-card
            class="overflow-y-auto"
            color="grey lighten-3"
            max-height="400"
            light
          >
            <v-card-text>
              <tracking-form-step-detail
                ref="step_detail"
              ></tracking-form-step-detail>
            </v-card-text>
            <v-card-actions class="mb-4">
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="e1 = 2">Continuar</v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="2" class="pa-0">
          <v-card class="overflow-y-auto" max-height="400">
            <v-card-text>
              <tracking-form-step-detail></tracking-form-step-detail>
            </v-card-text>
            <v-card-actions class="mb-4">
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="e1 = 3">Continuar</v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>

        <v-stepper-content step="3" class="pa-0">
          <v-card class="overflow-y-auto" max-height="400">
            <v-card-text>
              <tracking-form-step-detail></tracking-form-step-detail>
            </v-card-text>
            <v-card-actions class="mb-4">
              <v-spacer></v-spacer>
              <v-btn color="primary" @click="e1 = 1">Continuar</v-btn>
            </v-card-actions>
          </v-card>
        </v-stepper-content>
      </v-stepper-items>
    </v-stepper> -->

    <v-stepper v-model="curr" color="green" non-linear>
      <v-stepper-header class="overflow-x-auto">
        <v-stepper-step
          v-for="(step, n) in steps"
          :key="n"
          :complete="stepComplete(n + 1)"
          :step="n + 1"
          :rules="[(value) => !!step.valid]"
          :color="stepStatus(n + 1)"
          editable
        >
          {{ step.name }}
        </v-stepper-step>
      </v-stepper-header>
      <v-stepper-content v-for="(step, n) in steps" :step="n + 1" :key="n">
        {{ step.name }}
        <v-card color="grey lighten-1" class="mb-12" height="200px">
          <v-card-text>
            <v-form :ref="'stepForm'" v-model="step.valid" lazy-validation>
              <v-text-field
                label="Enter something..."
                :rules="step.rules"
              ></v-text-field>
            </v-form>
          </v-card-text>
        </v-card>
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
  </v-card>
</template>
<script>
import TrackingFormStepDetail from "./TrackingFormStepDetail.vue";
export default {
  components: { TrackingFormStepDetail },
  data() {
    return {
      curr: 1,
      lastStep: 4,
      steps: [
        { name: "Start", rules: [(v) => !!v || "Required."], valid: true },
        { name: "Step 2", rules: [(v) => !!v || "Required."], valid: true },
        {
          name: "Step 3",
          rules: [
            (v) => (v && v.length >= 4) || "Enter at least 4 characters.",
          ],
          valid: true,
        },
        { name: "Complete" },
      ],
      valid: false,
      stepForm: [],
    };
  },
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
    },
  },
};
</script>
<style></style>
