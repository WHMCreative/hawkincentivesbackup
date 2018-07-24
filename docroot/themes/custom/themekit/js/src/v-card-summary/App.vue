<template>
  <div id="card-summary">
    <CardSummaryList v-if="nodeId" :nodeId="nodeId"></CardSummaryList>
  </div>
</template>

<script>
import CardSummaryList from './components/CardSummaryList.vue'

export default {
  name: 'card-summary',
  components: {
    CardSummaryList
  },

  created() {
    let node = document.querySelector('.node.node--type-component-page');

    if (node) {
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
