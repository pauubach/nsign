import { defineStore } from "pinia";
import { api } from "boot/axios";
import { Notify } from "quasar";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),
  getters: {
    isLogged: (state) => state.user,
    getUser: (state) => state.user || null,
  },
  actions: {
    async login(user) {
      const response = await api
        .post("/api/login_check", user)
        .catch(() =>
          Notify.create({ type: "warning", message: "Datos incorrectos" })
        );
      localStorage.removeItem("nsign-jwt-token");
      localStorage.setItem("nsign-jwt-token", response.data.token);

      const response_user = await api.post(
        "/api/user",
        {},
        { headers: { Authorization: `Bearer ${response.data.token}` } }
      );
      this.user = {
        email: response_user.data.email,
        roles: response_user.data.roles,
      };
    },
    async register(user) {
      const response = await api.post("/api/register", user).catch(() => {
        Notify.create({
          type: "warning",
          message: "Error al dar de alta al usuario",
        });
      });
      if (response) {
        await this.login(user);
      }
    },
    async checkUser() {
      const token = localStorage.getItem("nsign-jwt-token");
      if (!token) {
        return null;
      }
      const response = await api.post(
        "/api/user",
        {},
        { headers: { Authorization: `Bearer ${token}` } }
      );
      this.user = response.data;
      return this.user;
    },
    logout() {
      this.user = null;
      localStorage.clear();
      this.checkUser();
    },
  },
});
