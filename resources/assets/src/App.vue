<template>
  <div id="app">
    <router-view/>
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
<script>
export default {
  name: 'app',
  metaInfo() {
    return {
      title: 'Home',
      titleTemplate: '%s - ' + this.$store.state.template.admin.title
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
