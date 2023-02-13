<template lang="">
  <v-app-bar color="rgba(0,0,0,0)" flat class="mb-16">
    <v-spacer v-if="Owner"></v-spacer>
    <v-tooltip top v-if="!Owner">
      <template #activator="{ on, attrs }">
        <v-avatar class="mt-n5" size="30" elevation="10" color="pink">
          <span class="white--text overline" v-bind="attrs" v-on="on">
            {{ initials(messageItem.user_name).init }}
          </span>
        </v-avatar>
      </template>
      <span>{{ initials(messageItem.user_name).full_name }}</span>
    </v-tooltip>
    <v-card
      :class="Owner ? 'mt-10 mx-2' : 'mt-10'"
      :color="Owner ? 'blue darken-1' : ''"
      class="overline"
      max-width="350px"
    >
      <v-list-item three-line>
        <v-list-item-content :class="Owner ? 'white--text' : ''">
          {{ messageItem.body.message }}
          <v-list-item-subtitle :class="Owner ? 'white--text' : ''">
            {{ $appFormatters.formatTimeFromNow(messageItem.times) }}
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-card>

    <v-tooltip top v-if="Owner">
      <template #activator="{ on, attrs }">
        <v-avatar class="mt-n5" size="30" elevation="10" color="indigo">
          <span class="white--text overline" v-bind="attrs" v-on="on">
            {{ initials(messageItem.user_name).init }}
          </span>
        </v-avatar>
      </template>
      <span>{{ initials(messageItem.user_name).full_name }}</span>
    </v-tooltip>
  </v-app-bar>
</template>
<script>
export default {
  props: {
    messageItem: {
      require: true,
      type: Object,
    },
  },
  computed: {
    Owner() {
      return this.messageItem.user == this.$gate.auth().id;
    },
  },
  methods: {
    initials(name) {
      var names = name.split(" "),
        initials = names[0].substring(0, 1).toUpperCase();

      if (names.length > 1) {
        initials += names[names.length - 1].substring(0, 1).toUpperCase();
      }
      return {
        init: initials,
        full_name: name,
      };
    },
  },
};
</script>
<style lang=""></style>
