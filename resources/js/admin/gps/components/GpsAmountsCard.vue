<template>
  <v-card class="mx-auto" max-width="350">
    <v-card-text>
      <p class="text-h5 text--primary">
        {{ propTitle }}
      </p>
      <div class="text--primary text-h5">
        {{ propAmount | currency() }} {{ propCurrency }}
      </div>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: {
    propTitle: {
      required: true,
    },
    propAmount: {
      type: Number,
      default: 0,
      required: true,
    },
    propCurrency: {
      default: 'MXN',
    },
  },
};
</script>
