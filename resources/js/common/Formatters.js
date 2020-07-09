export default {
  install(Vue, options) {
    Vue.prototype.$appFormatters = {
      formatDate: function(dateString, format) {
        return moment(dateString).format(format ? format : "MMMDD, YYYY");
      },
      formatTimeFromNow: (due) => {
        return moment(due).fromNow(true);
      },
      formatTimeDiffNow: (due, format) => {
        return moment(due).diff(Date.now(), format ? format : "days");
      },
      formatTimeDiffDays: (start, end) => {
        return moment(start).diff(end, "days");
      },
      formatByteToMB(sizeInBytes) {
        return (sizeInBytes / (1024 * 1024)).toFixed(2);
      },
      formatMbToBytes(mb) {
        return (mb * 1048576).toFixed(2);
      },
    };
  },
};
