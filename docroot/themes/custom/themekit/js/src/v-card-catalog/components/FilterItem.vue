<template>
  <label><input type="checkbox" :name="type" :value="filter" @change="$emit('selectFilter', $event)"><span>{{ filter }}</span></label>
</template>

<script>
  export default {
    props: ['filter', 'type'],

    methods: {
      cleanString(str) {
        return str.replace(/[^a-zA-Z ]/g, '').replace(/[^A-Z0-9]/ig, '-').replace('--', '-').toLowerCase();
      }
    }
  };
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
label {
  text-align: left;
}

</style>
