export default class PurchasePolicy {
  static validPurchase(user) {
    return user["compras.validar"] === 1;
  }

  static rejectPurchase(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["compras.rechazar"] === 1;
  }

  static authorizePurchase(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["compras.autorizar"] === 1;
  }

  static shedulePaymentDate(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["compras.programar.pago"] === 1;
  }

  static markAsPaidIvoice(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["factura.pagada"] === 1;
  }

  static puchaseInvoice(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["compras.asignar.factura"] === 1;
  }

  static createSupplier(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["proveedor.crear"] === 1;
  }

  static editSupplier(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["proveedor.editar"] === 1;
  }

  static activeSupplier(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["proveedor.activar"] === 1;
  }

  static isAdmin(user) {
    return user["compras.admin"] === 1;
  }
}
