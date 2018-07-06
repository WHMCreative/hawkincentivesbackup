<template>

  <div class="card-catalog">

    <!-- Begin Selected Cards -->
    <transition v-if="selectedCards.length > 0" name="slide">
      <div class="selected-cards">
        <div class="content-wrapper">
        <div class="content">
          <div class="cards">
            <Card v-for="card_id in selectedCards" :card="getCardById(card_id)" @selectCard='selectCard' v-bind:selected='true' :key="card_id"></Card>
          </div>
          <div class="actions">
            <button class="marketo-modal-cta-link" @click='openFrom'>Start a Conversation</button>
          </div>
        </div>
        </div>
      </div>
    </transition>
    <!-- End Selected Cards -->

    <div class="card-list">
    <!-- Begin Filters -->
      <div class="filters">
        <div class="content">
          <h3 class="filter-title">Reward Types</h3>
          <div class="filter filter--type" v-if="types.length > 0">
            <RadioFilter v-for="type in types" :filter="type" :type="'cardType'" :key="type" @selectRadio='selectRadio'></RadioFilter>
          </div>
          <div class="box-filter">
            <h3 class="filter-title">Filters</h3>
            <div class="filter filter--category" v-if="filters.length > 0">
              <FilterItem v-for="filter in filters" :filter="filter" :type="'boolean'" :key="filter.key" @selectFilter='selectFilter'></FilterItem>
            </div>
          </div>


        </div>

      </div>
      <!-- End Filters -->

      <div class="cards">
        <!-- Begin ALl Cards -->
        <div class="all-cards">
          <Card v-for="card in cards" :card="card" :key="card.id" @selectCard='selectCard' @openFrom='openFrom' v-bind:class="{selected: card.selected}"></Card>
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
import RadioFilter from "./RadioFilter";

export default {
  name: 'CardList',
  components: {
    RadioFilter,
    Card,
    FilterItem
  },

  data () {
    return {
      allCards: null,
      filters: [
        {key:'coBrand', value:'Co-brandable'},
        {key:'customization', value:'Customization'},
        {key:'fulfillment', value:'Fast Fulfillment Available'},
        {key:'virtual', value:'Virtual Option'},
        {key:'phsyical', value:'Physical Option'}
      ],
      selectedCards: [],
      selectedFilters: []
    }
  },

  mounted () {

  },

  methods: {

    // Get card object by ID
    getCardById (cardId) {
      let allCards = this.allCards ? this.allCards.entities : [];
      if (allCards) {
        const foundItems = allCards.filter(({id}) => cardId === id);
        return foundItems.length ? foundItems[0] : null;
      }
    },

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

    // Open form modal
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
        closeBtnInside: true,
        closeOnBgClick: false,
        closeOnContentClick: false,
        callbacks: {
          beforeOpen: function() {
            jQuery.magnificPopup.instance.close = function() {
              jQuery.magnificPopup.proto.close.call(this);
            };
          }
        }
      });
    },

    // Update radio filters
    selectRadio(filter) {
      let value = filter.target.value,
          type = filter.target.name,
          index = this.selectedFilters.findIndex(filter => filter.type === type);

      //Remove existing filters of same type
      if (index > -1) {
        this.selectedFilters.splice(index, 1);
      }

      if (value !== 'All') {
        if (filter.target.checked) {
          this.selectedFilters.push({value: value, type: type});
        }
      }
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
    selectCard(id) {
      if (this.selectedCards.includes(id)) {
        this.selectedCards = this.selectedCards.filter(card_id => card_id !== id);
      } else {
        this.selectedCards.push(id);
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
      let allCards = this.allCards ? this.allCards.entities : [];

      if (allCards) {

        if (this.selectedFilters.length > 0) {
          for (var i in this.selectedFilters) {
            allCards = allCards.filter((card) => {
              // Get filter type
              let type = this.selectedFilters[i].type,
                value = this.selectedFilters[i].value;

              if (type === 'boolean') {
                if (value === 'phsyical') {
                  return card['virtual'] === false;
                } else {
                  return card[value] === true;
                }
              } else  {
                // Find cards that match filter
                return card[type] === value;
              }

            })
          }
        }
        return allCards;
      }
    },

    // Return all unique types sorted
    types() {
      let allCards = this.allCards ? this.allCards.entities : [],
        types = ['All'];
      if (allCards) {
        for (var i in allCards) {
          for (var j in allCards[i].cardType) {
            types.push(allCards[i].cardType);
          }
        }
        types = types.filter( this.onlyUnique );
        types = types.sort( this.sortABC );
      }
      return types;
    },
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

</style>
