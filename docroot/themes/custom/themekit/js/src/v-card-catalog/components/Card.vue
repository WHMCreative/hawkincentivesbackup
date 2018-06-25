<template>
  <div class="card" @click="$emit('selectCard')" v-bind:class="{ selectable: !selected }">
    <div v-show="!selected" class="heart"></div>
    <h2>
      {{ card.name}}
    </h2>
    <p v-show="!selected">
    <template v-for="(category, index) in card.category">
      <template v-if="index == card.category.length - 1">{{ category.value }}</template>
      <template v-else>{{ category.value }}, </template>
    </template>
    <template v-for="(type, index) in card.type">
      <template v-if="index == card.type.length - 1">{{ type.value }}</template>
      <template v-else>{{ type.value }}, </template>
    </template>
    </p>
    <p>
      {{ card.description }}
    </p>
    <input v-show="!selected" id="checkBox" type="checkbox" :checked="card.selected">
  </div>
</template>

<script>
export default {
  props: ['card', 'selected']
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

.heart {
  background-color: #ccc;
  height: 10px;
  transform: rotate(-45deg);
  width: 10px;
  position: absolute;
  top: 10px;
  right: 10px;
}

.heart:before,
.heart:after {
  content: "";
  background-color: #ccc;
  border-radius: 50%;
  height: 10px;
  position: absolute;
  width: 10px;
}

.heart:before {
  top: -5px;
  left: 0;
}

.card {
  min-height: 100px;
  position: relative;
}

.card input[type=checkbox] {
  display: none;
}

.card.selectable:hover{
  cursor: pointer;
  background: #ffcccc;
}

.card:hover .heart,
.card.selected .heart,
.card:hover .heart:before,
.card.selected .heart:before,
.card:hover .heart:after,
.card.selected .heart:after {
  background-color: red;
}

/*.card.selected {*/
  /*background: red;*/
/*}*/

</style>
