<template>

  <div class="class-list">

    <select class="category" @change="changeCat">
      <option value="">--Select A Category--</option>
      <template v-for="category in categories">
        <option value="">{{ category }}</option>
      </template>
    </select>

    <select class="type">
      <option value="">--Select A type--</option>
      <template v-for="type in types">
        <option value="">{{ type }}</option>
      </template>
    </select>

    <div class="selected-cards" v-if="selectedCards.length > 0">
      <SelctedCard v-for="card in selectedCards" :card="card"></SelctedCard>
    </div>

    <div class="all-cards">
      <Card v-for="card in cards" :card="card" :key="card.name" @toggleCheckbox='toggleCheckbox(card)' v-bind:class="{selected: card.selected}"></Card>
    </div>

  </div>

</template>

<script>
import Card from './Card.vue'
import SelctedCard from './SelctedCard.vue'

export default {
  name: 'CardList',
  components: {
    Card,
    SelctedCard
  },

  data () {
    return {
      allCards: [
        {name: 'Card 1', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat3', type: 'type1'},
        {name: 'Card 2', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat2', type: 'type2'},
        {name: 'Card 3', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat3', type: 'type2'},
        {name: 'Card 4', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat3', type: 'type3'},
        {name: 'Card 5', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat2', type: 'type3'},
        {name: 'Card 6', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat3', type: 'type1'},
        {name: 'Card 7', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat2', type: 'type2'},
        {name: 'Card 8', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:'cat1', type: 'type3'}
      ]
    }
  },

  methods: {
    changeCat() {
      alert('changed');
    },

    toggleCheckbox(selectedCard) {
      if (selectedCard.selected) {
        selectedCard.selected = false;
      } else {
        selectedCard.selected = true;
      }
    }
  },

  computed: {

    cards() {
      return this.allCards;
    },

    categories() {

      // Custom function to get unique items from array
      function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
      }

      // Custom function to sort array
      function sortABC(a, b) {
        if(a < b) return -1;
        if(a > b) return 1;
        return 0;
      }

      let returnCat = [];
      for (var i in this.allCards) {
        returnCat.push(this.allCards[i].category);
      }

      returnCat = returnCat.filter( onlyUnique );
      returnCat = returnCat.sort( sortABC );

      return returnCat;
    },

    types() {

      // Custom function to get unique items from array
      function onlyUnique(value, index, self) {
        return self.indexOf(value) === index;
      }

      // Custom function to sort array
      function sortABC(a, b) {
        if(a < b) return -1;
        if(a > b) return 1;
        return 0;
      }

      let types = [];
      for (var i in this.allCards) {
        types.push(this.allCards[i].type);
      }

      types = types.filter( onlyUnique );
      types = types.sort( sortABC );

      return types;
    },

    selectedCards() {
      return this.cards.filter(card => card.selected);
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.all-cards {
  display: flex;
  flex-wrap: wrap;
}
.all-cards .card {
  flex: 0 0 33.33%;
}

.selected-cards {
  display: flex;
  flex-wrap: wrap;
  align-content: center;
  justify-content: center;
  background: #ccc;
  padding: 10px 0;
}
.selected-cards .card {
  flex: 0 0 33.33%;
  max-width: 300px;
}
</style>
