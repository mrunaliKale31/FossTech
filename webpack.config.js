const path = require('path');
const { VueLoaderPlugin } = require('vue-loader');

module.exports = {
    entry: {
        gitintegration: './src/main.js'
    },
    output: {
        path: path.resolve(__dirname, 'js'),
        filename: '[name].js',
        chunkFilename: '[name]-[contenthash].js',
        publicPath: '/js/'
    },
    module: {
        rules: [
            {
                test: /\.vue$/,
                loader: 'vue-loader'
            },
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.css$/,
                use: ['style-loader', 'css-loader']
            }
        ]
    },
    plugins: [
        new VueLoaderPlugin()
    ],
    resolve: {
        extensions: ['.js', '.vue', '.json'],
        alias: {
            vue$: 'vue/dist/vue.runtime.esm.js'
        }
    },
    devtool: 'source-map',
    optimization: {
        splitChunks: {
            chunks: 'all'
        }
    }
};
