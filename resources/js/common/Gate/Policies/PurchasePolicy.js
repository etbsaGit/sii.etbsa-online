export default class PurchasePolicy {
  static validPurchase(user) {
    return user["compras.validar"] === 1;
  }

  static authorizePurchase(user) {
    if (this.isAdmin(user)) {
      return true;
    }
    return user["compras.autorizar"] === 1;
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

  static isAdmin(user) {
    return user["compras.admin"] === 1;
  }
}
