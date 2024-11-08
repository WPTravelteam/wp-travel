# WP Travel - 🖥 Development Guide line.

## _⏳ Now is time to do great things._

To increase the productivity developers need to have common development setup. 

### 🖋 Editor
- [VS Code](https://code.visualstudio.com/) : Lets stick with this so that we can have common config for it.
#### 🧩 Extensions for VS Code
- [xDebug](https://github.com/xdebug/vscode-php-debug)
- [PHPCs](https://github.com/ikappas/vscode-phpcs)
- [ESLint](https://github.com/Microsoft/vscode-eslint)


### ✅ Requirements

| Package | Version |
| ----------- | ----------- |
| [Node](https://nodejs.org/en/) | `14.5.0 (Recommended) or above` |
| [Yarn](https://yarnpkg.com/) | `1.21.1 (Recommended) or above`  We will be using this to run commands. ```Don't use: npm install``` |
| [Composer](https://getcomposer.org/) | `1.6.3 (Recommended) or above` |

### ☁ Recommended
- [Local (By flywheel)](https://localwp.com/) for Local server
- [XDebug extension](https://github.com/pixeljar/local-addon-xdebug-vscode) for ```Local (By flywheel)```. Check [this for help](https://localwp.com/community/t/localbyflywheel-xdebug-vscode/11950/2).

### 🛠 Setup plugin
Before Setup the plugin you need to setup SSH [More Info](https://docs.gitlab.com/ee/user/ssh.html)
```sh
# Clone WP Travel Repo 
git clone https://github.com/WPTravelteam/wp-travel.git

# Go to wp-travel folder
cd wp-travel

# Install all required packages.
yarn install
```

### ⁉️ Issue with ```PHPCS``` in ```VS Code```.
Incase ```phpcs``` is not showing error in editor then:
- Create ```.vscode``` folder in ```wp-travel``` plugin folder.
- Create ```settings.json``` file inside ```.vscode```.
- Add following config inside ```settings.json```.

```json
{
    "phpcs.executablePath": "./vendor/bin/phpcs"
}
```

### Start development
This will start development mode to edit script to make changes in JS and CSS. 
```sh
yarn start
```

### Start production
This will Make and comple JS, CSS for production build and also make the final zip for production. Zip file is in the bundle/wp-travel-{version}.zip
```sh
yarn bundle
```
