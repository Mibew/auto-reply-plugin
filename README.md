# Auto Reply plugin

A plugin for Mibew Messenger that provides an ability to automatically
reply a visitor in a queue.

The default text of the reply is `All our operators are currently busy, please hold. Sorry for keeping you waiting.`.
It can be altered (and translated) using the standard localization
interface. On could just navigate to "`<Mibew Base URL>`/operator/translation"
page, find the appropriate constant, and change it.

## Installation

1. Get the archive with the plugin sources. You can download it from the
[official site](https://mibew.org/plugins#mibew-auto-reply) or build the
plugin from sources.

2. Untar/unzip the plugin's archive.

3. Put files of the plugins to the `<Mibew root>/plugins`  folder.

4. (optional) Add plugins configs to "plugins" structure in
"`<Mibew root>`/configs/config.yml". If the "plugins" stucture looks like
`plugins: []` it will become:
    ```yaml
    plugins:
        "Mibew:AutoReply": # Plugin's configurations are described below
            wait_time: 180
    ```

5. Navigate to "`<Mibew Base URL>`/operator/plugin" page and enable the plugin.

## Plugin's configurations

The plugin can be configured with values in "`<Mibew root>`/configs/config.yml" file.

### config.wait_time

Type: `Integer`

Default: `60`

Specify time in seconds to wait before automatically reply.

## Build from sources

There are several actions one should do before use the latest version of the plugin from the repository:

1. Obtain a copy of the repository using `git clone`, download button, or another way.
2. Install [node.js](http://nodejs.org/) and [npm](https://www.npmjs.org/).
3. Install [Gulp](http://gulpjs.com/).
4. Install npm dependencies using `npm install`.
5. Run Gulp to build the sources using `gulp default`.

Finally `.tar.gz` and `.zip` archives of the ready-to-use Plugin will be available in `release` directory.

## License

[Apache License 2.0](http://www.apache.org/licenses/LICENSE-2.0.html)
