import UserPolicy from "~/common/Gate/Policies/UserPolicy";
import GpsPolicy from "~/common/Gate/Policies/GpsPolicy";
import TrackingPolicy from "~/common/Gate/Policies/TrackingPolicy";

export default class Gate {
  constructor(user) {
    this.user = user;

    this.policies = {
      user: UserPolicy,
      gps: GpsPolicy,
      tracking: TrackingPolicy,
    };

    // this.groups = {
    //     group: GroupPolicy
    // }
  }

  before() {
    return this.user.all_permissions["superuser"] === 1;
  }

  allow(action, type, model = null) {
    if (this.before()) {
      return true;
    }

    return this.policies[type][action](this.user.all_permissions, model);
  }

  deny(action, type, model = null) {
    return !this.allow(action, type, model);
  }
}

// bootstrap.js
// import Gate from './Gate';
// Vue.prototype.$gate = new Gate(window.user.combined_permissions);
// v-if="$gate.allow('update', 'post', post)"
