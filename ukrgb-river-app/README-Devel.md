# ukrgb-river-app Developer Note

These notes are work in progress, started whilst porting my development from Linux to Mac (a.k.a. moving to the dark side :wink: )
Also part of the Joomla 5 upgrade process.
## Setup of Dev environment
It is assumed that Homebrew is being used (for mac)

This may be incomplete!

### Prerequisite (for Mac assuming you are using yarn)
jq is used to build the plugin
```
brew install node
brew install yarn
brew install jq
```
### Install  
```
yarn install
```

### Compiles and hot-reloads for development
```
yarn dev
```



### Other commands

Upgrade packages
```
yarn upgrade-interactive --latest
```