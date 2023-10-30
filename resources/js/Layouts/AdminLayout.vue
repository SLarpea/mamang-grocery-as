<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { ref } from "vue";

defineProps({
  title: String,
});

// data
const nav = ref(true);
const links = ref([
  {
    text: "Dashboard",
    icon: "fa-solid fa-bars",
    path: "admin.dashboard",
  },
  {
    text: "Products",
    icon: "fa-solid fa-cubes-stacked",
    path: "products.index",
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
    confirmButtonText: "Logout",
    cancelButtonText: "Cancel",
    allowOutsideClick: false,
    customClass: "default",
  }).then((result) => {
    if (result.isConfirmed) {
      Swal.fire({
        title: "you are logging out...",
        icon: "success",
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        allowOutsideClick: false,
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
  <v-layout>
    <Head :title="title" />
    <v-navigation-drawer v-model="nav" color="primary-bg-color">
      <v-list-item title="Mamang Grocery" subtitle="An E-commerce Grocery Store" height="64"></v-list-item>
      <hr>
      <ul>
        <Link
          v-for="(link, index) in links"
          :key="index"
          :href="route(link.path)"
          :class="['link', { active: link.text === title }]"
          method="get"
        >
          <i :class="link.icon"></i>
          <span>{{ link.text }}</span>
        </Link>
      </ul>
      <template v-slot:append>
        <hr>
        <div class="nav-footer">
          <span>&copy; Copyright 2023 <strong>Mamang Grocery</strong></span>
          <span>version 1.0</span>
        </div>
      </template>
    </v-navigation-drawer>

    <v-app-bar color="primary-bg-color">
      <v-app-bar-nav-icon @click="nav = !nav"></v-app-bar-nav-icon>
      <v-app-bar-title>{{ title }}</v-app-bar-title>
      <template v-slot:append>
        <v-menu>
          <template v-slot:activator="{ props }">
            <v-btn icon="fa-solid fa-user" v-bind="props"></v-btn>
          </template>
          <v-sheet height="auto" :width="150" style="padding: .5rem 0">
            <ul>
              <li class="logout" @click="logout">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
              </li>
            </ul>
          </v-sheet>
        </v-menu>
      </template>
    </v-app-bar>

    <v-main style="min-height: 100vh;background: #E0E0E0">
      <slot />
    </v-main>
  </v-layout>
  <!-- <div class="main">
    <Head :title="title" />
    <div class="sidebar">
      <div class="logo"></div>
      <div class="pages">
        <ul>
          <Link
          v-for="(link, index) in links"
          :key="index"
          :href="route(link.path)"
          :class="['link', { 'active': link.text === title }]"
          method="get"
          >
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </Link>
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
  </div> -->
</template>