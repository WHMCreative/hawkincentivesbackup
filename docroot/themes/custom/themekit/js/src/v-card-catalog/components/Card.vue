<template>
  <div class="card" @click="$emit('toggleCheckbox')">
    <div class="heart"></div>
    <h2>
      {{ card.name}}
    </h2>
    <p>
      {{ card.category}} {{ card.type}}
    </p>
    <p>
      {{ card.description }}
    </p>
    <input id="checkBox" type="checkbox" :checked="card.selected">
  </div>
</template>

<script>
export default {
  props: ['card']
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

.card:hover{
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
