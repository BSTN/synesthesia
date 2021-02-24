<template>
  <transition name="fade">
    <div id="compare" @click="close()" v-if="open">
      <div id="frame" @click.stop>
        <button id="closed" @click="close()"></button>
        <label>Vul hier de code in:</label>
        <input type="text" @keydown.enter="addProfile()" v-model="sharedCode" />
        <label>Geef dit profiel een naam (alleen voor jou zichtbaar):</label>
        <input type="text" @keydown.enter="addProfile()" v-model="sharedName" />
        <button @click="addProfile()">toevoegen</button>
        <hr />
        <md md="share.instructions" id="instructions"></md>
        {{ $store.state.shared.profiles }}
      </div>
    </div>
  </transition>
</template>
<script>
export default {
  props: ["open"],
  data() {
    return {
      sharedCode: "YqojeH1KtNrQoLzvZHTrFHGk795DXIyd",
      sharedName: "Abc"
    };
  },
  methods: {
    close() {
      this.$emit("update:open", false);
    },
    async addProfile() {
      if (this.sharedCode in this.$store.state.shared.profiles) {
        await this.$root.alert({
          message: "Profile already exists, updating data..."
        });
      }
      const { data } = await this.$axios
        .post("./api/getshared", {
          code: this.sharedCode
        })
        .catch(e => {
          console.warn(e);
        });
      if (data) {
        let profile = {};
        profile[this.sharedCode] = {
          name: this.sharedName,
          timestamp: new Date().getTime(),
          data: data
        };
        this.$store.dispatch("shared/setProfile", profile);
      }
    }
  }
};
</script>
<style lang="less" scoped>
#compare {
  position: fixed;
  top: 0;
  left: 0;
  background: rgba(#222, 0.9);
  width: 100%;
  height: 100%;
  z-index: 9;
  overflow: auto;;
  #frame {
    border: 1px solid @fg;
    width: 30rem;
    max-width: calc(100% - 2rem);
    background: @bg;
    border-radius: 0.5em;
    min-height: 50vh;
    margin: 3rem auto;
    padding: 2rem;
    position: relative;
    #closed {
      position: absolute;
      top: 0;
      right: 0;
      margin: 1rem;
      &:before {
        content: "✕";
      }
    }
    input {
      width: 100%;
      margin-bottom: 1rem;
    }
  }
}

label {
  margin-bottom: 0.5em;
}
input {
  
}
</style>