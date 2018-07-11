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
</style>
