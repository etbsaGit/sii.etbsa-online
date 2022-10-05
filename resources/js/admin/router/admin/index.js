import { sales } from "@admin/router/admin/sales";
import { users } from "@admin/router/admin/users";
import { gps } from "@admin/router/admin/gps";
import { vehicles } from "@admin/router/admin/vehicles";
import { purchases } from "@admin/router/admin/purchases";
import { system } from "@admin/router/admin/system";
import { rrhh } from "@admin/router/admin/rrhh";
import { customers } from "@admin/router/admin/customers";
import { nt } from "@admin/router/admin/nt";
import { products } from "@admin/router/admin/products";

export const admin = [
  ...sales,
  ...users,
  ...gps,
  ...vehicles,
  ...purchases,
  ...system,
  ...rrhh,
  ...customers,
  ...nt,
  ...products
];
