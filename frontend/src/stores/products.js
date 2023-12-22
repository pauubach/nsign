import { defineStore } from "pinia";
import { api } from "boot/axios";
import { Notify, Loading } from "quasar";

export const useProductsStore = defineStore("products", {
  state: () => ({
    products: [],
    categories: [],
  }),

  getters: {
    getAll: (state) => state.products || null,
  },

  actions: {
    async getProducts(complete = true) {
      if (complete) {
        Loading.show();
        this.getCategories();
      }
      const response = await api
        .post(
          "/api/product",
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
      if (complete) {
        this.getImages();
        Loading.hide();
      }
    },

    async getCategories() {
      const response = await api
        .post(
          "/api/categories",
          {},
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem(
                "nsign-jwt-token"
              )}`,
            },
          }
        )
        .catch(() => {});
      this.categories = response.data;
    },

    getImages() {
      this.products.forEach(async (product) => {
        if (product.image) {
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

          const urlCreator = window.URL || window.webkitURL;
          const imageUrl = urlCreator.createObjectURL(response.data);
          product.imageFile = imageUrl;
        }
      });
    },

    addCategory(name) {
      if (!this.categories.find((category) => category.name === name)) {
        this.categories.push({ name });
      }
    },

    async deleteProducts(ids) {
      this.products = this.products.filter(
        (product) => !ids.includes(product.id)
      );
      await api
        .post(
          "/api/product/delete",
          { id: ids },
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem(
                "nsign-jwt-token"
              )}`,
            },
          }
        )
        .catch(() => {
          Notify.create({
            type: "warning",
            message: "No se ha podido borrar el producto",
          });
          return false;
        });
      this.getProducts(false);
    },

    async addProduct(product) {
      const formData = new FormData();
      formData.append("id", product.id);
      formData.append("title", product.title);
      formData.append("category", product.category.name);
      formData.append("status", product.status);
      formData.append("price", product.price);
      formData.append("image", product.image);

      await api.post("/api/product/create", formData, {
        headers: {
          "Content-type": "multipart/form-data",
          Authorization: `Bearer ${localStorage.getItem("nsign-jwt-token")}`,
        },
      });
      this.getProducts();
    },
  },
});
