import { sales } from "~/router/admin/sales";
import { users } from "~/router/admin/users";
import { gps } from "~/router/admin/gps";
import { vehicles } from "~/router/admin/vehicles";
import { purchases } from "~/router/admin/purchases";
import { system } from "~/router/admin/system";
import { rrhh } from "~/router/admin/rrhh";
import { customers } from "~/router/admin/customers";
import { nt } from "~/router/admin/nt";
import { products } from "~/router/admin/products";
import { cargosInternos } from "~/router/admin/cargosInternos";
import { flujo_efectivo } from "~/router/admin/flujoEfectivo";

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
  ...products,
  ...cargosInternos,
  ...flujo_efectivo,
];
