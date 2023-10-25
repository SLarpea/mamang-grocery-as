<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { ref } from "vue";

defineProps({
  title: String,
});

// data
const links = ref([
  {
    text: "Dashboard",
    icon: "fa-solid fa-bars",
    path: "admin.dashboard",
  },
  {
    text: "Products",
    icon: "fa-solid fa-cubes-stacked",
    path: "admin.products",
  },
  {
    text: "Users",
    icon: "fa-solid fa-users",
    path: "admin.users",
  },
]);

// methods
const logout = () => {
  Swal.fire({
    title: "Logout",
    text: "Are you sure to logout?",
    icon: "question",
    showConfirmButton: true,
    showCancelButton: true,
    confirmButtonText: 'Logout',
    cancelButtonText: 'Cancel',
    allowOutsideClick: false,
    customClass: 'default'
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "you are logging out...",
        icon: "success",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        allowOutsideClick: false
      }).then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          router.post(route("logout"));
        }
      });
    }
  });
};

const onHandleSelectedLink = (link, index) => {
  router.get(route(link.path));
};

const listStyle = (link, pageTitle) => {
  return link.text === pageTitle ? "active" : null;
};

// hooks
</script>

<template>
  <div class="main">
    <Head :title="title" />
    <div class="sidebar">
      <div class="logo"></div>
      <div class="pages">
        <ul>
          <li
            :class="listStyle(link, title)"
            v-for="(link, index) in links"
            :key="index"
            @click.prevent="onHandleSelectedLink(link)"
          >
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </li>
        </ul>
      </div>
    </div>
    <div class="contentWrapper">
      <div class="header">
        <i class="fa-solid fa-bars"></i>
        <div class="accountWrapper">
          <span>admin</span>
          <div class="accountIcon">
            <i class="fa-solid fa-user"></i>
          </div>
          <div class="accountSettings">
            <ul>
              <li @click.prevent="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div class="content">
        <div class="pageTitle">
          <span>{{ title }}</span>
        </div>
        <main>
          <slot />
        </main>
      </div>
    </div>
  </div>
</template>