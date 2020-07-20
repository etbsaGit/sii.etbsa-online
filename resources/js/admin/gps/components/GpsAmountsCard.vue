<template>
  <v-card class="mx-auto" max-width="350">
    <v-card-text>
      <p class="display-1 text--primary">
        {{ propTitle }}
      </p>
      <div class="text--primary text-h4">
        {{ propAmount | money() }} MXN
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: {
    propTitle: {
      required: true
    },
    propAmount: {
      type: Number,
      default: 0,
      required: true
    }
  }
}
</script>
