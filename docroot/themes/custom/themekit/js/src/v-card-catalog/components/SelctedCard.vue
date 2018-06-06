<template>
  <div class="card selected-card">
    <h2>
      {{ card.name}}
    </h2>
    <p>
      {{ card.description }}
    </p>
  </div>
</template>

<script>
export default {
  props: ['card']
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.card {
  min-height: 50px;
  position: relative;
}


</style>
