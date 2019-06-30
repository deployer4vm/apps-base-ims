<template>
  <div id="app">
    <router-view/>
  </div>
</template>

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
