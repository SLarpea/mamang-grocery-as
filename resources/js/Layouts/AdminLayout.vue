<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { onMounted, reactive, ref } from "vue";

defineProps({
  title: String,
});

onMounted(() => pageIndex());

// data
const nav = ref(true);
const links = reactive([
  {
    text: "Dashboard",
    icon: "fas fa-th-large",
    path: "admin.dashboard",
  },
  {
    text: "Products",
    icon: "fa-solid fa-cubes-stacked",
    path: "products.index",
  },
  {
    text: "Categories",
    icon: "fa-solid fa-tags",
    path: "categories.index",
  },
  {
    text: "Users",
    icon: "fa-solid fa-users",
    path: "admin.users",
  },
  {
    text: "Orders",
    icon: "fa-solid fa-cart-flatbed",
    path: "admin.users",
  },
  {
    text: "Deliveries",
    icon: "fa-solid fa-truck",
    path: "admin.users",
  },
  {
    text: "Transactions",
    icon: "fa-solid fa-coins",
    path: "admin.users",
  },
  {
    text: "Top Sales",
    icon: "fa-solid fa-ranking-star",
    path: "admin.users",
  },
  {
    text: "Viewed Items",
    icon: "fa-solid fa-magnifying-glass-chart",
    path: "admin.users",
  },
]);

const pageIndex = (pageTitle) => {
  return links.map((link) => link.text).indexOf(pageTitle);
};

const { length, 0: firstLink } = links;
const settingsIdxStart = pageIndex("Products");
const settlementsIdxStart = pageIndex("Orders");
const reportsIdxStart = pageIndex("Top Sales");

const settingLinks = links.slice(settingsIdxStart, settlementsIdxStart);
const settlementLinks = links.slice(settlementsIdxStart, reportsIdxStart);
const reportLinks = links.slice(reportsIdxStart);

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
      <v-list-item
        title="Mamang Grocery"
        subtitle="An E-commerce Grocery Store"
        height="64"
      ></v-list-item>
      <hr />
      <v-list bg-color="primary-bg-color">
        <ul>
          <Link
            :href="route(firstLink.path)"
            :class="['link', { active: firstLink.text === title }]"
            method="get"
          >
            <i :class="firstLink.icon"></i>
            <span>{{ firstLink.text }}</span>
          </Link>
        </ul>
        <v-list-subheader>Settings</v-list-subheader>
        <ul>
          <Link
            v-for="(link, index) in settingLinks"
            :key="index"
            :href="route(link.path)"
            :class="['link', { active: link.text === title }]"
            method="get"
          >
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </Link>
        </ul>
      </v-list>
      <v-list bg-color="primary-bg-color">
        <v-list-subheader>Settlements</v-list-subheader>
        <ul>
          <Link
            v-for="(link, index) in settlementLinks"
            :key="index"
            :href="route(link.path)"
            :class="['link', { active: link.text === title }]"
            method="get"
          >
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </Link>
        </ul>
      </v-list>
      <v-list bg-color="primary-bg-color">
        <v-list-subheader>Reports</v-list-subheader>
        <ul>
          <Link
            v-for="(link, index) in reportLinks"
            :key="index"
            :href="route(link.path)"
            :class="['link', { active: link.text === title }]"
            method="get"
          >
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </Link>
        </ul>
      </v-list>
      <template v-slot:append>
        <hr />
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
          <v-sheet height="auto" :width="150" style="padding: 0.5rem 0">
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

    <v-main style="min-height: 100vh; background: #e0e0e0">
      <slot />
    </v-main>
  </v-layout>
</template>