<template>
  <label><input type="checkbox" :name="type" :value="filter.key" @change="$emit('selectFilter', $event)">{{ filter.value }}</label>
</template>

<script>
export default {
  props: ['filter', 'type']
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
label {
  text-align: left;
}

</style>
