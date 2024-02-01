export default class UserPolicy {
    static create(user) {
        return user.role === "editor";
    }

    static view(user, users) {
        return true;
    }
    static createUser(user) {
        return user["user.create"] === 1;
    }

    static deleteUser(user) {
        // return user.id === post.user_id;
        return user["user.delete"] === 1;
    }

    static updateUser(user) {
        // return user.id === post.user_id;
        return user["user.edit"] === 1;
    }
    static adminAgency(user) {
        return user["admin.agencies"] === 1;
    }

    static adminGroup(user) {
        // return user.id === post.user_id;
        return user["admin.groups"] === 1;
    }

    static adminPermission(user) {
        // return user.id === post.user_id;
        return user["admin.permissions"] === 1;
    }
}
