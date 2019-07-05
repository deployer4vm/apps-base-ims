<template>
  <div class="layout-wrapper layout-2">
    <div class="layout-inner">
      <app-layout-navbar />

      <div class="layout-container">
        <app-layout-sidenav />

        <div class="layout-content">
          <div class="router-transitions container-fluid flex-grow-1 container-p-y">
            <router-view />
          </div>

          <app-layout-footer v-if="showFooter" />
        </div>
      </div>
    </div>
    <div class="layout-overlay" @click="closeSidenav"></div>
  </div>
</template>

<script>
import navbar from './navbar'
import sidenav from './sidenav'
import footer from './footer'

export default {
  name: 'app-admin-1',
  components: {
    'app-layout-navbar': navbar,
    'app-layout-sidenav': sidenav,
    'app-layout-footer': footer
  },

  mounted () {
    this.layoutHelpers.init()
    this.layoutHelpers.update()
    this.layoutHelpers.setAutoUpdate(true)
  },

  beforeDestroy () {
    this.layoutHelpers.destroy()
  },
  computed: {
    showFooter() {
      return this.$store.getters.isFooterShowed;
    }
  },
  methods: {
    closeSidenav () {
      this.layoutHelpers.setCollapsed(true)
    }
  }
}
</script>
