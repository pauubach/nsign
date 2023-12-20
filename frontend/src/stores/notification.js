import { defineStore } from "pinia";

export const useNotificationStore = defineStore("notification", {
  state: () => ({
    show: false,
    color: "",
    message: "",
    timeout: 0, // time in milliseconds, recommended to be between 4000 and 10000 (-1 to keep open indefinitely)
    location: "top", // Aligns the component towards the top, bottom, right, left, can be combined like for example top right
  }),

  getters: {
    isVisible: (state) => state.show,
    getMessage: (state) => state.message,
    getColor: (state) => state.color,
    getTimeout: (state) => state.timeout,
    getLocation: (state) => state.location,
  },

  actions: {
    /**
     * Set notification status
     *
     * @param {Boolean} isVisible
     */
    setStatus(isVisible) {
      this.show = isVisible;
    },

    setData({ color, message, timeout = 5000, location = "top" }) {
      this.show = true;
      this.color = color;
      this.message = message;
      this.timeout = timeout;
      this.location = location;
    },

    showNotification(payload) {
      return this.setData(payload);
    },
    success(payload) {
      return this.showNotification({ color: "success", ...payload });
    },
    error(payload) {
      return this.showNotification({ color: "error", ...payload });
    },
    hideNotification() {
      return this.setStatus(false);
    },
  },
});
