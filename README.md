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

- [Node](https://nodejs.org/en/)
- [Yarn](https://yarnpkg.com/) : We will be using this to run commands. ```Don't use: npm install```
- [Composer](https://getcomposer.org/)

### ☁ Recommended
- [Local (By flywheel)](https://localwp.com/) for Local server
- [XDebug extension](https://github.com/pixeljar/local-addon-xdebug-vscode) for ```Local (By flywheel)```. Check [this for help](https://localwp.com/community/t/localbyflywheel-xdebug-vscode/11950/2).

### 🛠 Setup plugin
```sh
# Clone WP Travel Repo 
git clone git@gitlab.com:ws-plugins/wp-travel.git

# Go to wp-travel folder
cd wp-travel

# Checkout to dev branch
git checkout dev

# Install all required packages.
yarn setup
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
```sh
yarn start
```
