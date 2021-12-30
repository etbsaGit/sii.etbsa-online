import UserPolicy from "~/common/Gate/Policies/UserPolicy";
import GpsPolicy from "~/common/Gate/Policies/GpsPolicy";
import TrackingPolicy from "~/common/Gate/Policies/TrackingPolicy";
import VehiclesPolicy from "~/common/Gate/Policies/VehiclesPolicy";
import PurchasePolicy from "~/common/Gate/Policies/PurchasePolicy";
import store from "../Store";

export default class Gate {
  // constructor(user) {
  constructor() {
    // this.user = user;

    this.policies = {
      user: UserPolicy,
      gps: GpsPolicy,
      tracking: TrackingPolicy,
      vehicles: VehiclesPolicy,
      compras: PurchasePolicy,
    };

    // this.groups = {
    //     group: GroupPolicy
    // }
  }

  auth() {
    let { id, name, email } = store.state.user;
    return { id, name, email };
  }

  before() {
    return store.getters["user/permissions"]["superuser"] === 1;
    // return this.user.all_permissions["superuser"] === 1;
  }

  allow(action, type, model = null) {
    if (this.before()) {
      return true;
    }

    return this.policies[type][action](
      store.getters["user/permissions"],
      model
    );
    // return this.policies[type][action](this.user.all_permissions, model);
  }

  deny(action, type, model = null) {
    return !this.allow(action, type, model);
  }
}

// bootstrap.js
// import Gate from './Gate';
// Vue.prototype.$gate = new Gate(window.user.combined_permissions);
// v-if="$gate.allow('update', 'post', post)"
