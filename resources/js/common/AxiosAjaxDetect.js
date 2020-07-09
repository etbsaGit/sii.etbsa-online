import store from "~/common/Store";
class AxiosAjaxDetect {
  init(startCb, endCb) {
    let count = 0;

    // Add a request interceptor
    window.axios.interceptors.request.use(
      function(config) {
        count++;

        if (count === 1) startCb();

        return config;
      },
      function(error) {
        return Promise.reject(error);
      }
    );

    // Add a response interceptor
    window.axios.interceptors.response.use(
      function(response) {
        count--;

        if (count === 0) {
          endCb();
        }

        return response;
      },
      function(error) {
        if (error.response.status === 401) {
          window.location.href = "/login";
        }
        
        if (error.response) {
          store.commit("showSnackbar", {
            message: error.response.data.message,
            color: "error",
            duration: 5000,
          });
        } else if (error.request) {
          console.log(error.request);
        } else {
          console.log("Error", error.message);
        }

        count--;
        if (count === 0) {
          endCb();
        }


        return Promise.reject(error);
      }
    );
  }
}

export default new AxiosAjaxDetect();
