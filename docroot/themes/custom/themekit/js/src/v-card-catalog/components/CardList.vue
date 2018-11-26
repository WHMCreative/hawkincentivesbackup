<template>

  <div class="card-catalog">

    <!-- Begin Selected Cards -->
    <transition name="slide" v-on:after-leave="afterLeave">
      <div class="selected-cards" v-show="hasSelectedCards" >
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
          <h3 class="filter-title">Filter By</h3>
          <div class="filter filter--category" v-if="filters.length > 0">
            <FilterItem v-for="filter in filters" :filter="filter" :type="'boolean'" :key="filter" @selectFilter='selectFilter'></FilterItem>
          </div>
        </div>
      </div>
      <!-- End Filters -->

      <div class="cards">
        <div class="count">
          1 - {{visibleCards}} of {{ cardCount }} Cards
        </div>
        <!-- Begin ALl Cards -->
        <div class="all-cards">
          <Card v-for="card in cards" :card="card" :key="card.id" @selectCard='selectCard' @openFrom='openFrom' :class="{selected: checkIfSelected(card.id)}"></Card>
        </div>
        <div class="no-results" v-if="cards.length == 0">
          <p>Sorry! No reward options match your query. Please try a different combination of features.</p>
        </div>
        <template v-if="visibleCards !== cardCount">
          <button @click='loadMore' class="load-more">Load More</button>
        </template>
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

  props: {
    nodeId: {
      type: String
    }
  },

  data () {
    return {
      allCards: null,
      cardsToShow: 6,
      cardCount: 0,
      hasSelectedCards: false,
      multipler: 1,
      selectedCards: [],
      selectedFilters: [],
      selectedRadioOption: "All",
      visibleCards: 0
    }
  },

  mounted () {

  },

  methods: {

    afterLeave: function (el, done) {
      this.selectedCards = [];
      this.updateMarketo();
    },

    // Check if this card is selected
    checkIfSelected(id) {
      for (let i in this.selectedCards) {
        if (this.selectedCards[i] == id) return true;
      }
      return false;
    },

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
      for (let i = 0; i < arr.length; i++) {
        if(arr[i].value === value && arr[i].type === type) {
          return i;
        }
      }
    },

    // Load more cards
    loadMore() {
      let allCards = this.allCards ? this.allCards.entities : [];

      if (allCards) {
        if (allCards.length >= this.cards.length) {
          this.multipler = this.multipler + 1;
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
      this.hasSelectedCards = true;

      if (this.selectedCards.includes(id)) {
        if (this.selectedCards.length !== 1) {
          this.selectedCards = this.selectedCards.filter(card_id => card_id !== id);
        } else {
          this.hasSelectedCards = false;
        }
      } else {
        this.selectedCards.push(id);
      }

      this.updateMarketo();
    },

    // Sort array
    sortABC(a, b) {
      if(a < b) return -1;
      if(a > b) return 1;
      return 0;
    },

    updateMarketo() {
      // Get cards names
      let cardName = [];
      for (let i in this.selectedCards) {
        cardName.push(this.getCardById(this.selectedCards[i]).title);
      }

      // Update marketo form with card names
      let formInput = document.querySelector('.form--card-browser input[name="cardName"]');
      if (cardName) {
        formInput.value = cardName.join(', ');
      } else {
        formInput.value = '';
      }
    }
  },

  computed: {

    // Return cards array
    cards() {
      let allCards = this.allCards ? this.allCards.entities : [];

      if (allCards) {

        if (this.selectedFilters.length > 0) {
          allCards = allCards.filter((card) => {
            // Get filter type
            let cardValues = [];
            for (let i in card.cardCategory) {
              cardValues.push(card.cardCategory[i].entity.entityLabel)
            }

            for (let j in this.selectedFilters) {
              if (!cardValues.includes(this.selectedFilters[j].value)){
                return 0;
              }
            }
            return 1;
          })
        }

        // Set number of cards for this filter
        this.cardCount = allCards.length;

        // Set number of visible cards
        if (this.cardsToShow * this.multipler >= this.cardCount) {
          this.visibleCards = this.cardCount;
        } else {
          this.visibleCards = this.cardsToShow * this.multipler;
        }

        // Return the amount of cards per pager
        return allCards.slice(0, (this.visibleCards));
      }
    },

    // Return all unique types sorted
    filters() {
      let allCards = this.allCards ? this.allCards.entities : [],
        types = [];
      if (allCards) {
        for (let i in allCards) {
          for (let j in allCards[i].cardCategory) {
            for (let k in allCards[i].cardCategory[j]) {
              types.push(allCards[i].cardCategory[j].entity.entityLabel);
            }
          }
        }
        types = types.filter( this.onlyUnique );
        types = types.sort( this.sortABC );
      }
      return types;
    }
  },

  apollo: {
    // Query for all the published cards
    allCards: gql(`{
      allCards: cardQuery(sort:{
        direction: ASC
        field: "name"
      },
      filter: {
        conditions:[{
          field: "status"
          value: "1"
        }],
      }) {
        entities {
          ... on CardDefault {
            id: entityId
            title: entityLabel
            image: fieldPMedia {
              entity {
                ... on ParagraphMediaImage {
                  fieldMediaImage {
                    entity {
                      ... on MediaImage {
                        fieldImage {
                          image: derivative(style:FOURTH_COLUMN) {
                            url
                          }
                          alt
                        }
                      }
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
            description:fieldDescription {
              processed
            }
            cardType:fieldCardType
            benefits: fieldBenefits
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
