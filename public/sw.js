const CACHE_NAME = "cache-v1",
  CACHE_FILES = [
    "https://unpkg.com/pwacompat",
    "https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700",
    //Locales
    "/plugins/fontawesome-free/css/all.min.css",
    "/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css",
    "/plugins/icheck-bootstrap/icheck-bootstrap.min.css",
    "/plugins/jqvmap/jqvmap.min.css",
    "/css/adminlte.min.css",
    "/plugins/overlayScrollbars/css/OverlayScrollbars.min.css",
    "/plugins/daterangepicker/daterangepicker.css",
    "/plugins/summernote/summernote-bs4.css",
    "/plugins/jquery/jquery.min.js",
    "/plugins/bootstrap/js/bootstrap.bundle.min.js",
    "/plugins/chart.js/Chart.min.js",
    "/plugins/sweetalert2/sweetalert2.all.min.js",
    "/js/main.js",
    "/img/favicon.ico",
    "/manifest.json",
    "/index.html",
    "/",
  ];

// eslint-disable-next-line no-restricted-globals
self.addEventListener("install", (e) => {
  console.info("[SW] Instalando aplicacion");
  const cache = caches
    .open(CACHE_NAME)
    .then((cache) => cache.addAll(CACHE_FILES));

  // eslint-disable-next-line no-restricted-globals
  self.skipWaiting();
  e.waitUntil(cache);
});
// eslint-disable-next-line no-restricted-globals
self.addEventListener("activate", (e) => {
  console.info("[SW] Archivos cacheados exitosamente");
  //e.waitUntil(clients.claim());
});
// eslint-disable-next-line no-restricted-globals
self.addEventListener("fetch", (e) => {
  if (!(e.request.url.indexOf("http") === 0)) {
    return;
  }
  //console.info("[SW] Observando peticiones de internet", e.request.url);
  const cacheResolve = caches.open(CACHE_NAME).then((cache) => {
    return fetch(e.request)
      .then((response) => {
        if (e.request.method === "GET") cache.put(e.request, response.clone());
        return response;
      })
      .catch((error) => {
        console.error("[SW] No hay internet:", error.message);
        console.log("[SW] cargando de cache", e.request.url);
        return caches.match(e.request);
      });
  });
  e.respondWith(cacheResolve);
});
// eslint-disable-next-line no-restricted-globals
self.addEventListener("sync", (e) => {
  console.info("[SW] Corriendo evento sync");
});
// eslint-disable-next-line no-restricted-globals
self.addEventListener("push", (e) => {
  console.info("[SW] Corriendo evento push");
});

/*self.addEventListener("push", function (event) {
  console.log("[SW] Push Notify recibido");
  /*
  const title = "Orus system",
    options = {
      body: event.data.text(),
    };

  event.waitUntil(self.registration.showNotification(title, options));
  
});*/
