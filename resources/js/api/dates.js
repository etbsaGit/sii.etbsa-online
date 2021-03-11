import moment from 'moment';
moment.locale('es');
export const years = (back) => {
  const year = new Date().getFullYear();
  return Array.from({ length: back }, (v, i) => year - back + i + 2);
};

export const months = moment.months();
