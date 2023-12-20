import { defineStore } from "pinia";
import { api } from "boot/axios";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),
  getters: {
    isLogged: (state) => state.user,
    getUser: (state) => state.user || null,
  },
  actions: {
    login(user) {
      const payload = {
        email: user.email,
        password: user.password,
      };

      api.post("/api/login", user);
      this.user.email = "pau.ubach@gmail.com";
    },
    logout() {
      this.user = {};
    },
  },
});
