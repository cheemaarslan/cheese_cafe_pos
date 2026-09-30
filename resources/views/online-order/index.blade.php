<!doctype html>
<html>
  <head>
    <title>Online Order</title>
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0, minimal-ui"
    />
    <meta charset="UTF-8" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
  </head>

  <body>
    <!-- zuppler code starts -->
    <div
      id="zuppler-menu"
      data-channel-url="http://api.zuppler.com/v3/channels/e8f5b233fev.json"
      data-integration="cceeed86"
      data-restaurant-id="32945"
      data-locale="en"
      data-colors-background="#ffffff"
      data-colors-highContrast="#1f1f39"
      data-colors-midContrast="#6e7191"
      data-colors-lowContrast="#888888"
      data-colors-brand="#b7151e"
      data-colors-brandContrast="#ffffff"
      data-colors-heroBackground="#9e5375"
      data-colors-heroContrast="#FFFFFF"
      data-fonts-heading="Rubik, helvetica, sans-serif"
      data-fonts-body="Rubik, helvetica, sans-serif"
      data-fonts-deco="Rubik, helvetica, sans-serif"
    >
      <!-- Loading -->
      <div id="zloading">Please wait. Loading menu...</div>
      <style>
        @import url("https://fonts.googleapis.com/icon?family=Material+Icons&display=swap");
        #zloading {
          position: fixed;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          z-index: 999999;
          background: rgba(255, 255, 255, 0.8);
          color: #000000;
          font-size: 21px;
          font-weight: 700;
          text-align: center;
          padding-top: 200px;
          padding-top: 30vh;
        }
      </style>

      <!-- /Loading -->
    </div>
    <script src="//web5.zuppler.com/common.js"></script>
    <script src="//web5.zuppler.com/order.js"></script>
    <!-- zuppler code ends -->
  </body>
</html>
