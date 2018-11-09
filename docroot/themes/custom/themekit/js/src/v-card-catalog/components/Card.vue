<template>
  <div class="card" :class="['card-' + card.id]">
    <div class="card-overview">
      <div class="media">
        <div class="media-content">
          <button class="heart" @click="handleClick" v-show="selected"></button>
          <img :src=card.image.entity.fieldMediaImage.entity.fieldImage.image.url :alt=card.image.entity.fieldMediaImage.entity.fieldImage.alt />
        </div>
      </div>
      <div class="content" v-show="!selected">
        <h3 class="card-title">
          {{ card.title }}
        </h3>

        <button class="heart" @click="handleClick">Order Today</button>

        <div class="features">
          <div class="top-level-info">
            <div class="type" :class=card.cardType>
              {{ typeMap[card.cardType ]}}
            </div>
            <div class="cost" v-show="card.cardType === 'prepaid' || card.cardType === 'gift_card'">
              {{ costMap[card.cost ]}}
            </div>
          </div>
          <div class="content">
            <div class="description" v-if="card.description" v-html="card.description.processed">
              {{ card.description.processed }}
            </div>
            <div class="benfits" v-if="card.benefits.length > 0">
              <ul>
                <li v-for="benefit in card.benefits">
                  {{ benefit }}
                </li>
              </ul>
            </div>
          </div>
        </div>

      </div>
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
        'individual': 'Ships Individually',
        'bulk': 'Ships in Bulk'
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
        'anonymous': false,
        'personalized': true
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
    /*details(id) {
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
    },*/

    handleClick() {
      this.$emit('selectCard', this.card.id);
    },
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>




</style>
