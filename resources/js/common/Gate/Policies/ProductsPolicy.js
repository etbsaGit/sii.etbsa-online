export default class ProductsPolicy {

    
    static isAdmin(user) {
        return user["product.admin"] === 1;
    }
}