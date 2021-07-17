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
const subscription = async () => {
  try {
    if ("serviceWorker" in navigator && "PushManager" in window) {
      navigator.serviceWorker
        .register("/sw.js", {
          scope: ".",
        })
        .then(function (swReg) {
          console.log("[Main] SW Registrado");
          return swReg;
        })
        .catch(function (error) {
          console.error("[Main] SW error \n", error);
        });
      /*
        await register.pushManager
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
    } else {
      console.log("[Main] Este navegador no soporta SW o Push");
    }
  } catch (err) {
    console.error("[Main] Error en el montado de SW");
  }
};

window.onload = function (e) {
  if (Notification.permission === "denied") {
    console.log("[Main] Push Notify estan bloqueadas");
  } else {
    subscription();
    /*
    navigator.serviceWorker.getRegistration().then(function (reg) {
      reg.showNotification("Hello world!");
    });
    */
  }
};
