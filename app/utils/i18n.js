import Vue from 'vue';
import VueI18n from 'vue-i18n';

// load config
const config = JSON.parse(
  document.getElementById("bootload-config").innerText
);


// load translations
const translationsYml = JSON.parse(
  document.getElementById("bootload-translations").innerText
);
let translations = {};
for (let i in translationsYml) {
  for (let lan in translationsYml[i]) {
    if (!(lan in translations)) translations[lan] = {};
    translations[lan][i] = translationsYml[i][lan];
  }
}

Vue.use(VueI18n);
export const i18n = new VueI18n({
  locale: config.defaultLanguage,
  fallbackLocale: 'nl',
  messages: translations
});