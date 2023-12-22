import { defineStore } from "pinia";
import { api } from "boot/axios";

export const useProductsStore = defineStore("products", {
  state: () => ({
    products: null,
  }),
  getters: {
    getAll: (state) => state.products || null,
  },
  actions: {
    async getProducts() {
      const response = await api
        .post(
          "/api/list",
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem(
                "nsign-jwt-token"
              )}`,
            },
          }
        )
        .catch(() =>
          Notify.create({
            type: "warning",
            message: "No se han podido cargar los productos",
          })
        );
      this.products = response.data;
      this.getImages();
    },
    getImages() {
      this.products.forEach(async (product) => {
        if (product.image) {
          console.log(product.image);
          const response = await api
            .post(
              "/api/image",
              { file: product.image },
              {
                responseType: "blob",
                headers: {
                  Authorization: `Bearer ${localStorage.getItem(
                    "nsign-jwt-token"
                  )}`,
                },
              }
            )
            .catch(() => {});
          console.log(response.data);

          const urlCreator = window.URL || window.webkitURL;
          const imageUrl = urlCreator.createObjectURL(response.data);
          product.imageFile = imageUrl;
        }
      });
    },
  },
});
