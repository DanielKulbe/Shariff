# Shariff for bolt

Shariff enables website users to share their favorite content without compromising their privacy.

![Shariff Logo © 2014 Heise Zeitschriften Verlag](http://www.heise.de/icons/ho/shariff-logo.png)

Facebook, Google+ and Twitter supply official sharing code snippets which quietly siphon personal data from all page visitors. Shariff enables visitors to see how popular your page is on Facebook and share your content with others without needless data leaks.

## Original software
Shariff `(/ˈʃɛɹɪf/)` is an open-source, low-maintenance, high-privacy solution, [originally maintained by German computer magazine c't and heise online](http://heiseonline.github.io/shariff/).

Shariff consists of two parts: a simple JavaScript client library and an optional server-side component. The latter fetches the number of likes, tweets and plus-ones. Share buttons and share counts work without a connection between your visitors' browsers and *social networks* (unless they decide to share, of course).

Licensed under the [MIT License (MIT)](https://github.com/heiseonline/shariff/blob/master/LICENSE.txt)

## Font-Awesome
This Software includes the Font-Awsome Webfont.

Font Awesome 4.3.0 by @davegandy - http://fontawesome.io - @fontawesome

License - http://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)

## Options
Edit in the module´s `config.yml`.

### General
| Option | Description | Default |
|--------|-------------|---------|
| `count` | Enables the count feature.  | `false` |
| `fontawesome` | If Font-Awesome 4.3.0 should be added | `true` |
| `theme` | Two color shemes are included, `standard` or `white`. If set to `custom` the Shariff  | `standard` |
| `orientation` | `vertical` will stack the buttons vertically. | `horizontal`  |
| `client` | Client JavaScript configuration. | (see below) |
| `services` | An array of service names to be enabled. | (all enabled) |
| `extra` | An array of extra buttons names to be enabled. Same as `services`, but without a server callback. | (all enabled) |
| `server` | Server backend configuration. | (see below) |

### Service / Extra
Individual service and extra button settings.

| Option | Description |
|--------|-------------|
| `name` | The service name. For `services` the name will be matched against available services: `AddThis`, `Facebook`, `Flattr`, `GooglePlus`, `LinkedIn`, `Pinterest`, `Reddit`, `StumbleUpon`, `Twitter`, `Xing` |
| `title` | The button´s title text. |
| `text` | The button text. |
| `icon` | If set an icon will be prepended the this CSS class. By default this is used with Font-Awesome. |
| `url` | The service´s base url. The the `client` param settings to contruct the query string. |

### Client
Javascript specifig settings.

| Option | Description | Default |
|--------|-------------|---------|
| `url` | The canonical (share) URL of the page to check. | `null`: page's canonical URL or `og:url` or current URL |
| `referrer` | A string that will be appended to the share url. Can be disabled using `null`. | `null` |
| `params` | Query parameter for the `services` and `extra` buttons that will be appended to OR replaced the share url. The Token pattern below are available for automatic replacement. | (not set) |

### Replacement token
For Client parameters.

| Token | Replaced with |
|-------|---------------|
| `{{URL}}` | the current url or the defined client url |
| `{{NAME}}` | the og:site_name meta OR the base URL |
| `{{TITLE}}` | the og:title meta OR content of title |
| `{{SHARE}}` | the share-text, a combination of og:title and og:site_name OR the content of title |
| `{{TEXT}}` | the og:description OR descrition meta |
| `{{IMAGE}}` | og:image, if available |

### Server
Server specific setup.

| Option | Description | Default |
|--------|-------------|---------|
| `domain` | Domain for which share counts may be requested | `null`: The Website´s domain name. |
| `guzzle` | Individual Guzzle Client configuration. | (not set) |
| services | Each available Service can have a extra variables. Currently available are `facebook` (`app_id`, `secret`) for facebook tracking and `googleplus` (`dev_key`) to use this software with your own developer key. | (not set) |