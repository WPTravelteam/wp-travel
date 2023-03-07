const options = [
    // Currency
    {
      label: "Currency",
      options: [
        {
          value: "currency",
          label: "Currency",
          tab: "currency",
        },
        {
          value: "use-currency-name",
          label: "Use Currency Name",
          tab: "currency",
        },
        {
          value: "currency-position",
          label: "Currency Position",
          tab: "currency",
        },
        {
          value: "thousand-separator",
          label: "Thousand separator",
          tab: "currency",
        },
        {
          value: "decimal-separator",
          label: "Decimal separator",
          tab: "currency",
        },
        {
          value: "number-decimals",
          label: "Number of decimals",
          tab: "currency",
        },
      ]
    },
    // Maps
    {
      label: "Maps",
      options: [
        {
          value: "select-map",
          label: "Select Map",
          tab: "maps",
        },
        {
          value: "api-key",
          label: "Api Key",
          tab: "maps",
        },
        {
          value: "zoom-level",
          label: "Zoom Level",
          tab: "maps",
        },
      ]
    },
    //Pages
    {
      label: "Pages",
      options: [
        {
          value: "checkout-page",
          label: "Checkout Page",
          tab: "pages",
        },
        {
          value: "dashboard-page",
          label: "Dashboard Page",
          tab: "pages",
        },
        {
          value: "thankyou-page",
          label: "Thank You Page",
          tab: "pages",
        },
      ]
    },
    // Archive Page Title
    {
      label: "Archive Page Title",
      options: [
        {
          value: "hide-plugin-archive-page-title",
          label: "Hide Plugin Archive Page Title",
          tab: "archive-page-title",
        },
      ]
    },
    // Facts
    {
      label: "Facts",
      options: [
        {
          value: "trip-facts",
          label: "Trip facts",
          tab: "facts",
        },
        {
          value: "trip-facts",
          label: "Trip facts",
          tab: "facts",
        },
      ]
    },
    // Trips Settings
    {
      label: "Trips Settings",
      options: [
        {
          value: "custom-trip-codes",
          label: "Custom Trip Codes",
          tab: "trip-settings"
        },
        {
          value: "hide-related-trips",
          label: "Hide related trips",
          tab: "trip-settings"
        },
        {
          value: "trip-date-listing",
          label: "Trip Date Listing",
          tab: "trip-settings"
        },
        {
          value: "enable-expired-trip",
          label: "Enable Expired Trip Options",
          tab: "trip-settings"
        },
        {
          value: "if-expired-trip",
          label: "If expired, trip set to expired/delete",
          tab: "trip-settings"
        },
        {
          value: "disable-star-rating",
          label: "Disable Star Rating for admin",
          tab: "trip-settings"
        },
      ]
    },
    // Field Editor
    {
      label: "Field Editor",
      options: [
        {
          value: "multiple-traveler",
          label: "Multiple Traveler",
          tab: "field-editor",
        },
      ]
    },
    // General Email Settings
    {
      label: "General Email Settings",
      options: [
        {
          value: "from-email",
          label: "From Email",
          tab: "general-email-settings"
        },
        {
          value: "remove-poweredby-text",
          label: "Remove Powered By Text",
          tab: "general-email-settings"
        },
        {
          value: "custom-poweredby-text",
          label: "Custom Powered By Text",
          tab: "general-email-settings"
        },
      ]
    },
    // Email Templates
    {
      label: "Email Templates",
      options: [
        {
          value: "booking-email-templates",
          label: "Booking Email Templates",
          tab: "email-templates"
        },
        {
          value: "payment-email-templates",
          label: "Payment Email Templates",
          tab: "email-templates"
        },
        {
          value: "inquiry-email-templates",
          label: "Inquiry Email Templates",
          tab: "email-templates"
        },
      ]
    },
    // Account
    {
      label: "Account",
      options: [
        {
          value: "require-login",
          label: "Require Login",
          tab: "account",
        },
        {
          value: "enable-registration",
          label: "Enable Registration",
          tab: "account",
        },
        {
          value: "create-customer-booking",
          label: "Create customer on Registration",
          tab: "account",
        },
        {
          value: "automatically-generate-username",
          label: "Automatically Generate Username",
          tab: "account",
        },
        {
          value: "automatically-generate-password",
          label: "Automatically Generate Password",
          tab: "account",
        },
      ]
    },
    // Checkout
    {
      label: "Checkout",
      options: [
        {
          value: "price-unavailable-text",
          label: "Price Unavailable Text",
          tab: "checkout",
        },
        {
          value: "select-booking",
          label: "Select Booking Option",
          tab: "checkout",
        },
        {
          value: "enable-multiple-checkout",
          label: "Enable multiple Checkout",
          tab: "checkout",
        },
        {
          value: "enable-multiple-checkout",
          label: "Enable multiple Checkout",
          tab: "checkout",
        },
        {
          value: "enable-multiple-travelers",
          label: "Enable multiple Travelers",
          tab: "checkout",
        },
      ]
    },
    // Payment
    {
      label: "Payment",
      options: [
        {
          value: "partial-payment",
          label: "Partial Payment",
          tab: "payment",
        },
        {
          value: "payment-gateways",
          label: "Payment Gateways",
          tab: "payment",
        },
        {
          value: "tax-options",
          label: "Enable Tax",
          tab: "payment",
        },
        {
          value: "tax-on-trip",
          label: "Tax on Trip prices",
          tab: "payment",
        },
        {
          value: "tax-percentage",
          label: "Tax percentage",
          tab: "payment",
        },
      ]
    },
    // Invoice
    {
      label: "Invoice",
      options: [
        {
          value: "use-relative-path",
          label: "Use Relative Path",
          tab: "invoice",
        },
        {
          value: "invoice-logo",
          label: "Invoice Logo",
          tab: "invoice",
        },
        {
          value: "invoice-address",
          label: "Invoice Address",
          tab: "invoice",
        },
        {
          value: "invoice-contact",
          label: "Invoice Contact",
          tab: "invoice",
        },
        {
          value: "invoice-website",
          label: "Invoice Website",
          tab: "invoice",
        },
      ]
    },
    // Miscellaneous Options
    {
      label: "Miscellaneous Options",
      options: [
        {
          value: "enable-trip-inquiry",
          label: "Enable Trip Inquiry",
          tab: "misc-options",
        },
        {
          value: "gdpr-message",
          label: "GDPR Message",
          tab: "misc-options",
        },
        {
          value: "open-gdpr-tab",
          label: "Open GDPR in new tab",
          tab: "misc-options",
        },
      ]
    },
    // Advanced Gallery
    {
      label: "Advanced Gallery",
      options: [
        {
          value: "advanced-gallery-display-style",
          label: "Gallery Display Style",
          tab: "advanced-gallery",
        },
        {
          value: "advanced-gallery-autoplay-slides",
          label: "Autoplay Slides",
          tab: "advanced-gallery",
        },
        {
          value: "advanced-gallery-number-slides",
          label: "Number of slides",
          tab: "advanced-gallery",
        },
      ]
    },
    // reCaptcha V2
    {
      label: "ReCaptcha V2",
      options: [
        {
          value: "recaptcha-v2-site-key",
          label: "Site Key",
          tab: "recaptcha-v2",
        },
        {
          value: "recaptcha-v2-secret-key",
          label: "Secret Key",
          tab: "recaptcha-v2",
        },
      ]
    },
    // Third Party
    // Fixer API
    {
      label: "Fixer API",
      options: [
        {
          value: "use-api-layer-fixer-api",
          label: "Use API Layer Fixer API",
          tab: "third-party",
        },
        {
          value: "fixer-api-enter-api-access-key",
          label: "Enter your API Access Key",
          tab: "third-party",
        },
        {
          value: "fixer-api-premium-api-key-sub",
          label: "Premium API Key Subscription",
          tab: "third-party",
        },
      ]
    },
    //Currency Exchange
    {
      label: "Currency Exchange",
      options: [
        {
          value: "currency-exchange-set-api-timer",
          label: "Set API Timer Reset",
          tab: "third-party",
        },
        {
          value: "currency-exchange-purge-api-cache",
          label: "Purge API Cache",
          tab: "third-party",
        },
      ]
    },
    // Google Calendar
    {
      label: "Google Calendar",
      options: [
        {
          value: "google-calendar-client-id",
          label: "Client ID",
          tab: "third-party",
        },
        {
          value: "google-calendar-client-secret",
          label: "Client Secret",
          tab: "third-party",
        },
        {
          value: "google-calendar-redirect-url",
          label: "Redirect URL",
          tab: "third-party",
        },
      ]
    },
    // Weather Forecast API
    {
      label: "Weather Forecast API",
      options: [
        {
          value: "weather-forecast-enter-weather-api",
          label: "Enter Weather API Key",
          tab: "third-party",
        },
      ]
    },
    // Wishlists Options
    {
      label: "Wishlists Options",
      options: [
        {
          value: "wishlists-add-to-wishlist-icon",
          label: "\"Add to wishlist\" Icon",
          tab: "third-party",
        },
        {
          value: "wishlists-remove-from-wishlist-icon",
          label: "\"Remove from wishlist\" Icon",
          tab: "third-party",
        },
        {
          value: "wishlists-add-to-wishlist-text",
          label: "\"Add to wishlist\" Text",
          tab: "third-party",
        },
        {
          value: "wishlists-remove-from-wishlist-text",
          label: "\"Remove from wishlist\" Text",
          tab: "third-party",
        },
        {
          value: "icon-color",
          label: "Icon color",
          tab: "third-party",
        },
      ]
    },
    // Zapier Automation
    {
      label: "Zapier Automation",
      options: [
        {
          value: "zapier-enable-inquiry-automation",
          label: "Enable Inquiry Automation",
          tab: "third-party",
        },
        {
          value: "zapier-enable-bookings-automation",
          label: "Enable Bookings Automation",
          tab: "third-party",
        },
      ]
    },
    // Multiple Currency
    {
      label: "Multiple Currency",
      options: [
        {
          value: "multiple-currency-base-currency",
          label: "Base Currency",
          tab: "third-party",
        },
        {
          value: "multiple-currency-use-geolocation",
          label: "Use Geo Location",
          tab: "third-party",
        },
        {
          value: "multiple-currency-menu-location",
          label: "Menu Location",
          tab: "third-party",
        },
        {
          value: "multiple-currency-select-currencies",
          label: "Select Currencies",
          tab: "third-party",
        },
        {
          value: "multiple-currency-reset-cache",
          label: "Reset Cache",
          tab: "third-party",
        },
      ]
    },
    // Mailchimp Settings
    {
      label: "Mailchimp Settings",
      options: [
        {
          value: "mailchimp-api-key",
          label: "API Key",
          tab: "third-party",
        },
        {
          value: "mailchimp-select-list",
          label: "Select List",
          tab: "third-party",
        },
        {
          value: "mailchimp-form",
          label: "Form",
          tab: "third-party",
        },
        {
          value: "mailchimp-opt-in",
          label: "Opt-in",
          tab: "third-party",
        },
        {
          value: "mailchimp-subscribe-label",
          label: "Subscribe Label",
          tab: "third-party",
        },
        {
          value: "mailchimp-subscribe-description",
          label: "Subscribe Description",
          tab: "third-party",
        },
      ]
    },
    // PWA Settings
    {
        label: "PWA Settings",
        options: [
            {
                value: "enable-pwa",
                label: "Enable PWA",
                tab: "advanced"
            },
            {
                value: "pwa-app-fullname",
                label: "App Fullname",
                tab: "advanced"
            },
            {
                value: "pwa-app-shortname",
                label: "App Short name",
                tab: "advanced"
            },
            {
                value: "pwa-start-url",
                label: "Start URL",
                tab: "advanced"
            },
            {
                value: "pwa-app-logo",
                label: "App Logo",
                tab: "advanced"
            },
        ]
    }
  ];

export default options;