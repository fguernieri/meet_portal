const path = require('path')
const { merge } = require('webpack-merge')
const defaultConfig = require('@nextcloud/webpack-vue-config')

module.exports = merge(defaultConfig, {
	entry: {
		'meet_portal-main': path.join(__dirname, 'src', 'main.js'),
	},
})
