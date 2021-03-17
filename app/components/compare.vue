<template>
  <transition name="fade">
    <div id="compare" @click="close()" v-if="open">
      
      <div id="frame" @click.stop>
        
        <md md="share.instructions" id="instructions"></md>
        
        <label>
          Jouw unieke code:
        </label>
        <div id="unique">{{$store.state.profile.SHARED}}</div>

        <!-- doe eerst een test om je data te kunnen delen -->

        <label v-if="Object.keys(profiles).length > 0">Selecteer een deelnemer:</label>
        <div id="list" v-if="Object.keys(profiles).length > 0">
          <div id="profile" v-for="(profile,id) in profiles" :key="id" @click="setActiveProfile(id)" :class="{active: $store.state.shared.active === id}">
            <span class='checkbox'></span>
            <span class='name'>{{profile.name}}</span>
            <button @click.stop="remove(id)" class="delete"></button>
          </div>
        </div>
        <button>ok</button>
        <div id="add">
          <button id="closed" @click="close()"></button>
          <label>Vul hier de code in om een deelnemer toe te voegen:</label>
          <input type="text" @keydown.enter="addProfile()" v-model="sharedCode" />
          <label>Geef dit profiel een naam (alleen voor jou zichtbaar):</label>
          <input type="text" @keydown.enter="addProfile()" v-model="sharedName" />
          <button id="addprofile" @click="addProfile()">toevoegen</button>
        </div>
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
  computed: {
    profiles () {
      return this.$store.state.shared.profiles
    }
  },
  watch: {
    'open': (val) => {
      if (val) {
        document.body.classList.add('scrollblock')
      } else {
        document.body.classList.remove('scrollblock')
      }
    }
  },
  methods: {
    close() {
      this.$emit("update:open", false);
    },
    async remove (id) {
      const check = await this.$root.confirm({
        message: "Are you sure you want to delete this profile?"
      }).catch(err => {
        console.log('Canceling delete profile')
      });
      if (check) {
        this.$store.dispatch('shared/remove', id)
      }
    },
    async setActiveProfile (id) {
      this.$store.commit("shared/setActive", id)
    },
    async addProfile() {
      if (this.sharedCode in this.$store.state.shared.profiles) {
        await this.$root.alert({
          message: "Profile already exists"
        });
        return false
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
        await this.$store.dispatch("shared/setProfile", profile);
        this.$store.commit("shared/setActive", this.sharedCode);
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
    // border: 1px solid @fg;
    width: 30rem;
    max-width: calc(100% - 2rem);
    background: @bg;
    border-radius: 0.5em;
    min-height: 50vh;
    margin: 3rem auto;
    padding: 3rem 2rem;
    position: relative;
    @media (max-width: 30rem) {
      margin-top: 1rem;
      max-width: calc(100% - 1rem);
      padding: 3rem 1rem;
    }
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

#list {
  margin-bottom: 2rem;
  border: 1px solid @fg;
  border-radius: 0.25rem;
  #profile {
    border-bottom: 1px solid @fg;
    &:last-child {
      border-bottom: 0;
    }
    cursor:pointer;
    display:flex;
    overflow: hidden;
    .checkbox, .delete {
      min-width: 2.5rem;
      text-align: center;
      font-size: 1em;
      flex-grow:0;
      flex-shrink:0;
    }
    .checkbox {
      position: relative;
      &:before {
        transform: scale(0.5);
        content: "";
        position:absolute;
        left:25%;
        top:25%;
        width:50%;
        height:50%;
        border-radius: 100%;
        border: 0.15rem solid @fg;
        box-sizing: border-box;
        transition: all 0.5s @easeInOutExpo;
      }
    }
    .delete {
      opacity: 0.5;
      &:before {
        content: "✕";
      }
      &:hover {
        opacity: 1;
      }
    }
    .name {
      width: 100%;
      padding: 0.5em 0 0.5em .5em;
      overflow: hidden;
      text-overflow: ellipsis;
      flex-grow:1;
      flex-shrink:1;
    }
    &:hover {
      .checkbox {
        &:before {
          // background: @fg;
          transform: scale(.8);
        }
      }
    }
    &.active {
      .checkbox {
        &:before {
          background:@fg;
        }
      }
    }
  }
}

label {
  margin-bottom: 0.5em;
}
input {
  width: 100%;
  box-sizing: border-box;
  border-radius: 0.25em;
}

#instructions {
  font-size: 0.75em;
  opacity: 0.5;
  /deep/ h1 {
    margin-bottom: .5em;
  }
}

#add {
  clear:both;
  overflow: auto;
  margin-bottom: 2rem;
}

#addprofile {
  border: 1px solid @fg;
  padding: 0.5em 1em;
  border-radius: 0.25em;
  float:right;
}
</style>