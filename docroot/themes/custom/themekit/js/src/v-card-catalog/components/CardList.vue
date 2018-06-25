<template>

  <div class="card-catalog">

    <!-- Begin Selected Cards -->
    <div class="selected-cards" v-if="selectedCards.length > 0">
      <Card v-for="card in selectedCards" :card="card" v-bind:selected='true'></Card>
    </div>
    <!-- End Selected Cards -->

    <div class="card-list">
    <!-- Begin Filters -->
      <div class="filters">

        <div class="filter filter--category" v-if="categories.length > 0">
          <div class="filter-label">Catgory</div>
          <FilterItem v-for="category in categories" :filter="category" :type="'category'" @selectFilter='selectFilter'></FilterItem>
        </div>

        <div class="filter filter--type" v-if="types.length > 0">
          <div class="filter-label">Type</div>
          <FilterItem v-for="type in types" :filter="type" :type="'type'" @selectFilter='selectFilter'></FilterItem>
        </div>
      </div>
      <!-- End Filters -->

      <div class="cards">
        <!-- Begin ALl Cards -->
        <div class="all-cards">
          <Card v-for="card in cards" :card="card" :key="card.name" @selectCard='selectCard(card)' v-bind:class="{selected: card.selected}"></Card>
        </div>
        <div class="no-results" v-if="cards.length == 0">
          <p>No results</p>
        </div>
        <!-- End All Cards -->
      </div>
    </div>

  </div>

</template>

<script>
import Card from './Card.vue'
import FilterItem from './FilterItem.vue'
import gql from 'graphql-tag'

export default {
  name: 'CardList',
  components: {
    Card,
    FilterItem
  },

  data () {
    return {
      allCardGQ: null,
      allCards: [
        {name: 'Card 1', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 3'}, {value:'Cat 1'}], type: [{value:'type 3'}, {value:'type 1'}]},
        {name: 'Card 2', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 2'}, {value:'Cat 1'}], type: [{value:'type 2'}, {value:'type 1'}]},
        {name: 'Card 3', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 3'}], type: [{value:'type 3'}, {value:'type 2'}]},
        {name: 'Card 4', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 3'}], type: [{value:'type 3'}]},
        {name: 'Card 5', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 2'}], type: [{value:'type 2'}, {value:'type 1'}]},
        {name: 'Card 6', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 3'}, {value:'Cat 2'}], type: [{value:'type 1'}]},
        {name: 'Card 7', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 2'}], type: [{value:'type 3'}, {value:'type 1'}]},
        {name: 'Card 8', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', selected: false, category:[{value:'Cat 1'}], type: [{value:'type 4'}, {value:'type 1'}]}
      ],
      selectedFilters: []
    }
  },

  methods: {

    // Get index of multidimensional array
    getIndexOf(arr, value, type) {
      for (var i = 0; i < arr.length; i++) {
        if(arr[i].value === value && arr[i].type === type) {
          return i;
        }
      }
    },

    // Get Unique Values
    onlyUnique(value, index, self) {
      return self.indexOf(value) === index;
    },

    // Update selected filters
    selectFilter(filter) {
      let value = filter.target.value,
        type = filter.target.name;
      if (filter.target.checked) {
        this.selectedFilters.push({value: value, type: type});
      } else {
        let index = this.getIndexOf(this.selectedFilters, value, type);
        if (index > -1) {
          this.selectedFilters.splice(index, 1);
        }
      }
    },

    // Updated selected cards
    selectCard(selectedCard) {
      if (selectedCard.selected) {
        selectedCard.selected = false;
      } else {
        selectedCard.selected = true;
      }
    },

    // Sort array
    sortABC(a, b) {
      if(a < b) return -1;
      if(a > b) return 1;
      return 0;
    }
  },

  computed: {

    // Return cards array
    cards() {
      let allCards = this.allCards.slice();
      if (this.selectedFilters.length > 0) {
        for (var i in this.selectedFilters) {
          allCards = allCards.filter((card) => {
            // Get filter type
            let type = this.selectedFilters[i].type;

            // Find cards that match filter
            let foundCategory = card[type].findIndex((filter) => {
              return filter.value === this.selectedFilters[i].value;
            })
            return foundCategory !== -1
          })
        }
      }
      return allCards;
    },

    // Return all unique categories sorted
    categories() {
      let returnCat = [];
      for (var i in this.allCards) {
        for (var j in this.allCards[i].category) {
          returnCat.push(this.allCards[i].category[j].value);
        }
      }

      returnCat = returnCat.filter( this.onlyUnique );
      returnCat = returnCat.sort( this.sortABC );

      return returnCat;
    },

    // Return all unique types sorted
    types() {

      let types = [];
      for (var i in this.allCards) {
        for (var j in this.allCards[i].type) {
          types.push(this.allCards[i].type[j].value);
        }
      }

      types = types.filter( this.onlyUnique );
      types = types.sort( this.sortABC );

      return types;
    },

    // Return selected cards
    selectedCards() {
      return this.allCards.filter(card => card.selected);
    }
  },

  apollo: {
    // Simple query that will update the 'hello' vue property
    /*allCardGQ: gql(`{
      allCardGQ: nodeQuery {
        entities {
          ... on NodeArticle {
            label: entityLabel
            body {
              processed
            }
            tags: fieldTags {
              entity {
                entityLabel
              }
            }
          }
        }
      }
    }`),*/
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.card-list {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
}

/* Filters */
.filters {
  flex: 1;
}

.filter {
  margin-bottom: 16px;
}

.filter .filter-label {
  font-size: 10px;
  text-transform: uppercase;
  text-align: left;
}

/* Cards */
.cards {
  flex: 5;
}

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

.no-results {
  text-align: left;
}

</style>
