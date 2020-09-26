export default class TrackingPolicy {

    static assignSeller(user) {
        if (this.isAdmin(user)) {
            return true;
        }
        return user["asignar.vendedor"] === 1;
    }

    static isAdmin(user) {
        return user["tracking.admin"] === 1;
    }
}