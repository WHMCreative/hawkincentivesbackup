<template>

  <div class="card-summary">

    <div class="card-list">

      <div class="cards">
        <!-- Begin Cards -->
        <div class="all-cards">
          <!--<Card v-for="card in cards" :card="card" :key="card.id" @selectCard='selectCard' @openFrom='openFrom' :class="{selected: checkIfSelected(card.id)}"></Card>-->
          <Card v-for="card in cards" :card="card" :key="card.id" @selectCard='selectCard' :class="{selected: checkIfSelected(card.id)}"></Card>
        </div>
        <!-- End Cards -->
      </div>
    </div>

  </div>

</template>

<script>
import Card from './Card.vue'
import gql from 'graphql-tag'

// Find a key by an array value
function findKeyByValue(list, value) {
  if (list !== null) {
    for (let key in list) {
      if (list.hasOwnProperty(key)) {
        if (list[key] === value) {
          return key;
        }
      }
    }
  }
  return null;
}

export default {
  name: 'CardSummaryList',
  components: {
    Card
  },

  props: {
    nodeId: {
      type: String
    }
  },

  data () {
    return {
      allCards: null,
      sidebar: null,
      filters: [],
      cardsToShow: 3,
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

    // Get Unique Values
    onlyUnique(value, index, self) {
      return self.indexOf(value) === index;
    },

    // Open form modal
    // openFrom () {
    //   // Find form content
    //   let modalSrc = document.getElementsByClassName('form--card-browser');
    //   if(!modalSrc.length) return;
    //
    //   // Open in modal
    //   jQuery.magnificPopup.open({
    //     items: {
    //       src: modalSrc,
    //       type: 'inline'
    //     },
    //     closeBtnInside: true,
    //     closeOnBgClick: false,
    //     closeOnContentClick: false,
    //     callbacks: {
    //       beforeOpen: function() {
    //         jQuery.magnificPopup.instance.close = function() {
    //           jQuery.magnificPopup.proto.close.call(this);
    //         };
    //       }
    //     }
    //   });
    // },

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
      let cardsId = [];
      const cardSelector = '.paragraph--type--summary-cards .field--name-field-cards .card';
      const count = jQuery(cardSelector).length;

      // Get card (entity) IDs. It will be used for obtain necessary cards for rendering
      jQuery(cardSelector).each(function () {
        cardsId.push(this.getAttribute('data-card-id'));
      });

      if (allCards) {
        // Set number of cards
        this.cardCount = count;

        let cardList = [],
            currentKey;

        for (let i = 0; i < allCards.length; i++) {
          // It is used for sorting
          currentKey = findKeyByValue(cardsId, allCards[i].id);

          if (currentKey) {
            cardList[currentKey] = allCards[i];
          }
        }

        // Return cards
        return cardList;
      }
    },

    // Return all unique types sorted
    types() {
      let allCards = this.allCards ? this.allCards.entities : [],
        types = ['All'];
      if (allCards) {
        for (let i in allCards) {
          for (let j in allCards[i].cardType) {
            types.push(allCards[i].cardType);
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
                      image: derivative(style:FOURTH_COLUMN) {
                        url
                      }
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

    // Query for sidebar content
    sidebar: {
      query: gql(`query ($nid:String!){
        sidebar:nodeById (id: $nid) {
          entityLabel
          fieldPContent {
            entity {
              bundle:entityBundle
              description: fieldDescription {
                processed
              }
            }
          }
        }
      }`),
      variables() {
        return {
          nid: this.nodeId
        }
      },

       skip() {
        return !this.nodeId;
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
