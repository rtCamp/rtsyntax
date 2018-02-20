const path = require('path');

module.exports = [
	{
		entry : './js/block.jsx',
		output: {
			path    : path.resolve(__dirname,'js'),
			filename: 'block.build.js',
		},
		module: {
			loaders: [
				{
					test   : /.jsx$/,
					loader : 'babel-loader',
					exclude: /node_modules/,
				},
			],
		},
	},
	{
		entry: './js/highlight_worker.jsx',
		output: {
			path : path.resolve(__dirname, 'js'),
			filename: 'highlight_worker.build.js'
		},
		module: {
			loaders: [
				{
					test: /.jsx$/,
					loader: 'babel-loader',
					exclude: /node_modules/,
				}
			]
		}
	}
];
