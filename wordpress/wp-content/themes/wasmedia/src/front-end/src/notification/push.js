module.exports = function() {
  const isPushSupported = window.OneSignal.isPushNotificationsSupported();
  if (isPushSupported) {
    window.OneSignal.isPushNotificationsEnabled().then(isEnabled => {
      if (!isEnabled) {
        window.OneSignal.registerForPushNotifications();
      }
    });
  }
};
