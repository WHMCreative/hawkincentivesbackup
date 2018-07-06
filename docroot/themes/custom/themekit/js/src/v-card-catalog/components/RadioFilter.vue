<template>
  <label><span><input type="radio" :name="type" :value="filter" @change="$emit('selectRadio', $event)">{{ typeMap[filter]}}</span></label>
</template>

<script>
export default {
  props: ['filter', 'type'],
  data() {

    return {
      typeMap: {
        'All': 'All Rewards',
        'gift_card': 'Gift Card',
        'omnicode': 'OmniCode',
        'prepaid': 'PrePaid'
      }
    }
  },
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
