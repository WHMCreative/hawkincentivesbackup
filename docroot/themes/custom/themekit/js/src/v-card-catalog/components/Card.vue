<template>
  <div class="card" @click="$emit('selectCard')" :class="[{ selectable: !selected}, 'card-' + card.id]">
    <div class="card-overview">
      <div class="media">
        <div v-show="!selected" class="heart"></div>
        <img :src=card.image.entity.fieldMediaImage.entity.fieldImage.url :alt=card.image.entity.fieldMediaImage.entity.fieldImage.alt />
      </div>
      <div class="content">
        <h3 class="card-title">
          {{ card.title }}
        </h3>

        <div class="card-info">
          <div class="top-level-info">
            <div class="type" :class=card.cardType>
              {{ card.cardType }}
            </div>
            <div class="cost">
              {{ card.cost }}
            </div>
          </div>
          <div class="features">
            <div class="feature co-brand" :class="{ true: card.coBrand }">
              Co-Brandable
            </div>
            <div class="feature fulfillment" :class="{ true: card.fulfillment }">
              Fast Fulfillment
            </div>
            <div class="feature customization" :class="{ true: card.customization }">
              Customization
            </div>
            <div class="feature virtual" :class="{ true: card.virtual }">
              Virtual Options
            </div>
          </div>

        </div>
      <button class="view-details" @click='details(card.id)'>View Details</button>
    </div>


    </div>

    <div class="card-modal mfp-hide":class="'modal--card-' + card.id">
      <div class="media">
        <img :src=card.image.entity.fieldMediaImage.entity.fieldImage.url :alt=card.image.entity.fieldMediaImage.entity.fieldImage.alt />
      {{ card.description.processed }}
        <!--<button class="marketo-modal-cta-link" @click="$emit('openFrom')">Start a Conversation</button>-->
        <button class="marketo-modal-cta-link" @click="$emit('openFrom')">Start a Conversation</button>
      </div>


      <div class="content">
        <h3 class="card-title">
          {{ card.title }}
        </h3>

        <div class="card-info">
          <div class="top-level-info">
            <div class="type" :class=card.cardType>
              {{ card.cardType }}
            </div>
            <div class="cost">
              {{ card.cost }}
            </div>
          </div>
          <div class="features">
            <div class="feature co-brand" :class="{ true: card.coBrand }">
              Co-Brandable
            </div>
            <div class="feature fulfillment" :class="{ true: card.fulfillment }">
              Fast Fulfillment
            </div>
            <div class="feature customization" :class="{ true: card.customization }">
              Customization
            </div>
            <div class="feature virtual" :class="{ true: card.virtual }">
              Virtual Options
            </div>
            <div class="feature personalization" :class="{ true: card.personalization }">
              Personalization
            </div>
            <div class="feature prepaidLoad" :class="{ true: card.prepaidLoad }">
              Prepaid Load
            </div>
            <div class="feature delivery" :class="{ true: card.delivery }">
              Delivery/Shipping
            </div>
            <div class="feature numMechants" :class="{ true: card.numMechants }">
              Number of Merchants
            </div>

            <div class="feature cashBack" :class="{ true: card.cashBack }">
              5% Cash Back
            </div>
            <div class="feature filtered" :class="{ true: card.filtered }">
              Filterable
            </div>
            <div class="feature greetingCard" :class="{ true: card.greetingCard }">
              greetingCard
            </div>
            <div class="feature issance" :class="{ true: card.issance }">
              Issuance
            </div>
            <div class="feature loadMax" :class="{ true: card.loadMax }">
              loadMax
            </div>
            <div class="feature network" :class="{ true: card.network }">
              network
            </div>
            <div class="feature prepaidLoad" :class="{ true: card.prepaidLoad }">
              prepaidLoad
            </div>
            <div class="feature prepaidLoad" :class="{ true: card.prepaidType }">
              prepaidType
            </div>

            <div class="feature cardCategory" :class="{ true: card.cardCategory }">
    <!--{{ card.cardCategory.entity.entityLabel }}-->
            </div>
            <div class="feature prepaidLoad" :class="{ true: card.currency }">
    <!--{{ card.currency.entity.entityLabel }}-->
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
  methods: {

    details(id) {
      // Find form content
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
      });
    },

    open() {
      console.log('open');
    }
  }
};
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>

.heart {
  background-color: #ccc;
  height: 10px;
  transform: rotate(-45deg);
  width: 10px;
  position: absolute;
  top: 20px;
  right: 20px;
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

.card input[type=checkbox] {
  display: none;
}

.card.selectable:hover{
  cursor: pointer;
}

.card:hover .heart,
.card.selected .heart,
.card:hover .heart:before,
.card.selected .heart:before,
.card:hover .heart:after,
.card.selected .heart:after {
  background-color: red;
}


.card-overview {
  min-height: 100px;
  display: flex;
  margin: 0 -16px 48px;
  font-weight: 700;

  .media,
  .content {
    margin: 0 16px;
  }

  .media {
    flex: 2;
    position: relative;

    img {
      width: 100%;
    }
  }

  .content {
    flex: 3;
    text-align: left;

    .card-title {
      font-size: 30px;
      margin-bottom: 20px;
    }

    .card-info {
      border-bottom: #ccc 2px solid;
      border-top: #ccc 2px solid;
      padding: 30px 0 15px;
      position: relative;
    }

    .top-level-info {
      display: flex;
      align-items: center;
      position: absolute;
      top: 0;
      transform: translateY(-50%);
      background: white;
      padding-right: 5px;
    }

    .type {
      background: #f00;
      padding: 3px 8px;
      border-radius: 3px;
      color: white;
    }

    .cost {
      padding: 0 10px;
      color: #ccc;
    }


    .features {
      display: flex;
      flex-wrap: wrap;

      .feature {
        flex: 0 0 50%;

        &::before {
          content:'X';
          color: #ccc;
          margin-right: 16px;
        }

        &.true:before {
          content:'O';
          color: #f00;
        }
      }
    }

    .view-details {
      cursor: pointer;
    }
  }

}


</style>
