<template>
  <div id="card-catalog">
    <CardList v-if="nodeId" :nodeId="nodeId"></CardList>
  </div>
</template>

<script>
import CardList from './components/CardList.vue'

export default {
  name: 'card-catalog',
  components: {
    CardList
  },

  created() {
    let node = document.querySelector('.node.node--type-component-page');

    if(node) {
      this.nodeId = node.dataset.id
    }
  },

  data() {
    return {
      nodeId: null
    }
  }
}
</script>

<style>
#card-catalog {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>
