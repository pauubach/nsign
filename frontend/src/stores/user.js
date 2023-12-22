import { defineStore } from "pinia";
import { api } from "boot/axios";
import { Notify, Loading } from "quasar";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),
  getters: {
    isLogged: (state) => state.user,
    isAdmin: (state) => state.user && state.user.roles.includes("ROLE_ADMIN"),
  },
  actions: {
    async login(user) {
      Loading.show();
      const response = await api
        .post("/api/login_check", user)
        .catch(() =>
          Notify.create({ type: "warning", message: "Datos incorrectos" })
        )
        .finally(() => Loading.hide());
      localStorage.removeItem("nsign-jwt-token");
      localStorage.setItem("nsign-jwt-token", response.data.token);

      Loading.show();
      const response_user = await api
        .post(
          "/api/user",
          {},
          { headers: { Authorization: `Bearer ${response.data.token}` } }
        )
        .finally(() => Loading.hide());
      this.user = {
        email: response_user.data.email,
        roles: response_user.data.roles,
      };
    },
    async register(user) {
      Loading.show();
      const response = await api
        .post("/api/register", user)
        .catch(() => {
          Notify.create({
            type: "warning",
            message: "Error al dar de alta al usuario",
          });
        })
        .finally(() => Loading.hide());
      if (response) {
        await this.login(user);
      }
    },
    async checkUser() {
      const token = localStorage.getItem("nsign-jwt-token");
      if (!token) {
        return null;
      }

      Loading.show();
      const response = await api
        .post(
          "/api/user",
          {},
          { headers: { Authorization: `Bearer ${token}` } }
        )
        .catch(() => {})
        .finally(() => Loading.hide());
      if (response) {
        this.user = response.data;
        return this.user;
      }
    },
    logout() {
      this.user = null;
      localStorage.clear();
      this.checkUser();
    },
  },
});
