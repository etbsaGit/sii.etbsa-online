import { TrackingProspect } from "@admin/router/admin/TrackingProspect";
import { users } from "@admin/router/admin/users";
import { gps } from "@admin/router/admin/gps";
import { vehicles } from "@admin/router/admin/vehicles";
import { purchases } from "@admin/router/admin/purchases";
import { system } from "@admin/router/admin/system";

export const admin = [
  ...TrackingProspect,
  ...users,
  ...gps,
  ...vehicles,
  ...purchases,
  ...system,
];
