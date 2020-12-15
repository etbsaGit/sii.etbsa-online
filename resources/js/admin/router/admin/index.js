import { tracking } from "@admin/router/admin/tracking";
import { users } from "@admin/router/admin/users";
import { gps } from "@admin/router/admin/gps";
import { vehicles } from "@admin/router/admin/vehicles";

export const admin = [...tracking, ...users, ...gps,...vehicles];
