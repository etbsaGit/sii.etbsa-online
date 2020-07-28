export default {
  install(Vue, options) {
    Vue.prototype.$appFormatters = {
      formatDate: function(dateString, format) {
        return moment(dateString).format(format ? format : "MMMDD, YYYY");
      },
      formatTimeFromNow: (dateString) => {
        return moment(dateString).fromNow(true);
      },
      formatTimeDiffNow: (dateString, format) => {
        return moment(dateString).diff(Date.now(), format ? format : "days");
      },
      formatTimeDiffDays: (dateString, end) => {
        return moment(dateString).diff(end, "days");
      },
      formatLetterMonth: (numberMonth) => {
        return moment.months(numberMonth);
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
