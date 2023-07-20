import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import ElementUI from "element-ui";
import "element-ui/lib/theme-chalk/index.css";
import locale from "element-ui/lib/locale/lang/en";
import Axios from "axios";

Vue.config.productionTip = false;

Vue.prototype.$http = Axios.create();

Vue.use(ElementUI, { locale });

new Vue({
  router,
  render: (h) => h(App),
}).$mount("#app");
