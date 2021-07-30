const subscription = async () => {
  try {
    if ("serviceWorker" in navigator) {
      navigator.serviceWorker
        .register("/sw.js")
        .then(function (response) {
          if (response) {
            console.log("[SW] Services Worker registrado con exito");
            Notification.requestPermission()
              .then((response) => {
                if (response === "granted") {
                  console.log("[SW] Servicio de notificaciones activadas");
                  //window.sendPushMessage("Contactos", "Notificaciones activadas");
                } else {
                  console.error("[SW] Servicio de notificaciones rechazadas");
                }
              })
              .catch((error) => console.error(error.message));
          }
        })
        .catch(function (error) {
          console.error("[Main] SW error \n", error);
        });
    } else {
      console.log("[Main] Este navegador no soporta SW - Server no seguro");
    }
  } catch (err) {
    console.error("[Main] Error en el montado de SW");
  }
};

window.onload = function (e) {
  subscription();
};

window.sendPushMessage = (title, message) => {
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.ready.then((sw) => {
      sw.showNotification(title, {
        body: message,
      }).catch((error) => {
        console.error("[SW] Notificaciones no disponibles", error.message);
        window.Swal.fire({
          title: message,
          showConfirmButton: title !== "error" ? false : true,
          timer: title !== "error" ? 1500 : 9000,
          position: "top",
        });
      });
    });
  } else {
    window.Swal.fire({
      title: message,
      showConfirmButton: true,
      timer: 6000,
      position: "top",
    });
  }
};

/*
function urlBase64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");

  const rawData = window.atob(base64);
  const outputArray = new Uint8Array(rawData.length);

  for (let i = 0; i < rawData.length; ++i) {
    outputArray[i] = rawData.charCodeAt(i);
  }
  return outputArray;
}

//const public_key = "BAXaaj09DxhifYLLpBqmDrY815JxlmqpslozLflLxeml4cmFUxPwk1rTIVLvoqBLqReVeKyeloWH_GZ90ryA8IE",
//  validPublicKey = urlBase64ToUint8Array(public_key),

/*
  if (Notification.permission === "denied") {
    console.log("[Main] Push Notify estan bloqueadas");
  } else {
    subscription();
  }
  */
/*
        subscrition = await register.pushManager
          .subscribe({
            userVisibleOnly: true,
            applicationServerKey: validPublicKey,
          })
          .then((res) => {
            console.log("[Main] Push Notify registrado");
            return res;
          })
          .catch((err) => {
            console.error("[Main] Push Notify error \n", err);
          });

        
        console.log(JSON.stringify(subscrition));
        await fetch("http://localhost:3000/api/user/subscriptionNotify", {
          method: "GET",
          body: JSON.stringify(subscrition),
          headers: {
            "Content-Type": "application/json",
          },
        })
          .then((data) => {
            console.log("Subscripción enviada a servidor");
          })
          .catch((e) => {
            console.error("Error al enviar la subscripción al servidor", e);
          });
      */
