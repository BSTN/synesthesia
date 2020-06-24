<template>
  <div id="textpage">
    {{ extra }}
    <topbar>
      <template #left>
        <router-link to="/">{{ $t("home") }}</router-link>
      </template>
      <template #right>
        <!-- <router-link to="/about" v-if="url !== '/about'">{{
          $t("about")
        }}</router-link> -->
      </template>
    </topbar>
    <md :md="textname" v-if="textname" ref="md"></md>
  </div>
</template>
<script>
import { each } from "lodash";
import { mapGetters } from "vuex";
export default {
  computed: {
    ...mapGetters({
      extra: "extra/all",
    }),
    textname() {
      return this.$route.params.textname || false;
    },
    url() {
      return this.$route.path;
    },
  },
  methods: {
    check() {
      if (this.$refs["md"]) {
        let children = this.$refs["md"].$el.children[0].children;
        let top = window.scrollY;
        let bottom = window.scrollY + window.innerHeight;
        each(children, (el, k) => {
          if (el.offsetTop < bottom) el.classList.add("show");
          else el.classList.remove("show");
        });
      }
    },
  },
  mounted() {
    window.addEventListener("scroll", this.check);
    setTimeout(this.check, 500);
  },
};
</script>
<style lang="less" scoped>
#textpage {
  background: @bg;
  #md {
    padding: 1rem 0.5rem;
    font-family: "Victor";
    animation: delayfade;
    animation-delay: 0s;
    animation-duration: 3s;
    animation-direction: forwards;
    animation-fill-mode: forwards;
    opacity: 0;
    @media (min-width: 1000px) {
      padding: 4rem 1rem;
    }
    @keyframes delayfade {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }
  }
  /deep/ #md > #md > * {
    opacity: 0;
    transition: all 3s @easeOutExpo;
    &.show {
      opacity: 1;
    }
  }
  /deep/ #md #md p {
    &.big {
      font-size: 2em;
      line-height: 1em;
      margin-bottom: 2em;
      max-width: 24em;
      .movein();
      @media (max-width: 800px) {
        font-size: 1em;
      }
    }
    @media (max-width: 800px) {
      font-size: 0.75em;
      &.big {
        font-size: 1em;
      }
    }
  }
  /deep/ h1 {
    margin-top: 2em;
    margin-bottom: 1em;
    margin: 2em auto 1em auto;
    font-size: 1.5em;
    max-width: 16em;
    text-align: center;
    text-indent: none;
    &:first-child {
      margin: 1em auto 1em auto;
    }
    @media (max-width: 800px) {
      font-size: 1em;
      margin: 4em auto 3em;
    }
  }
  /deep/ p {
    text-indent: 2em;
    max-width: 28em;
    width: 100%;
    margin: 0 auto 1em auto;
    // margin: 0 auto 2em auto;
  }
  /deep/ #md > #md > div {
    font-family: Helvetica, sans-serif;
  }
  /deep/ #md > #md > a {
    display: block;
    margin: 2em auto;
  }
}
</style>
