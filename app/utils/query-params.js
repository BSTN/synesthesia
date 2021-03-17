export default (store) => {
  let params = new URLSearchParams(location.search);
  const id = params.get("id")
  const language = params.get("lang")
  if (id) {
    store.dispatch("profile/set", { USERID: id });
    // URLSearchParams.delete("id")
  }
  if (language) {
    store.dispatch("profile/set", { language: language });
    // URLSearchParams.delete("lang")
  }
}