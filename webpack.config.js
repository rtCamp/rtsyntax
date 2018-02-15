module.exports = {
    entry: './blocks/syntax-highlighter/js/block.jsx',
    output: {
        path: __dirname + '/blocks/syntax-highlighter/build' ,
        filename: 'block.build.js',
    },
    module: {
        loaders: [
            {
                test: /.jsx$/,
                loader: 'babel-loader',
                exclude: /node_modules/,
            },
        ],
    },
};