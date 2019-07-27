<template>
  <div id="app">
    <transition name="fade">
      <router-view></router-view>
    </transition>
    
    <BlockUI message="" :html="html" v-if="loading" />
    <alert-modal />
    <notifications group="notifications-default" />
    <notifications group="notifications-top-left" position="top left" />
    <notifications group="notifications-top-center" position="top center" />
    <notifications group="notifications-bottom-right" position="bottom right" />
    <notifications group="notifications-bottom-left" position="bottom left" />
    <notifications group="notifications-bottom-center" position="bottom center" />
  </div>
</template>
<style src="@/vendor/libs/vue-notification/vue-notification.scss" lang="scss"></style>
<style src="@/vendor/libs/spinkit/spinkit.scss" lang="scss"></style>
<style>
.loading-container .loading-backdrop {
  opacity: 0.1 !important;
}
.loading-container .loading {
  box-shadow: none !important;
  background-color: transparent !important;
}

.v-fade-enter-active, .v-fade-leave-active {
  transition: opacity .2s;
}
.v-fade-enter, .v-fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
  opacity: 0;
}
</style>
<script>
import BlockUI from 'node_modules/vue-blockui';
Vue.use(BlockUI);
export default {
  name: 'app',
  metaInfo() {
    return {
      title: 'Home',
      titleTemplate: '%s - ' + this.$store.state.template.admin.title
    }
  },
  data: () => ({
    html: `<div class="sk-circle sk-primary">
              <div class="sk-circle1 sk-child"></div>
              <div class="sk-circle2 sk-child"></div>
              <div class="sk-circle3 sk-child"></div>
              <div class="sk-circle4 sk-child"></div>
              <div class="sk-circle5 sk-child"></div>
              <div class="sk-circle6 sk-child"></div>
              <div class="sk-circle7 sk-child"></div>
              <div class="sk-circle8 sk-child"></div>
              <div class="sk-circle9 sk-child"></div>
              <div class="sk-circle10 sk-child"></div>
              <div class="sk-circle11 sk-child"></div>
              <div class="sk-circle12 sk-child"></div>
            </div>`
  }),
  computed: {
    loading() {
      return this.$store.state.template.showLoading;
    }
  },
  updated () {
    // Remove loading state
    setTimeout(() => document.body.classList.remove('app-loading'), 1);
  },
  methods: {
    appendCSS(){
      let file = document.createElement('link');
      file.lang = 'scss';
      file.href = 'myfile.css';
      document.head.appendChild(file);
    }
  }
}
</script>
