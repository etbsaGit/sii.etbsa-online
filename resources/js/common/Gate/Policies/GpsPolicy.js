export default class GpsPolicy {

    static updateGps(user) {
        if (this.isAdmin(user)) {
            return true;
        }
        return user["gps.edit"] === 1;
    }

    static isAdmin(user) {
        return user["gps.admin"] === 1;
    }
}