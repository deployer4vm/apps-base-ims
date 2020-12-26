<template>
  <div>
    <h4 class="d-flex justify-content-between align-items-center w-100 py-2 mb-4">
      <div v-html="headerTitle"></div>
      <router-link class="btn btn-success rounded-pill btn-sm d-block" :to="{name: 'user.add'}">
        <span class="ion ion-md-add"></span>&nbsp; Tambah User
      </router-link>
    </h4>
    <hr class="container-m-nx border-light mt-0 mb-4" />
  </div>
</template>
<script>
export default {
  name: "syncomponent-page-header",
  props: ["headerTitle"]
};
</script>
