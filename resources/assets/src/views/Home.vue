<template>
  <div>
    <h4 class="font-weight-bold py-3 mb-4">Home</h4>
    <p>
      This page is an example of basic layout. For more options use
      <strong>Laravel + Vue.js starter template generator</strong> in the docs.
    </p>
    <p>
      <button class="btn btn-primary btn-lg">Button</button>
    </p>

    <!-- Feed -->
    <b-card no-body class="mb-4">
      <b-card-header header-tag="h6">Feed</b-card-header>
      <b-card-body>
        <div class="media pb-1 mb-3">
          <div class="ui-feed-icon-container">
            <span class="ui-icon ui-feed-icon ion ion-ios-thumbs-up bg-success text-white"></span>
            <img :src="`${publicUrl}img/avatars/7-small.png`" class="ui-w-40 rounded-circle" alt>
          </div>
          <div class="media-body align-self-center ml-3">
            <a href="javascript:void(0)">Alice Hampton</a> liked
            <a href="javascript:void(0)">Article Name</a>
            <div class="text-muted small">2 hours ago</div>
          </div>
        </div>
        <div class="media pb-1 mb-3">
          <div class="ui-feed-icon-container">
            <span class="ui-icon ui-feed-icon ion ion-ios-text bg-secondary text-white"></span>
            <img :src="`${publicUrl}img/avatars/8-small.png`" class="ui-w-40 rounded-circle" alt>
          </div>
          <div class="media-body align-self-center ml-3">
            <a href="javascript:void(0)">Hallie Lewis</a> commented on
            <a href="javascript:void(0)">Article Name</a>
            <div
              class="my-1"
            >Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore.</div>
            <div class="text-muted small">2 hours ago</div>
          </div>
        </div>
        <div class="media pb-1 mb-3">
          <div class="ui-feed-icon-container">
            <span class="ui-icon ui-feed-icon ion ion-ios-thumbs-down bg-danger text-white"></span>
            <img :src="`${publicUrl}img/avatars/9-small.png`" class="ui-w-40 rounded-circle" alt>
          </div>
          <div class="media-body align-self-center ml-3">
            <a href="javascript:void(0)">Amanda Warner</a> disliked
            <a href="javascript:void(0)">Article Name</a>
            <div class="text-muted small">2 hours ago</div>
          </div>
        </div>
        <div class="media">
          <div class="ui-feed-icon-container">
            <span class="ui-icon ui-feed-icon ion ion-md-checkmark bg-primary text-white"></span>
            <img :src="`${publicUrl}img/avatars/11-small.png`" class="ui-w-40 rounded-circle" alt>
          </div>
          <div class="media-body align-self-center ml-3">
            <a href="javascript:void(0)">Belle Ross</a> followed
            <a href="javascript:void(0)">Nelle Maxwell</a>
            <div class="text-muted small">2 hours ago</div>
          </div>
        </div>
      </b-card-body>
      <a
        href="javascript:void(0)"
        class="card-footer d-block text-center text-body small font-weight-semibold"
      >SHOW MORE</a>
    </b-card>
    <!-- / Feed -->
  </div>
</template>

<script>
export default {
  name: "home",
  metaInfo: {
    title: "Home"
  }
};
</script>
