/** @format */

module.exports = {
	pluginName: 'nanato-addons',
	mainFile: 'nanato-addons.php',
	buildDir: 'build',
	zipName: '{{name}}-{{version}}.zip',
	excludePatterns: [
		'node_modules/', 
		'vendor/', 
		'.*', 
		'.git/', 
		'.gitignore', 
		'composer.lock', 
		'composer.json', 
		'package.json', 
		'package-lock.json', 
		'wp-release.config.js', 
		'*.md', 
		'*.scss', 
		'phpcs.xml.dist'
	],
	config: {
		includeGitOps: true,
		tagPrefix: 'v',
		branch: 'main',
	},
	hooks: {
		preRelease: [],
		postRelease: [],
		preBuild: [],
		postBuild: [],
	},
};
