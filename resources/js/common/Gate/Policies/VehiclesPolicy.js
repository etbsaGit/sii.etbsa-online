export default class VehiclesPolicy {
  static canAutorizar(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return false;
  }

  static canDispersar(user) {
    return user["flotilla.dispersar"] === 1;
  }
  static canAsignar(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["flotilla.asignar"] === 1;
  }

  static isAdmin(user) {
    return user["flotilla.admin"] === 1;
  }
}
