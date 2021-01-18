self.addEventListener("push", function (event) {
  console.log("[SW] Push Notify recibido");

  const title = "Orus system",
    options = {
      body: event.data.text(),
    };

  event.waitUntil(self.registration.showNotification(title, options));
});
