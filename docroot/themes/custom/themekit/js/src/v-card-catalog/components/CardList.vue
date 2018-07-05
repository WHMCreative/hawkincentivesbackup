<template>

  <div class="card-catalog">

    <!-- Begin Selected Cards -->
    <div class="selected-cards" v-if="selectedCards.length > 0">
      <Card v-for="card in selectedCards" :card="card" v-bind:selected='true'></Card>
      <button class="marketo-modal-cta-link" @click='openFrom'>Start a Conversation</button>
    </div>
    <!-- End Selected Cards -->

    <div class="card-list">
    <!-- Begin Filters -->
      <div class="filters">
        fliters

        <!--<div class="filter filter&#45;&#45;category" v-if="categories.length > 0">-->
          <!--<div class="filter-label">Catgory</div>-->
          <!--<FilterItem v-for="category in categories" :filter="category" :type="'category'" @selectFilter='selectFilter'></FilterItem>-->
        <!--</div>-->

        <!--<div class="filter filter&#45;&#45;type" v-if="types.length > 0">-->
          <!--<div class="filter-label">Type</div>-->
          <!--<FilterItem v-for="type in types" :filter="type" :type="'type'" @selectFilter='selectFilter'></FilterItem>-->
        <!--</div>-->
      </div>
      <!-- End Filters -->

      <div class="cards">
        <!-- Begin ALl Cards -->
        <div class="all-cards">
          <Card v-for="card in cards" :card="card" :key="card.name" @selectCard='selectCard(card)' @openFrom='openFrom' v-bind:class="{selected: card.selected}"></Card>
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
      allCards: null,
      /*allCards: [
        {title: 'Card 1', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 3'}, {value:'Cat 1'}], type: [{value:'type 3'}, {value:'type 1'}]},
        {title: 'Card 2', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 2'}, {value:'Cat 1'}], type: [{value:'type 2'}, {value:'type 1'}]},
        {title: 'Card 3', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 3'}], type: [{value:'type 3'}, {value:'type 2'}]},
        {title: 'Card 4', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 3'}], type: [{value:'type 3'}]},
        {title: 'Card 5', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 2'}], type: [{value:'type 2'}, {value:'type 1'}]},
        {title: 'Card 6', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 3'}, {value:'Cat 2'}], type: [{value:'type 1'}]},
        {title: 'Card 7', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 2'}], type: [{value:'type 3'}, {value:'type 1'}]},
        {title: 'Card 8', description: 'Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.', category:[{value:'Cat 1'}], type: [{value:'type 4'}, {value:'type 1'}]}
      ],*/
      selectedFilters: []
    }
  },

  mounted () {

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

    openFrom () {
      // Find form content
      let modalSrc = document.getElementsByClassName('form--card-browser');
      if(!modalSrc.length) return;

      // Open in modal
      jQuery.magnificPopup.open({
        items: {
          src: modalSrc,
          type: 'inline'
        },
        closeBtnInside: false,
      });
    },

    // Update selected filters
    // selectFilter(filter) {
    //   let value = filter.target.value,
    //     type = filter.target.name;
    //   if (filter.target.checked) {
    //     this.selectedFilters.push({value: value, type: type});
    //   } else {
    //     let index = this.getIndexOf(this.selectedFilters, value, type);
    //     if (index > -1) {
    //       this.selectedFilters.splice(index, 1);
    //     }
    //   }
    // },

    // Updated selected cards
    selectCard(selectedCard) {
      if (selectedCard.selected) {
        selectedCard.selected = false;
      } else {
        selectedCard.selected = true;
      }

      // console.log(this.cards.filter(card => card.selected));
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
      let allCards = this.allCards ? this.allCards.entities : [];
      // let allCards = this.allCards ? this.allCards : [];

      if (allCards) {
        let cards = [];
        // Add selected value
        for (var i = 0; i < allCards.length; i++) {
          cards[i] = Object.assign({selected: false}, allCards[i]);
        }

        allCards = cards;
        if (this.selectedFilters.length > 0) {
          for (var i in this.selectedFilters) {
            cards = allCards.filter((card) => {
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
      }
    },

    // Return all unique categories sorted
    categories() {
      // let returnCat = [];
      // for (var i in this.allCards) {
      //   for (var j in this.allCards[i].category) {
      //     returnCat.push(this.allCards[i].category[j].value);
      //   }
      // }
      //
      // returnCat = returnCat.filter( this.onlyUnique );
      // returnCat = returnCat.sort( this.sortABC );
      //
      // return returnCat;
    },

    // Return all unique types sorted
    types() {

      // let types = [];
      // for (var i in this.allCards) {
      //   for (var j in this.allCards[i].type) {
      //     types.push(this.allCards[i].type[j].value);
      //   }
      // }
      //
      // types = types.filter( this.onlyUnique );
      // types = types.sort( this.sortABC );
      //
      // return types;
    },

    // Return selected cards
    selectedCards() {
      return this.cards.filter(card => card.selected);
    }
  },

  apollo: {
    // Simple query that will update the 'hello' vue property
    allCards: gql(`{
      allCards: cardQuery {
        entities {
          ... on Card {
            id: entityId
            title: entityLabel
            cashBack: fieldCashBack
            coBrand: fieldCoBrand
            customization: fieldCustomization
            image: fieldPMedia {
              entity {
                fieldMediaImage {
                  entity {
                    fieldImage {
                      url
                      alt
                    }
                  }
                }
              }
            }
            cardCategory:fieldCardCategory {
              entity {
                entityLabel
              }
            }
            cost: fieldCost
            currency:fieldCurrency {
              entity {
                entityLabel
              }
            }
            delivery:fieldDelivery
            description:fieldDescription {
              processed
            }
            fulfillment: fieldFulfillment
            filtered: fieldFiltered
            greetingCard: fieldGreetingCard
            issance: fieldIssuance
            virtual: fieldVirtual
            loadMax: fieldLoadMax
            network: fieldNetwork
            numMechants: fieldNumMechants
            personalization: fieldPersonalization
            prepaidLoad: fieldPrepaidLoad
            prepaidType: fieldPrepaidType
            cardType:fieldCardType
          }
        }
      }
    }`),
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
.card-list {
  max-width: 1370px;
  margin: 0 auto;
  display: flex;
}

.filters,
.cards {
  margin: 0 16px;
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
  flex: 2;
}

/*.all-cards {
  display: flex;
  flex-wrap: wrap;
}
.all-cards .card {
  flex: 0 0 33.33%;
}*/

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
