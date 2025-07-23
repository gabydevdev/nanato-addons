module.exports = {
  "pluginName": "nanato-addons",
  "mainFile": "nanato-addons.php",
  "buildDir": "build",
  "zipName": "{{name}}-{{version}}.zip",
  "excludePatterns": [
    "node_modules/",
    "vendor/",
    "composer.json",
    "composer.lock",
    "package.json",
    ".git/",
    ".*",
    "src/",
    "*.log",
    ".env*",
    "tests/",
    "*.md",
    "phpcs.xml.dist",
    "*.zip",
    "wp-release.config.js"
  ],
  "config": {
    "includeGitOps": true,
    "tagPrefix": "v",
    "branch": "main"
  },
  "hooks": {
    "preRelease": [],
    "postRelease": [],
    "preBuild": [],
    "postBuild": []
  }
};
