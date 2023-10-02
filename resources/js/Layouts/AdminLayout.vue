<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import { ref } from "vue";

defineProps({
  title: String,
});

// data
const selectedLink = ref(0);
const links = ref([
  {
    text: "Dashboard",
    icon: "fa-solid fa-bars",
    path: "",
  },
  {
    text: "Products",
    icon: "fa-solid fa-cubes-stacked",
    path: "",
  },
  {
    text: "Users",
    icon: "fa-solid fa-users",
    path: "",
  },
]);

// methods
const logout = () => {
  router.post(route("logout"));
};

const onHandleSelectedLink = (index) => {
  selectedLink.value = index;
};

const listStyle = (idx, selectedIdx) => {
    return (idx === selectedIdx) ? "active" : null;
}

// hooks
</script>

<template>
  <div class="main">
    <Head :title="title" />
    <div class="sidebar">
      <div class="logo"></div>
      <div class="pages">
        <ul>
          <li :class="listStyle(index, selectedLink)" v-for="(link, index) in links" :key="index" @click.prevent="onHandleSelectedLink(index)">
            <i :class="link.icon"></i>
            <span>{{ link.text }}</span>
          </li>
          <!-- <li>
                    <i class="fa-solid fa-bars"></i>
                    <span>Dashboard</span>
                </li>
                <li>
                    <i class="fa-solid fa-bars"></i>
                    <span>Dashboard</span>
                </li>
                <li>
                    <i class="fa-solid fa-bars"></i>
                    <span>Dashboard</span>
                </li> -->
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
              <li>
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
    <!-- <h1>AdminLayout</h1> -->
    <!-- <form method="POST" @submit.prevent="logout">
      <ResponsiveNavLink as="button"> Log Out </ResponsiveNavLink>
    </form> -->
  </div>
</template>