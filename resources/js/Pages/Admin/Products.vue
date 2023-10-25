<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Modal from "@/Components/Admin/Modal.vue";
import { ref } from "vue";

const showModal = ref(false);
const modalMode = ref("default");
const iconMode = ref({
    add: 'fa-regular fa-square-plus',
    edit: 'fa-solid fa-pen',
    delete: 'fa-solid fa-xmark'
})
const previewImage = ref("");

const openModal = (mode) => {
    modalMode.value = mode
    showModal.value = true
};

const onImagePreview = (e) => {
    const file = e.target.files[0];
    previewImage.value = URL.createObjectURL(file);
}

</script>

<template>
  <AdminLayout title="Products">
    <div class="page">
      <div class="wrapper">
        <button class="addBtn" @click.prevent="openModal('add')">
          <i class="fa-regular fa-square-plus"></i>
          <span>Add Product</span>
        </button>
        <div class="tableContent">
          <table class="styled-table">
            <thead>
              <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Image</th>
                <th>Price</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Yogurt</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>
                  <button class="editBtn">
                    <i class="fa-solid fa-pen"></i>
                  </button>
                  <button class="deleteBtn">
                    <i class="fa-solid fa-xmark"></i>
                  </button>
                </td>
              </tr>
              <tr>
                <td>2</td>
                <td>Biskwit</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Watah</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>4</td>
                <td>Sittsirya</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>5</td>
                <td>Tsokoleyt</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>6</td>
                <td>Nido</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>7</td>
                <td>Bigas</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>8</td>
                <td>Sardinas</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>9</td>
                <td>Seypgard</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
              <tr>
                <td>10</td>
                <td>joy</td>
                <td>Wala pa e</td>
                <td>Mahal</td>
                <td>wait lang</td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="paginationContent">
          <div class="paginationWrapper"></div>
        </div>
      </div>
    </div>
    <Modal v-if="showModal" :mode="modalMode" :imgSrc="previewImage" @submit="showModal = false" @cancel="showModal = false">
        <template #header>
            <span>{{ modalMode }} product</span>
        </template>

        <template #body>
            <div class="inputWrapper">
                <span>Name:</span>
                <div class="input">
                    <input type="text" placeholder="Enter product name" />
                </div>
            </div>
            <div class="inputWrapper">
                <span>Image:</span>
                <div class="file">
                    <input type="file" @change="onImagePreview" />
                </div>
            </div>
            <div class="inputWrapper">
                <span>Price:</span>
                <div class="input">
                    <input type="text" placeholder="Enter price" />
                </div>
            </div>
        </template>

        <template #button-confirm-text>
            <i :class="iconMode[modalMode]"></i>
            {{ modalMode }}
        </template>
    </Modal>
  </AdminLayout>
</template>