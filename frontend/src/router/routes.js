import MainLayout from "layouts/MainLayout.vue";
import IndexPage from "pages/IndexPage.vue";
import LoginPage from "pages/LoginPage.vue";
import RegisterPage from "pages/RegisterPage.vue";
import ErrorNotFound from "src/pages/ErrorNotFound.vue";

const routes = [
  {
    path: "/",
    component: MainLayout,
    children: [
      {
        path: "",
        component: IndexPage,
        name: "home",
      },
      {
        path: "login",
        component: LoginPage,
        name: "login",
      },
      {
        path: "register",
        component: RegisterPage,
        name: "register",
      },
    ],
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: "/:catchAll(.*)*",
    component: ErrorNotFound,
  },
];

export default routes;
