<template>
  <div id="home">
    <paperjs v-if="$config.animation" />
    <topbar>
      <template #left>
        <languages />
      </template>
      <template #right>
        <router-link to="/results">{{ $t('myresults') }}</router-link>
      </template>
    </topbar>
    <div id="content">
      <transition name="homeflip" mode="out-in">
        <md v-if="!$store.state.func.start" id="hometext" key="1" class="section" md="home" />
        <md v-else-if="$store.state.func.start" id="overview" key="2" class="textpage" md="tests">
          overview
        </md>
      </transition>
      <!-- <div id="toresults">
        <router-link to="/results">
          Klik hier voor de resultaten pagina.
        </router-link>
      </div> -->
      <div id="introtext" class="section" md="intro">
        <md id="abouttext" class="section" md="about" />
      </div>
    </div>
  </div>
</template>
<script>
import Vue from "vue";

export default {
  data() {
    return {
      homemd: false,
      contactmd: false,
      delayed: false
    };
  },
  mounted() {
    setTimeout(() => {
      this.delayed = true;
    }, 2000);
  },
  methods: {}
};
</script>
<style lang="less" scoped>
#home {
  min-height: 100vh;
  min-height: calc(var(--vh, 1vh) * 100);
  border-width: 0;

  >button {
    position: fixed;
    z-index: 9999;

    @media print {
      color: #00f;
    }
  }

  // /deep/ #topbar {
  //   position: sticky;
  // }

  #content {
    padding: 1rem 1.75rem;
    font-family: "Victor";
    position: relative;
    z-index: 2;

    @media (max-width: 600px) {
      padding: 1rem;
    }

    #hometext {
      font-family: "Victor";
      font-weight: 300;
      font-size: 3rem;
      letter-spacing: -0.035em;
      text-align: center;
      max-width: 12em;
      margin: 1em auto;

      /deep/ p {
        line-height: 1em;
      }

      /deep/ .link {
        font-size: 1rem;
      }

      @media (max-width: 1000px) {
        font-size: 2rem;
      }

      @media (max-width: 500px) {
        font-size: 1.5rem;
        letter-spacing: -0.01;

        /deep/ p {
          line-height: 1.25em;
          margin-bottom: 2rem;
        }

        /deep/ .link {
          font-size: 0.75rem;
        }
      }
    }

    #overview {
      // min-height: 80vh;
      margin-bottom: 4rem;

      /deep/ #md {
        padding-left: 0;
        padding-right: 0;
      }
    }

    #introtext {
      border-top: 2px solid @fg;
      padding: 2rem 0;
      font-size: 1em;
      font-family: "Victor";
      font-weight: 500;
      columns: 3;
      column-gap: 1rem;

      @media (max-width: 30rem) {
        padding: 1rem 0;
      }

      #md {
        margin-bottom: 1em;
        font-size: 1rem;
      }

      /deep/ p {
        text-indent: 1.5rem;

        &:first-child {
          font-size: 1.25rem;
          line-height: 1.2em;
        }
      }

      @media (max-width: 60rem) {
        padding: 2rem 0;
        columns: 1;

        >div {
          max-width: 24rem;
          margin: 0 auto;
        }
      }

      @media (max-width: 45rem) {
        columns: 1;

        /deep/ p {
          text-indent: 1.5rem;

          &:first-child {
            font-size: 1rem;
          }
        }
      }
    }
  }
}

#toresults {
  text-align: center;
  padding: 4rem 1rem;
  display: block;
  border-top: 2px solid @fg;

  a {
    text-decoration: none;
  }
}
</style>
