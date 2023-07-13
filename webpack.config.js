const path = require("path");

module.exports = {
  resolve: {
    alias: {
      "~": path.resolve("resources/js"),
      "@admin": path.resolve("resources/js/admin"),
      "@validator": path.resolve("resources/js/common/utils/validators.js"),
    },
  },
};