<template>
  <div class="card" :class="['card-' + card.id]">
    <div class="card-overview">
      <div class="media">
        <div class="media-content">
          <button class="heart" @click="handleClick" ></button>
          <img :src=card.image.entity.fieldMediaImage.entity.fieldImage.image.url :alt=card.image.entity.fieldMediaImage.entity.fieldImage.alt />
        </div>
      </div>
      <div class="content" v-show="!selected">
        <h3 class="card-title">
          {{ card.title }}
        </h3>

        <div class="features">
          <div class="top-level-info">
            <div class="type" :class=card.cardType>
              {{ typeMap[card.cardType ]}}
            </div>
            <div class="cost" v-show="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
              {{ costMap[card.cost ]}}
            </div>
          </div>
          <div class="content boolean">
            <div class="feature icon co-brand" :class="{ true: card.coBrand }">
              Co-Brandable
            </div>
            <div class="feature icon fulfillment" :class="{ true: card.fulfillment }">
              Fast Fulfillment
            </div>
            <div class="feature icon customization" :class="{ true: card.customization }">
              Customization
            </div>
            <div class="feature icon virtual" :class="{ true: card.virtual }" v-show="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
              Virtual Options
            </div>
          </div>
        </div>

        <button class="view-details" @click='details(card.id)' v-show="!selected">View Details</button>
      </div>
    </div>

    <div class="card card-modal mfp-hide":class="'modal--card-' + card.id" v-show="!selected">

      <div class="card-info">
        <div class="media">
          <img :src=card.image.entity.fieldMediaImage.entity.fieldImage.image.url :alt=card.image.entity.fieldMediaImage.entity.fieldImage.alt />
        </div>
        <div class="content">
          <h3 class="card-title">
            {{ card.title }}
          </h3>
          <div class="description" v-html="card.description.processed">
            {{ card.description.processed }}
          </div>
        </div>
      </div>

      <div class="features">
        <div class="top-level-info">
          <div class="type" :class=card.cardType>
            {{ typeMap[card.cardType ]}}
          </div>
          <div class="cost" v-show="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
            {{ costMap[card.cost ]}}
          </div>
        </div>
        <div class="content boolean">
          <template v-if="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
            <div class="feature icon co-brand" :class="{ true: card.coBrand }">
              Co-Brandable
            </div>
          </template>
          <div class="feature icon fulfillment" :class="{ true: card.fulfillment }">
            Fast Fulfillment
          </div>
          <div class="feature icon customization" :class="{ true: card.customization }">
            Customization
          </div>
          <div class="feature icon virtual" :class="{ true: card.virtual }">
            Virtual Options
          </div>
          <template v-if="card.cardType === 'prepaid'">
            <div class="feature icon cashBack" :class="{ true: card.cashBack }">
              5% Cash Back
            </div>
          </template>
          <div class="feature icon filtered" :class="{ true: card.filtered }">
            Filterable
          </div>
          <template v-if="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
            <div class="feature icon greetingCard" :class="{ true: card.greetingCard }">
              Greeting Card
            </div>
          </template>
        </div>

        <div class="content varied">
          <template v-if="card.cardType === 'prepaid'">
            <div class="feature personalization" :class="{ true: card.personalization }">
              <span class="label">Personalization</span>
              <span class="value">{{ personalizationMap[card.personalization] }}</span>
            </div>
          </template>
          <div class="feature delivery" :class="{ true: card.delivery }">
            <span class="label">Delivery/Shipping</span>
            <span class="value">{{ devlieryMap[card.delivery] }}</span>
          </div>
          <template v-if="card.cardType === 'gift_card' || card.cardType === 'omnicodes'">
            <div class="feature cardCategory" :class="{ true: card.cardCategory }">
              <span class="label">Card Category</span>
              <span class="value">{{ card.cardCategory.entity.entityLabel }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'gift_card' || card.cardType === 'omnicodes'">
            <div class="feature currency" :class="{ true: card.currency }">
              <span class="label">Currency</span>
              <span class="value">{{ card.currency.entity.entityLabel }}</span>
            </div>
          </template>

          <template v-if="card.cardType === 'prepaid' && (card.prepaidType === 'pre_filtered' || card.prepaidType === 'filtered')">
            <div class="feature numMechants" :class="{ true: card.numMechants }">
              <span class="label">Number of Merchants</span>
              <span class="value">{{ card.numMechants }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'gift_card' || card.cardType === 'omnicodes'">
            <div class="feature issuance" :class="{ true: card.issance }">
              <span class="label">Issuance</span>
              <span class="value">{{ issuanceMap[card.issance] }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'gift_card' || card.cardType === 'omnicodes'">
            <div class="feature loadMax" :class="{ true: card.loadMax }">
              <span class="label">Load Max</span>
              <span class="value">${{ card.loadMax }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'prepaid'">
            <div class="feature network" :class="{ true: card.network }">
              <span class="label">Network</span>
              <span class="value">{{ networkMap[card.network] }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'prepaid'">
            <div class="feature prepaidLoad" :class="{ true: card.prepaidLoad }">
              <span class="label">Prepaid Load</span>
              <span class="value">{{ prepaidLoadMap[card.prepaidLoad] }}</span>
            </div>
          </template>
          <template v-if="card.cardType === 'prepaid'">
            <div class="feature prepaidType" :class="{ true: card.prepaidType }">
              <span class="label">Prepaid Type</span>
              <span class="value">{{ prepaidTypeMap[card.prepaidType] }}</span>
            </div>
          </template>
        </div>
      </div>
      <button class="marketo-modal-cta-link" @click="$emit('openFrom')">Start a Conversation</button>
    </div>

    <input v-show="!selected" id="checkBox" type="checkbox" :checked="card.selected">
  </div>
</template>

<script>
export default {
  props: ['card', 'selected'],
  data() {

    return {
      costMap: {
        '1x': '$',
        '2x': '$$',
        '3x': '$$$',
        '4x': '$$$$'
      },

      devlieryMap: {
        'individual': 'Individual',
        'bulk': 'Bulk'
      },

      issuanceMap: {
        'us_only': 'U.S. Only',
        'international': 'International*'
      },

      networkMap: {
        'discover': 'Discover',
        'Mastercard': 'Mastercard',
        'visa': 'Visa'
      },

      personalizationMap: {
        'anonymous': 'Anonymous',
        'personalized': 'Personalized'
      },

      prepaidLoadMap: {
        'single_load': 'Single Load',
        'reloadable': 'Reloadable'
      },

      prepaidTypeMap: {
        'non_filtered': 'Non Filtered',
        'pre_filtered': 'Pre-Filtered',
        'filtered': 'Filtered',
      },

      typeMap: {
        'prepaid': 'PrePaid',
        'gift_card': 'Gift Card',
        'omnicode': 'OmniCode'
      }
    }
  },
  methods: {

    // Open card detail modal
    details(id) {
      // Find detail content
      let selector = 'modal--card-' + id;
      let modalSrc = document.getElementsByClassName(selector);
      if (!modalSrc.length) return;

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

    handleClick() {
      this.$emit('selectCard', this.card.id);
    },
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>




</style>
