document.addEventListener("DOMContentLoaded", () => {
  const e = localStorage.getItem("ui-font-base");
  e && document.documentElement.style.setProperty("--text-base", e + "px"), window.addEventListener("reload-page", () => {
    setTimeout(() => {
      window.location.reload();
    }, 300);
  });
});
typeof Alpine < "u" && Alpine.data("uiSwitcher", () => ({
  init() {
    this.applyStoredPreferences();
  },
  applyStoredPreferences() {
    const e = localStorage.getItem("ui-font-base");
    e && document.documentElement.style.setProperty("--text-base", e + "px");
  },
  setFontSize(e) {
    document.documentElement.style.setProperty("--text-base", e + "px"), localStorage.setItem("ui-font-base", e);
  }
}));
