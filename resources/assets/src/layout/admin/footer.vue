<template>
  <nav class="layout-footer footer" :class="getLayoutFooterBg()">
    <div
      class="container-fluid d-flex flex-wrap justify-content-between text-center container-p-x pb-3"
    >
      <div class="pt-3">
        <span class="footer-text font-weight-bolder">{{ footerText }}</span>
      </div>
      <div>
        <template v-for="menu in footerMenu">
          <router-link
            tag="a"
            :to="menu.route"
            class="footer-link pt-3 ml-4"
            v-bind:key="menu.caption"
            v-if="menu.show"
          >{{ menu.caption }}</router-link>
        </template>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "app-layout-footer",

  computed: {
    footerText() {
      return this.$store.getters.getFooterText;
    },
    footerMenu() {
      return this.$store.getters.getFooterMenu;
    }
  },
  methods: {
    getLayoutFooterBg() {
      return `bg-${this.layoutFooterBg}`;
    }
  }
};
</script>
